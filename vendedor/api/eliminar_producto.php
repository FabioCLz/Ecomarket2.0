<?php

include("../../config/db.php");

$id=$_GET["id"];

$sql="DELETE FROM productos WHERE id='$id'";

if(mysqli_query($conn,$sql)){

echo "ok";

}else{

echo mysqli_error($conn);

}

?>