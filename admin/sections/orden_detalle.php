<?php
include("../../config/db.php");

$id = $_GET['id'];

$sql = "SELECT productos.nombre, detalle_pedido.cantidad, detalle_pedido.precio
FROM detalle_pedido
JOIN productos ON detalle_pedido.producto_id = productos.id
WHERE detalle_pedido.pedido_id = $id";

$result = mysqli_query($conn,$sql);
?>

<div class="modal fade" id="modalOrden">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Detalle de la orden #<?php echo $id; ?></h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<?php while($row=mysqli_fetch_assoc($result)) { ?>

<div class="d-flex justify-content-between border-bottom py-2">

<span><?php echo $row['nombre']; ?></span>

<span>
<?php echo $row['cantidad']; ?> x 
Bs <?php echo $row['precio']; ?>
</span>

</div>

<?php } ?>

</div>

</div>
</div>
</div>