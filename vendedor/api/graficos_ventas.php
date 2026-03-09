<?php
include("../config/db.php");

$filtro = $_GET['filtro'] ?? 'mes';
$vendedor = $_SESSION['usuario_id'] ?? 1; // Ajusta según sesión

$labels = [];
$valores = [];

if($filtro=='dia'){
    // Ventas del día actual
    for($h=0;$h<24;$h++){
        $labels[] = "$h h";
        $res = mysqli_fetch_assoc(mysqli_query($conn,"
            SELECT SUM(d.precio*d.cantidad) total 
            FROM pedidos p
            JOIN detalle_pedido d ON p.id=d.pedido_id
            JOIN productos pr ON d.producto_id=pr.id
            WHERE pr.vendedor_id='$vendedor' AND HOUR(p.fecha)='$h'
        "));
        $valores[] = $res['total'] ?? 0;
    }
} elseif($filtro=='mes'){
    // Ventas por día del mes actual
    $dias = date('t'); // días del mes
    for($d=1;$d<=$dias;$d++){
        $labels[] = $d;
        $res = mysqli_fetch_assoc(mysqli_query($conn,"
            SELECT SUM(d.precio*d.cantidad) total 
            FROM pedidos p
            JOIN detalle_pedido d ON p.id=d.pedido_id
            JOIN productos pr ON d.producto_id=pr.id
            WHERE pr.vendedor_id='$vendedor' AND DAY(p.fecha)='$d' AND MONTH(p.fecha)=MONTH(CURDATE())
        "));
        $valores[] = $res['total'] ?? 0;
    }
} elseif($filtro=='anio'){
    // Ventas por mes del año actual
    for($m=1;$m<=12;$m++){
        $labels[] = date('F', mktime(0,0,0,$m,10));
        $res = mysqli_fetch_assoc(mysqli_query($conn,"
            SELECT SUM(d.precio*d.cantidad) total 
            FROM pedidos p
            JOIN detalle_pedido d ON p.id=d.pedido_id
            JOIN productos pr ON d.producto_id=pr.id
            WHERE pr.vendedor_id='$vendedor' AND MONTH(p.fecha)='$m' AND YEAR(p.fecha)=YEAR(CURDATE())
        "));
        $valores[] = $res['total'] ?? 0;
    }
}

echo json_encode(['labels'=>$labels,'valores'=>$valores]);