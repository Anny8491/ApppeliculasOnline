<?php
include '../../db/conexion.php';
$data = json_decode(file_get_contents("php://input"));
$email = $data->email;
$password = $data->password;

$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
  if (password_verify($password, $user['password'])) {
    unset($user['password']);
    echo json_encode(["success" => true, "usuario" => $user]);
    exit;
  }
}
echo json_encode(["success" => false, "message" => "Credenciales inválidas"]);
?>