<?php
include("../../config/db.php");

$sql = "SELECT 
p.id AS pedido_id,
u.nombre AS cliente,
p.total,
p.estado,
p.fecha,
COUNT(dp.producto_id) AS productos
FROM pedidos p
JOIN usuarios u ON p.cliente_id = u.id
JOIN detalle_pedido dp ON dp.pedido_id = p.id
GROUP BY p.id
ORDER BY p.fecha DESC";

$result = mysqli_query($conn,$sql);

if(!$result){
die("Error SQL: ".mysqli_error($conn));
}
?>

<div class="ordenes-container">

<h3>Pedidos Recientes</h3>

<div class="ordenes-grid">

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<div class="orden-card">

<div class="orden-header">

<span class="orden-id">
Pedido #<?php echo $row['pedido_id'] ?>
</span>

<span class="estado <?php echo $row['estado'] ?>">
<?php echo $row['estado'] ?>
</span>

</div>

<div class="orden-body">

<p><strong>Cliente:</strong> <?php echo $row['cliente'] ?></p>

<p><strong>Productos:</strong> <?php echo $row['productos'] ?></p>

<p><strong>Total:</strong> $<?php echo $row['total'] ?></p>

</div>

<div class="orden-footer">

<span class="fecha">
<?php echo date("d M Y", strtotime($row['fecha'])) ?>
</span>

</div>

</div>

<?php } ?>

</div>

</div>