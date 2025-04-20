<?php
// Permitir solicitudes de cualquier origen (CORS)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Si es una solicitud OPTIONS (preflight), responder con éxito
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include '../../db/conexion.php';

// Verificar que sea una solicitud POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "message" => "Método no permitido"
    ]);
    exit;
}

// Obtener y decodificar los datos JSON
$json = file_get_contents("php://input");
$data = json_decode($json);

// Verificar que se recibieron datos válidos
if (!$json || json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        "success" => false,
        "message" => "Datos JSON inválidos: " . json_last_error_msg()
    ]);
    exit;
}

// Verificar que se proporcionaron todos los datos necesarios
if (!isset($data->id) || !is_numeric($data->id) || 
    !isset($data->titulo) || !isset($data->descripcion) || !isset($data->url_imagen)) {
    echo json_encode([
        "success" => false,
        "message" => "Datos incompletos o inválidos"
    ]);
    exit;
}

$id = (int)$data->id;
$titulo = trim($data->titulo);
$descripcion = trim($data->descripcion);
$url_imagen = trim($data->url_imagen);

// Validaciones básicas
if (empty($titulo) || empty($descripcion) || empty($url_imagen)) {
    echo json_encode([
        "success" => false,
        "message" => "Todos los campos son obligatorios"
    ]);
    exit;
}

try {
    // Actualizar película
    $sql = "UPDATE peliculas SET titulo = ?, descripcion = ?, url_imagen = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
    }
    
    $stmt->bind_param("sssi", $titulo, $descripcion, $url_imagen, $id);
    $success = $stmt->execute();
    
    if (!$success) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }
    
    // Verificar si se actualizó alguna fila
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "success" => true,
            "message" => "Película actualizada correctamente"
        ]);
    } else {
        // Si no se actualizó ninguna fila, puede ser porque no se encontró el ID
        // o porque los datos son iguales a los existentes
        $sql_check = "SELECT id FROM peliculas WHERE id = ?";
        $stmt_check = $conexion->prepare($sql_check);
        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        
        if ($result->num_rows > 0) {
            echo json_encode([
                "success" => true,
                "message" => "No se realizaron cambios en la película"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No se encontró la película con el ID proporcionado"
            ]);
        }
    }
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error en el servidor: " . $e->getMessage()
    ]);
}
?>