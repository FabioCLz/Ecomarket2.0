<?php
session_start();
include("../config/db.php");

// Obtener el usuario logueado
$cliente_id = $_SESSION['usuario_id'] ?? 1;

// Obtener pedido_id de la URL
$pedido_id = $_GET['pedido_id'] ?? null;

if(!$pedido_id){
    echo "Pedido no válido";
    exit;
}

// 1️⃣ Marcar pedido como pagado
$stmt = $conn->prepare("UPDATE pedidos SET estado = 'pagado' WHERE id = ? AND cliente_id = ?");
$stmt->bind_param("ii", $pedido_id, $cliente_id);
$stmt->execute();
$stmt->close();

// 2️⃣ Vaciar carrito
$conn->query("DELETE cp FROM carrito_productos cp JOIN carrito c ON cp.carrito_id = c.id WHERE c.cliente_id = $cliente_id");
$conn->query("DELETE FROM carrito WHERE cliente_id = $cliente_id");

// 3️⃣ Mostrar mensaje de éxito
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago Exitoso - Ecomarket</title>
    <link rel="stylesheet" href="../assets/css/cliente.css">
    <style>
        .success-container {
            text-align: center;
            margin: 100px auto;
            font-family: 'Poppins', sans-serif;
        }
        .success-container h1 {
            color: #28a745;
            font-size: 3rem;
        }
        .success-container p {
            font-size: 1.2rem;
        }
        .success-container a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 25px;
            background-color: #874fff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <h1>¡Pago Exitoso!</h1>
        <p>Gracias por tu compra. Tu pedido #<?php echo $pedido_id; ?> ha sido procesado correctamente.</p>
        <a href="index.php">Volver al Inicio</a>
    </div>
</body>
</html>