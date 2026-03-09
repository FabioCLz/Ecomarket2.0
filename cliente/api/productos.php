<?php
include("../../config/db.php");

$categoria = $_GET['categoria'] ?? null;

if($categoria){

$sql = "SELECT productos.*, categorias.nombre AS categoria
FROM productos
JOIN categorias ON productos.categoria_id = categorias.id
WHERE categoria_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$categoria);
$stmt->execute();
$result = $stmt->get_result();

}else{

$sql = "SELECT productos.*, categorias.nombre AS categoria
FROM productos
JOIN categorias ON productos.categoria_id = categorias.id";

$result = $conn->query($sql);

}

$data = [];

while($row = $result->fetch_assoc()){
$data[] = $row;
}

echo json_encode($data);