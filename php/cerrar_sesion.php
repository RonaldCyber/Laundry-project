<?php
session_start();
require_once 'conexion.php';

if (isset($_SESSION['usuario_id'])) {
    $stmt = $conn->prepare("UPDATE usuarios SET token_remember = NULL, token_expira = NULL WHERE id_usuario = ?");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $stmt->close();
}

setcookie('remember_username', '', time() - 3600, "/");
setcookie('auth_token', '', time() - 3600, "/");

$_SESSION = array();
session_destroy();

header("Location: ../index.html");
exit();
?>