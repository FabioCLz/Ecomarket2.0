<?php
include("../config/db.php");

$producto_id = $_GET['producto_id'] ?? 0;

if($producto_id){
    $stmt = $conn->prepare("SELECT c.puntuacion, c.comentario, u.nombre AS cliente
                            FROM calificaciones c
                            JOIN usuarios u ON c.cliente_id = u.id
                            WHERE c.producto_id = ?
                            ORDER BY c.fecha DESC");
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reviews = [];
    while($row = $result->fetch_assoc()){
        $reviews[] = $row;
    }
    echo json_encode($reviews);
    $stmt->close();
} else {
    echo json_encode([]);
}
?>