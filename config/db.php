<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "ecomarket";

$conn = new mysqli($host,$user,$pass,$db);

if($conn->connect_error){
    die("Error conexion: " . $conn->connect_error);
}

?>