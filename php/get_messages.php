<?php
session_start();
header('Content-Type: application/json');

$response = [];

if (isset($_SESSION['registro_exitoso'])) {
    $response['success'] = $_SESSION['registro_exitoso'];
    unset($_SESSION['registro_exitoso']);
}

if (isset($_SESSION['error_login'])) {
    $response['error'] = $_SESSION['error_login'];
    unset($_SESSION['error_login']);
}

echo json_encode($response);
?>