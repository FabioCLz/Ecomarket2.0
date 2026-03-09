<?php
include("../config/db.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

$nombre = $_POST["nombre"];
$email = $_POST["email"];
$password = password_hash($_POST["password"], PASSWORD_DEFAULT);
$telefono = $_POST["telefono"] ?? null;
$rol = $_POST["rol"];

$foto = "default.png";

if(isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0){

$nombreFoto = time()."_".$_FILES["foto"]["name"];
$ruta = "../uploads/perfiles/".$nombreFoto;

move_uploaded_file($_FILES["foto"]["tmp_name"], $ruta);

$foto = $nombreFoto;

}

$sql = "INSERT INTO usuarios(nombre,email,password,telefono,foto_perfil,rol_id)
VALUES(?,?,?,?,?,?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi",$nombre,$email,$password,$telefono,$foto,$rol);

if($stmt->execute()){
header("Location: login.php");
exit();
}else{
$error = "Error al registrar usuario";
}

}
?>

<!DOCTYPE html>
<html lang="es>

<head>

<meta charset="UTF-8">
<title>Registro | EcoMarket</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/register.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

</head>

<body>

<div class="container registro-container">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card registro-card animate__animated animate__fadeInUp">

<div class="card-body">

<h3 class="text-center mb-4 titulo">Crear cuenta EcoMarket</h3>

<?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

<form method="POST" enctype="multipart/form-data">

<div class="text-center mb-3">

<img src="../uploads/perfiles/default.png" id="preview" class="foto-preview">

</div>

<div class="mb-3">
<label>Foto de perfil</label>
<input type="file" name="foto" class="form-control" onchange="previewImage(event)">
</div>

<div class="mb-3">
<label>Nombre completo</label>
<input type="text" name="nombre" class="form-control" required>
</div>

<div class="mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" required>
</div>

<div class="mb-3">
<label>Teléfono</label>
<input type="text" name="telefono" class="form-control">
</div>

<div class="mb-3">

<label>Contraseña</label>

<div class="input-group">

<input type="password" name="password" id="password" class="form-control" required>

<button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">

<i class="fa-solid fa-eye" id="eyeIcon"></i>

</button>

</div>

</div>

<div class="mb-3">

<label>Tipo de usuario</label>

<select name="rol" class="form-select">

<option value="3">Cliente</option>
<option value="2">Vendedor</option>

</select>

</div>

<button class="btn btn-success w-100 btn-registro">

Registrarse

</button>

<div class="text-center mt-3">

<a href="login.php">Ya tengo cuenta</a>

</div>

</form>

</div>
</div>
</div>
</div>
</div>

<script>

function togglePassword(){

const pass = document.getElementById("password");
const icon = document.getElementById("eyeIcon");

if(pass.type === "password"){
pass.type = "text";
icon.classList.replace("fa-eye","fa-eye-slash");
}else{
pass.type = "password";
icon.classList.replace("fa-eye-slash","fa-eye");
}

}

function previewImage(event){

const reader = new FileReader();

reader.onload = function(){
document.getElementById('preview').src = reader.result;
}

reader.readAsDataURL(event.target.files[0]);

}

</script>

</body>
</html>