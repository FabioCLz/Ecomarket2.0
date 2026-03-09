<?php

require "../../libs/dompdf/autoload.inc.php";
include("../../config/db.php");

use Dompdf\Dompdf;

$dompdf = new Dompdf();

$sql = "
SELECT 
pedidos.id,
usuarios.nombre,
pedidos.total,
pedidos.estado,
pedidos.fecha
FROM pedidos
JOIN usuarios ON usuarios.id = pedidos.cliente_id
ORDER BY pedidos.fecha DESC
";

$res = $conn->query($sql);

$html = "

<h2 style='text-align:center;'>Reporte de Ventas EcoMarket</h2>

<table border='1' width='100%' cellspacing='0' cellpadding='8'>

<tr style='background:#2ecc71;color:white;'>

<th>ID</th>
<th>Cliente</th>
<th>Total</th>
<th>Estado</th>
<th>Fecha</th>

</tr>

";

while($row = $res->fetch_assoc()){

$html .= "

<tr>

<td>".$row['id']."</td>
<td>".$row['nombre']."</td>
<td>Bs ".$row['total']."</td>
<td>".$row['estado']."</td>
<td>".$row['fecha']."</td>

</tr>

";

}

$html .= "</table>";

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream("reporte_ecomarket.pdf");

?>