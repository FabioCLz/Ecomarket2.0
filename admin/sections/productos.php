<?php
include("../../config/db.php");

/* PRODUCTOS */
$sqlProductos = "SELECT 
productos.id,
productos.nombre,
productos.precio,
productos.stock,
productos.imagen,
categorias.nombre AS categoria
FROM productos
LEFT JOIN categorias ON productos.categoria_id = categorias.id";

$productos = $conn->query($sqlProductos);


/* MAS VENDIDO */
$sqlMasVendido = "SELECT 
productos.nombre,
SUM(detalle_pedido.cantidad) AS total_vendido
FROM detalle_pedido
JOIN productos ON detalle_pedido.producto_id = productos.id
GROUP BY productos.id
ORDER BY total_vendido DESC
LIMIT 1";

$masVendido = $conn->query($sqlMasVendido)->fetch_assoc();


/* MENOS VENDIDO */
$sqlMenosVendido = "SELECT 
productos.nombre,
SUM(detalle_pedido.cantidad) AS total_vendido
FROM detalle_pedido
JOIN productos ON detalle_pedido.producto_id = productos.id
GROUP BY productos.id
ORDER BY total_vendido ASC
LIMIT 1";

$menosVendido = $conn->query($sqlMenosVendido)->fetch_assoc();
?>

<div class="productos-admin">

<h2>
<i class="fa-solid fa-box"></i> Productos registrados
</h2>

<div class="estadisticas-productos">

<div class="card-est">

<h3>
<i class="fa-solid fa-arrow-trend-up"></i>
Producto más vendido
</h3>

<p>
<?php echo $masVendido['nombre'] ?? "Sin ventas registradas"; ?>
</p>

</div>

<div class="card-est">

<h3>
<i class="fa-solid fa-arrow-trend-down"></i>
Producto menos vendido
</h3>

<p>
<?php echo $menosVendido['nombre'] ?? "Sin ventas registradas"; ?>
</p>

</div>

</div>


<div class="productos-grid">

<?php while($p = $productos->fetch_assoc()){ ?>

<div class="producto-card">

<div class="producto-img">
<img src="../uploads/productos/<?php echo $p['imagen'] ?>" alt="">
</div>

<div class="producto-info">

<h3><?php echo $p['nombre'] ?></h3>

<span class="categoria">
<i class="fa-solid fa-tag"></i>
<?php echo $p['categoria'] ?>
</span>

<div class="producto-detalles">

<span class="precio">
<i class="fa-solid fa-coins"></i>
Bs <?php echo $p['precio'] ?>
</span>

<span class="stock">
<i class="fa-solid fa-layer-group"></i>
Stock: <?php echo $p['stock'] ?>
</span>

</div>

</div>

</div>

<?php } ?>

</div>

</div>