<?php
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if(!$data || !isset($data['producto_id'])) {
    echo json_encode(['status'=>'error','msg'=>'Datos inválidos']);
    exit;
}

$producto_id = $data['producto_id'];
$cantidad = $data['cantidad'] ?? 1;

// Inicializar carrito si no existe
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// Agregar o actualizar producto
if(isset($_SESSION['cart'][$producto_id])){
    $_SESSION['cart'][$producto_id] += $cantidad;
} else {
    $_SESSION['cart'][$producto_id] = $cantidad;
}

echo json_encode(['status'=>'ok','cart'=>$_SESSION['cart']]);