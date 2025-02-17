<?php
//SCRIPT DE CONEXION A LA BASE DE DATOS
$servername = "localhost";
$database = "comunidad";
$username = "root";
$password = "";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: ".mysqli_connect_error());
}

//mysqli_close($conn);
?>