<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: http://localhost/Ecomarket2.0/cliente/index.php");
    exit;
}

$cliente_id = $_SESSION['user_id'];
$pedido_id = $_POST['pedido_id'] ?? null;
$producto_id = $_POST['producto_id'] ?? null;
$puntuacion = $_POST['puntuacion'] ?? null;
$comentario = $_POST['comentario'] ?? '';

if(!$pedido_id || !$producto_id || !$puntuacion){
    die("Datos incompletos");
}

// Insertar calificación
$stmt = $conn->prepare("INSERT INTO calificaciones (producto_id, cliente_id, puntuacion, comentario) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiis", $producto_id, $cliente_id, $puntuacion, $comentario);
$stmt->execute();
$stmt->close();

header("Location: success.php?session_id=".$_GET['session_id'] ?? '');
exit;
?>