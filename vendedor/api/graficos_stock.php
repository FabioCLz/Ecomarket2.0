<?php
include("../config/db.php");
$vendedor = $_SESSION['usuario_id'] ?? 1;

$labels = [];
$valores = [];

$query = mysqli_query($conn,"SELECT nombre, stock FROM productos WHERE vendedor_id='$vendedor'");
while($row=mysqli_fetch_assoc($query)){
    $labels[] = $row['nombre'];
    $valores[] = $row['stock'];
}

echo json_encode(['labels'=>$labels,'valores'=>$valores]);