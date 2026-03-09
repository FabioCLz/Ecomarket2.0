<?php
session_start();
include("../config/db.php");

$cliente_id = $_SESSION['usuario_id'] ?? 1; // Para pruebas
$producto_id = $_POST['producto_id'] ?? 0;
$puntuacion = $_POST['puntuacion'] ?? 0;
$comentario = $_POST['comentario'] ?? '';

if($producto_id && $puntuacion > 0){
    $stmt = $conn->prepare("INSERT INTO calificaciones (producto_id, cliente_id, puntuacion, comentario) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $producto_id, $cliente_id, $puntuacion, $comentario);
    if($stmt->execute()){
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false, 'error'=>$stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success'=>false, 'error'=>'Datos incompletos']);
}
?>