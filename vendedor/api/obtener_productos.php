<?php

session_start();
include("../../config/db.php");

$vendedor = $_SESSION["usuario_id"];

$sql="SELECT * FROM productos WHERE vendedor_id='$vendedor' ORDER BY id DESC";

$res=mysqli_query($conn,$sql);

$datos=[];

while($row=mysqli_fetch_assoc($res)){

$datos[]=$row;

}

echo json_encode($datos);

?>