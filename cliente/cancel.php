<?php
require_once('../stripe-php/init.php'); // Asegúrate que la librería esté en esta ruta

\Stripe\Stripe::setApiKey('TU_STRIPE_SECRET_KEY'); // Reemplaza con tu secret key

// Preparar items del carrito
$line_items = [];
foreach($cart as $item){
    $line_items[] = [
        'price_data' => [
            'currency' => 'usd', // o 'pen' si quieres soles
            'product_data' => [
                'name' => $item['nombre'],
            ],
            'unit_amount' => $item['precio'] * 100, // Stripe usa centavos
        ],
        'quantity' => $item['cantidad'],
    ];
}

// Crear sesión de checkout
$data = [
    'success_url' => 'http://localhost/Ecomarket2.0/cliente/success.php',
    'cancel_url'  => 'http://localhost/Ecomarket2.0/cliente/cancel.php',
    'payment_method_types' => ['card'],
    'mode' => 'payment',
    'line_items' => $line_items
];

$session = \Stripe\Checkout\Session::create($data);

// Redirigir a Stripe
header("Location: " . $session->url);
exit;
?>