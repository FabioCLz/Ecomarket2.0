<?php
session_start();
include("../config/db.php");

$cliente_id = $_SESSION['usuario_id'] ?? 1;
$input = json_decode(file_get_contents('php://input'), true);

if(!$input || count($input) === 0){
    http_response_code(400);
    echo json_encode(['error'=>'Carrito vacío']);
    exit;
}

// === Limpiar carrito previo ===
$conn->query("DELETE cp FROM carrito_productos cp JOIN carrito c ON cp.carrito_id = c.id WHERE c.cliente_id = $cliente_id");
$conn->query("DELETE FROM carrito WHERE cliente_id = $cliente_id");

// === Crear nuevo carrito ===
$stmt = $conn->prepare("INSERT INTO carrito (cliente_id) VALUES (?)");
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$carrito_id = $stmt->insert_id;
$stmt->close();

// === Insertar productos en carrito_productos ===
foreach($input as $item){
    $stmt = $conn->prepare("INSERT INTO carrito_productos (carrito_id, producto_id, cantidad) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $carrito_id, $item['producto_id'], $item['cantidad']);
    $stmt->execute();
    $stmt->close();
}

// === Guardar pedido en tabla pedidos ===
$total = 0;
foreach($input as $item){
    $total += $item['precio'] * $item['cantidad'];
}

$stmt = $conn->prepare("INSERT INTO pedidos (cliente_id, total, estado) VALUES (?, ?, ?)");
$estado = "pendiente";
$stmt->bind_param("ids", $cliente_id, $total, $estado);
$stmt->execute();
$pedido_id = $stmt->insert_id;
$stmt->close();

// === Insertar detalle del pedido ===
$stmt = $conn->prepare("INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)");
foreach($input as $item){
    $stmt->bind_param("iiid", $pedido_id, $item['producto_id'], $item['cantidad'], $item['precio']);
    $stmt->execute();
}
$stmt->close();

// === Crear line_items para Stripe ===
$line_items = [];
foreach($input as $item){
    $line_items[] = [
        'price_data' => [
            'currency' => 'usd',
            'product_data' => ['name' => $item['nombre']],
            'unit_amount' => intval($item['precio'] * 100),
        ],
        'quantity' => $item['cantidad'],
    ];
}

// === Stripe checkout via cURL ===
$secret_key = 'sk_test_51T7GheQfDEV1hYvCUV5Dz9Xl0D7kyxQgVAvASfYU5NHBnJcKzFqAifGhucp1sx4vAmMeVKA3WcsrsK1c3M3kjrj500dzfdCiul';

$data = [
    'success_url' => 'http://localhost/Ecomarket2.0/cliente/success.php?pedido_id='.$pedido_id,
    'cancel_url' => 'http://localhost/Ecomarket2.0/cliente/cancel.php',
    'payment_method_types[]' => 'card', // Stripe espera array
    'mode' => 'payment'
];

// Stripe requiere line_items como múltiples parámetros price_data etc.
foreach($line_items as $i => $item){
    $data["line_items[$i][price_data][currency]"] = $item['price_data']['currency'];
    $data["line_items[$i][price_data][product_data][name]"] = $item['price_data']['product_data']['name'];
    $data["line_items[$i][price_data][unit_amount]"] = $item['price_data']['unit_amount'];
    $data["line_items[$i][quantity]"] = $item['quantity'];
}

$ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$secret_key]);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
$response = curl_exec($ch);
curl_close($ch);

header('Content-Type: application/json');
echo $response;