<?php

session_start();
include("../config/db.php");

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM usuarios WHERE email='$email'";
$res = $conn->query($sql);

if($res->num_rows > 0){

$user = $res->fetch_assoc();

if(password_verify($password,$user['password'])){

$_SESSION['usuario_id'] = $user['id'];
$_SESSION['rol'] = $user['rol_id'];

if($user["rol_id"] == 1){
header("Location: ../admin/dashboard.php");
}

if($user["rol_id"] == 2){
header("Location: ../vendedor/dashboard.php");
}

if($user["rol_id"] == 3){
header("Location: ../cliente/index.php");
}

}else{

echo "Contraseña incorrecta";

}

}else{

echo "Usuario no encontrado";

}

?>