<?php
header('Content-Type: application/json');
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit();
}

$nombre = $_POST['nombre'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$email = $_POST['email'] ?? '';
$tipo_servicio = $_POST['tipo_servicio'] ?? '';
$cantidad = $_POST['cantidad'] ?? 0;
$mensaje = $_POST['mensaje'] ?? '';
$direccion = $_POST['direccion'] ?? '';

if (empty($nombre) || empty($telefono) || empty($email) || empty($tipo_servicio) || empty($cantidad) || empty($mensaje)) {
    echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']);
    exit();
}

$precios = [
    'Lavado' => 2.50,
    'Planchado' => 1.50,
    'Secado Rápido' => 1.00,
    'Tinturado' => 5.00,
    'Lavado + Planchado' => 3.50,
    'Lavado + Secado + Planchado' => 4.00
];

$precio_unitario = $precios[$tipo_servicio] ?? 2.50;
$total = $precio_unitario * $cantidad;

$sql = "CREATE TABLE IF NOT EXISTS solicitudes_servicio (
    id_solicitud INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(120) NOT NULL,
    tipo_servicio VARCHAR(100) NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    direccion TEXT,
    mensaje TEXT,
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

$stmt = $conn->prepare("INSERT INTO solicitudes_servicio (nombre, telefono, email, tipo_servicio, cantidad, precio_unitario, total, direccion, mensaje) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssdddss", $nombre, $telefono, $email, $tipo_servicio, $cantidad, $precio_unitario, $total, $direccion, $mensaje);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Solicitud enviada', 'total' => $total]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al guardar']);
}

$stmt->close();
$conn->close();
?>