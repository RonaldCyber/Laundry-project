<?php
// Este script se puede ejecutar con cron job o al cargar la página
require_once 'conexion.php';

// Limpiar tokens expirados
$now = date('Y-m-d H:i:s');
$stmt = $conn->prepare("UPDATE usuarios SET token_remember = NULL, token_expira = NULL WHERE token_expira < ?");
$stmt->bind_param("s", $now);
$stmt->execute();
$stmt->close();
$conn->close();

echo "Cookies expiradas limpiadas correctamente";
?>