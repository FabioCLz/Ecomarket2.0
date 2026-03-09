<?php
include("../config/db.php");
$vendedor = $_SESSION['usuario_id'] ?? 1;

$labels = [];
$valores = [];

$query = mysqli_query($conn,"
    SELECT c.nombre, SUM(d.cantidad*d.precio) total
    FROM detalle_pedido d
    JOIN productos p ON d.producto_id=p.id
    JOIN categorias c ON p.categoria_id=c.id
    JOIN pedidos pd ON d.pedido_id=pd.id
    WHERE p.vendedor_id='$vendedor'
    GROUP BY c.id
");
while($row=mysqli_fetch_assoc($query)){
    $labels[] = $row['nombre'];
    $valores[] = $row['total'] ?? 0;
}

echo json_encode(['labels'=>$labels,'valores'=>$valores]);