<?php
header('Content-Type: application/json');

$response = [];

if (isset($_COOKIE['remember_username'])) {
    $response['username'] = $_COOKIE['remember_username'];
    $response['remember'] = true;
} else {
    $response['username'] = '';
    $response['remember'] = false;
}

echo json_encode($response);
?>