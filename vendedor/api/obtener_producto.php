<?php

include("../../config/db.php");

header('Content-Type: application/json');

$id = $_GET["id"];

$sql = "SELECT * FROM productos WHERE id='$id' LIMIT 1";

$res = mysqli_query($conn,$sql);

$producto = mysqli_fetch_assoc($res);

echo json_encode($producto);

?>