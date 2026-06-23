<?php
session_start();
header('Content-Type: application/json');
require_once 'conexion.php';

// Verificar autenticación de admin
if (!isset($_SESSION['usuario_autenticado']) || $_SESSION['usuario_rol'] != 'ADMIN') {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

$id = $_POST['id'] ?? 0;
$estado = $_POST['estado'] ?? '';

if (!$id || !$estado) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit();
}

$stmt = $conn->prepare("UPDATE solicitudes_servicio SET estado = ? WHERE id_solicitud = ?");
$stmt->bind_param("si", $estado, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>