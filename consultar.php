<?php
include '../../db/conexion.php';
$sql = "SELECT * FROM peliculas";
$result = $conexion->query($sql);
$peliculas = [];
while ($row = $result->fetch_assoc()) {
  $peliculas[] = $row;
}
echo json_encode($peliculas);
?>