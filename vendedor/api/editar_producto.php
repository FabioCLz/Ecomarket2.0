<?php

include("../../config/db.php");

$id=$_POST["id"];
$nombre=$_POST["nombre"];
$descripcion=$_POST["descripcion"];
$precio=$_POST["precio"];
$stock=$_POST["stock"];

$sql="SELECT imagen FROM productos WHERE id='$id'";
$res=mysqli_query($conn,$sql);
$producto=mysqli_fetch_assoc($res);

$imagen_actual=$producto["imagen"];

if(!empty($_FILES["imagen"]["name"])){

$nueva_imagen=time()."_".$_FILES["imagen"]["name"];

$tmp=$_FILES["imagen"]["tmp_name"];

move_uploaded_file($tmp,"../../assets/productos/".$nueva_imagen);

$imagen_final=$nueva_imagen;

}else{

$imagen_final=$imagen_actual;

}

$sql="UPDATE productos SET

nombre='$nombre',
descripcion='$descripcion',
precio='$precio',
stock='$stock',
imagen='$imagen_final'

WHERE id='$id'";

if(mysqli_query($conn,$sql)){

echo "ok";

}else{

echo mysqli_error($conn);

}

?>