<?php
session_start();
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $cedula = $_POST['cedula'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $direccion = $_POST['direccion'];
    $id_ciudad = $_POST['id_ciudad'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (empty($cedula) || empty($nombres) || empty($apellidos) || empty($telefono) || 
        empty($correo) || empty($direccion) || empty($id_ciudad) || empty($username) || empty($password)) {
        die("Todos los campos son obligatorios");
    }
    
    $password_hash = hash('sha256', $password);
    
    $stmt = $conn->prepare("INSERT INTO usuarios (cedula, nombres, apellidos, telefono, correo, direccion, id_ciudad, username, password_hash, rol, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'CLIENTE', 'ACTIVO')");
    $stmt->bind_param("ssssssiss", $cedula, $nombres, $apellidos, $telefono, $correo, $direccion, $id_ciudad, $username, $password_hash);
    
    if ($stmt->execute()) {
        $id_usuario = $stmt->insert_id;
        
        $stmt2 = $conn->prepare("INSERT INTO clientes (id_usuario) VALUES (?)");
        $stmt2->bind_param("i", $id_usuario);
        $stmt2->execute();
        $stmt2->close();
        
        $_SESSION['registro_exitoso'] = "Cuenta creada exitosamente";
        header("Location: ../.html/login.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: ../registro.html");
    exit();
}
?>