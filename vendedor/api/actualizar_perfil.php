<?php
session_start();
include("../../config/db.php");

header('Content-Type: application/json');

$usuario_id = $_POST['usuario_id'];
$nombre = $_POST['nombre'] ?? '';
$email = $_POST['email'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$latitud = $_POST['latitud'] ?? '';
$longitud = $_POST['longitud'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$ciudad = $_POST['ciudad'] ?? '';

$foto = '';
if(isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['name'] != ''){
    $ext = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
    $foto = time()."_perfil.".$ext;
    move_uploaded_file($_FILES['foto_perfil']['tmp_name'], "../../uploads/perfiles/".$foto);
}

// Actualizar tabla usuarios
$update = "UPDATE usuarios SET nombre='$nombre', email='$email', telefono='$telefono'";
if($foto) $update .= ", foto_perfil='$foto'";
$update .= " WHERE id=$usuario_id";
$conn->query($update);

// Actualizar tabla direcciones
$res = $conn->query("SELECT id FROM direcciones WHERE usuario_id=$usuario_id");
if($res->num_rows>0){
    $conn->query("UPDATE direcciones SET latitud=$latitud, longitud=$longitud, direccion='".$conn->real_escape_string($direccion)."', ciudad='".$conn->real_escape_string($ciudad)."' WHERE usuario_id=$usuario_id");
}else{
    $conn->query("INSERT INTO direcciones(usuario_id, latitud, longitud, direccion, ciudad) VALUES ($usuario_id, $latitud, $longitud, '".$conn->real_escape_string($direccion)."', '".$conn->real_escape_string($ciudad)."')");
}

echo json_encode(['success'=>true]);
?>