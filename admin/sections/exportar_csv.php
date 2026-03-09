<?php
include("../../config/db.php");

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename=reporte_ventas.csv');

$output = fopen("php://output", "w");

fputcsv($output, ["ID Pedido","Cliente","Total","Estado","Fecha"]);

$sql = "
SELECT pedidos.id, usuarios.nombre, pedidos.total, pedidos.estado, pedidos.fecha
FROM pedidos
JOIN usuarios ON usuarios.id = pedidos.cliente_id
";

$res = $conn->query($sql);

while($row = $res->fetch_assoc()){
fputcsv($output,$row);
}

fclose($output);
?>