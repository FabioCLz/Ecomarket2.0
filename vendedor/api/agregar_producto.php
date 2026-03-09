<?php
session_start();
include("../../config/db.php");

$vendedor = $_SESSION["usuario_id"];

$nombre = $_POST["nombre"];
$descripcion = $_POST["descripcion"];
$precio = $_POST["precio"];
$stock = $_POST["stock"];
$categoria = $_POST["categoria_id"]; // NUEVO

$imagen = $_FILES["imagen"]["name"];
$tmp = $_FILES["imagen"]["tmp_name"];

move_uploaded_file($tmp, "../../assets/productos/".$imagen);

mysqli_query($conn, "
    INSERT INTO productos
    (vendedor_id, categoria_id, nombre, descripcion, precio, stock, imagen)
    VALUES
    ('$vendedor', '$categoria', '$nombre', '$descripcion', '$precio', '$stock', '$imagen')
");

header("Location: ../dashboard.php");