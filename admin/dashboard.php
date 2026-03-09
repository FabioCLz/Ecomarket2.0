<?php
include("../config/db.php");
include("../includes/auth.php");

$id = $_SESSION['usuario_id'];

$sql = "SELECT nombre,foto_perfil FROM usuarios WHERE id = $id";
$res = $conn->query($sql);
$user = $res->fetch_assoc();

$nombre = $user['nombre'];
$foto = $user['foto_perfil'];
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard EcoMarket</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/dashboard.css">

</head>

<body>

<div class="admin-dashboard">

<!-- SIDEBAR -->
<div class="ds-left-menu">

<button class="btn-menu">
<i class="fa-solid fa-circle-chevron-left"></i>
</button>

<div class="ds-perfil">

<div class="foto">
<img src="../uploads/perfiles/<?php echo $foto ?>" alt="">
</div>

<div class="info-perfil">
<span>Admin</span>
<h3><?php echo $nombre ?></h3>
</div>

</div>

<div class="ds-menu">

<ul>

<li>
<a href="sections/home.php">
<i class="fa-solid fa-home"></i>
<span>Home</span>
</a>
</li>

<li>
<a href="sections/productos.php">
<i class="fa-solid fa-truck-fast"></i>
<span>Productos</span>
</a>
</li>



<li>
<a href="sections/ordenes.php">
<i class="fa-solid fa-basket-shopping"></i>
<span>Ordenes</span>
</a>
</li>

<li>
<a href="sections/reportes.php">
<i class="fa-solid fa-clipboard-list"></i>
<span>Reportes</span>
</a>
</li>

<li>
<a href="sections/perfil.php">
<i class="fa-solid fa-sliders"></i>
<span>Ajustes</span>
</a>
</li>

</ul>

</div>

<div class="sign-off">

<a href="../auth/logout.php" class="btn-sign-off">

<i class="fa-solid fa-arrow-right-to-bracket"></i>
<span>Cerrar Sesión</span>

</a>

</div>

</div>


<!-- PANEL DERECHO -->
<div class="ds-panel">

<div class="panel-header">

<div class="icono">
<i class="fa-solid fa-bag-shopping"></i>
</div>

<h2>Dashboard</h2>

</div>

<!-- CONTENIDO DINAMICO -->
<div class="ds-panel-content" id="contenido">



</div>

</div>

</div>


<!-- LIBRERIAS -->
<script src="https://kit.fontawesome.com/075922b03a.js"></script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="../assets/js/app.js"></script>

</body>
</html>