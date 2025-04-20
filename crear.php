<?php
include '../../db/conexion.php';
$data = json_decode(file_get_contents("php://input"));
$nombre = $data->nombre;
$email = $data->email;
$password = password_hash($data->password, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sss", $nombre, $email, $password);
$success = $stmt->execute();

echo json_encode(["success" => $success]);
?>