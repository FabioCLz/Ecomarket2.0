<?php
session_start();
include("../config/db.php");

if(isset($_POST["login"])){

$email = $_POST["email"];
$password = $_POST["password"];

$sql = "SELECT * FROM usuarios WHERE email='$email'";
$result = mysqli_query($conn,$sql);

if(mysqli_num_rows($result) > 0){

$user = mysqli_fetch_assoc($result);

if(password_verify($password,$user["password"])){

$_SESSION["usuario"] = $user["nombre"];
$_SESSION["rol"] = $user["rol_id"];
$_SESSION["usuario_id"] = $user["id"];
$_SESSION["foto"] = $user["foto_perfil"];

if($user["rol_id"] == 1){
header("Location: ../admin/dashboard.php");
exit();
}

if($user["rol_id"] == 2){
header("Location: ../vendedor/dashboard.php");
exit();
}

if($user["rol_id"] == 3){
header("Location: ../cliente/index.php");
exit();
}

}else{
$error = "Contraseña incorrecta";
}

}else{
$error = "Usuario no encontrado";
}

}
?>

<!DOCTYPE html>
<html>
<head>

<title>Login EcoMarket</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<link rel="stylesheet" href="../assets/css/login.css">

</head>

<body>

<div class="login-wrapper">

<div class="login-card">

<h2 class="title">EcoMarket</h2>
<p class="subtitle">Iniciar sesión</p>

<?php if(isset($error)){ ?>
<div class="alert alert-danger">
<?php echo $error; ?>
</div>
<?php } ?>

<form method="POST">

<div class="mb-3">

<label>Email</label>
<input type="email" name="email" class="form-control" required>

</div>

<div class="mb-3 position-relative">

<label>Contraseña</label>
<input type="password" id="password" name="password" class="form-control" required>

<i class="bi bi-eye toggle-password" onclick="togglePassword()"></i>

</div>

<button name="login" class="btn btn-success w-100">
Ingresar
</button>

<div class="text-center mt-3">

<a href="register.php">Crear cuenta</a>

</div>

</form>

</div>

</div>

<script>

function togglePassword(){

let input = document.getElementById("password");

if(input.type === "password"){
input.type = "text";
}else{
input.type = "password";
}

}

</script>

</body>
</html>