<?php
session_start();
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;
    
    if (empty($username) || empty($password)) {
        $_SESSION['error_login'] = "Usuario y contraseña son obligatorios";
        header("Location: ../.html/login.html");
        exit();
    }
    
    $password_hash = hash('sha256', $password);
    
    $stmt = $conn->prepare("SELECT id_usuario, username, nombres, apellidos, correo, rol, estado FROM usuarios WHERE (username = ? OR correo = ?) AND password_hash = ?");
    $stmt->bind_param("sss", $username, $username, $password_hash);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $usuario = $result->fetch_assoc();
        
        if ($usuario['estado'] == 'INACTIVO') {
            $_SESSION['error_login'] = "Tu cuenta está inactiva";
            header("Location: ../.html/login.html");
            exit();
        }
        
        $_SESSION['usuario_id'] = $usuario['id_usuario'];
        $_SESSION['usuario_nombre'] = $usuario['nombres'] . ' ' . $usuario['apellidos'];
        $_SESSION['usuario_username'] = $usuario['username'];
        $_SESSION['usuario_correo'] = $usuario['correo'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
        $_SESSION['usuario_autenticado'] = true;
        
        if ($remember) {
            setcookie('remember_username', $usuario['username'], time() + (7 * 24 * 60 * 60), "/");
            
            $token = bin2hex(random_bytes(32));
            $token_hash = hash('sha256', $token);
            $expira = date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60));
            
            $stmt2 = $conn->prepare("UPDATE usuarios SET token_remember = ?, token_expira = ? WHERE id_usuario = ?");
            $stmt2->bind_param("ssi", $token_hash, $expira, $usuario['id_usuario']);
            $stmt2->execute();
            $stmt2->close();
            
            setcookie('auth_token', $token, time() + (7 * 24 * 60 * 60), "/");
        }
        
        $stmt->close();
        $conn->close();
        
        if ($usuario['rol'] == 'ADMIN') {
            header("Location: ../admin/solicitudes.php");
        } else {
            header("Location: ../.html/index.html");
        }
        exit();
    } else {
        $_SESSION['error_login'] = "Usuario o contraseña incorrectos";
        header("Location: ../.html/login.html");
        exit();
    }
} else {
    header("Location: ../.html/login.html");
    exit();
}
?>