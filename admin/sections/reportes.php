<?php
include("../../config/db.php");

$periodo = $_GET['periodo'] ?? "mes";

switch($periodo){

case "dia":
$filtro = "DATE(fecha) = CURDATE()";
break;

case "semana":
$filtro = "YEARWEEK(fecha,1) = YEARWEEK(CURDATE(),1)";
break;

case "mes":
$filtro = "MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha)=YEAR(CURDATE())";
break;

case "anio":
$filtro = "YEAR(fecha)=YEAR(CURDATE())";
break;

default:
$filtro = "MONTH(fecha) = MONTH(CURDATE())";
}


/* VENTAS */
$sqlVentas = "
SELECT DATE(fecha) as fecha, SUM(total) as ventas
FROM pedidos
WHERE $filtro
GROUP BY DATE(fecha)
";

$resVentas = $conn->query($sqlVentas);

$labels = [];
$dataVentas = [];

while($row = $resVentas->fetch_assoc()){
$labels[] = $row['fecha'];
$dataVentas[] = $row['ventas'];
}


/* PRODUCTOS MAS VENDIDOS */

$sqlProductos = "
SELECT productos.nombre, SUM(detalle_pedido.cantidad) as vendidos
FROM detalle_pedido
JOIN productos ON productos.id = detalle_pedido.producto_id
JOIN pedidos ON pedidos.id = detalle_pedido.pedido_id
WHERE $filtro
GROUP BY productos.id
ORDER BY vendidos DESC
LIMIT 5
";

$resProd = $conn->query($sqlProductos);

$labelsProd = [];
$dataProd = [];

while($row = $resProd->fetch_assoc()){
$labelsProd[] = $row['nombre'];
$dataProd[] = $row['vendidos'];
}

?>

<div class="reportes-admin">

<h2>
<i class="fa-solid fa-chart-column"></i>
Reportes
</h2>


<div class="filtros-reportes">

<a href="?section=reportes&periodo=dia" class="btn-report">
<i class="fa-solid fa-calendar-day"></i> Día
</a>

<a href="?section=reportes&periodo=semana" class="btn-report">
<i class="fa-solid fa-calendar-week"></i> Semana
</a>

<a href="?section=reportes&periodo=mes" class="btn-report">
<i class="fa-solid fa-calendar"></i> Mes
</a>

<a href="?section=reportes&periodo=anio" class="btn-report">
<i class="fa-solid fa-calendar-days"></i> Año
</a>


<a href="sections/exportar_csv.php" class="btn-export">
<i class="fa-solid fa-file-excel"></i> Exportar CSV
</a>

<a href="sections/exportar_pdf.php" class="btn-export">
<i class="fa-solid fa-file-pdf"></i> Exportar PDF
</a>

</div>


<div class="graficas-reportes">

<div class="grafica">

<h3>
<i class="fa-solid fa-coins"></i>
Ventas
</h3>

<canvas id="ventasChart"></canvas>

</div>


<div class="grafica">

<h3>
<i class="fa-solid fa-fire"></i>
Productos más vendidos
</h3>

<canvas id="productosChart"></canvas>

</div>

</div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

new Chart(document.getElementById("ventasChart"), {

type:'line',

data:{
labels: <?php echo json_encode($labels); ?>,
datasets:[{
label:'Ventas Bs',
data: <?php echo json_encode($dataVentas); ?>,
borderWidth:3,
tension:0.4
}]
}

});


new Chart(document.getElementById("productosChart"), {

type:'bar',

data:{
labels: <?php echo json_encode($labelsProd); ?>,
datasets:[{
label:'Vendidos',
data: <?php echo json_encode($dataProd); ?>,
borderWidth:2
}]
}

});

</script>