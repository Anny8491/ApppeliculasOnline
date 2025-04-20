<?php
// Configuración de la conexión a la base de datos
$host = "localhost"; 
$usuario = "root";   
$password = "";      
$db = "peliculas_db";

// Crear conexión
$conexion = new mysqli($host, $usuario, $password, $db);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Configurar caracteres UTF-8
$conexion->set_charset("utf8");
?>