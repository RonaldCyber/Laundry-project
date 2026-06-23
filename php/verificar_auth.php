<?php
session_start();

function verificarAutenticacion() {
    if (isset($_SESSION['usuario_autenticado']) && $_SESSION['usuario_autenticado'] === true) {
        return true;
    }
    
    if (isset($_COOKIE['auth_token'])) {
        require_once 'conexion.php';
        
        $token = $_COOKIE['auth_token'];
        $token_hash = hash('sha256', $token);
        $now = date('Y-m-d H:i:s');
        
        $stmt = $conn->prepare("SELECT id_usuario, username, nombres, apellidos, correo, rol FROM usuarios WHERE token_remember = ? AND token_expira > ? AND estado = 'ACTIVO'");
        $stmt->bind_param("ss", $token_hash, $now);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $usuario = $result->fetch_assoc();
            
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['usuario_nombre'] = $usuario['nombres'] . ' ' . $usuario['apellidos'];
            $_SESSION['usuario_username'] = $usuario['username'];
            $_SESSION['usuario_correo'] = $usuario['correo'];
            $_SESSION['usuario_rol'] = $usuario['rol'];
            $_SESSION['usuario_autenticado'] = true;
            
            $stmt->close();
            $conn->close();
            return true;
        }
        
        $stmt->close();
        $conn->close();
    }
    
    return false;
}

function obtenerUsuarioActual() {
    if (verificarAutenticacion()) {
        return [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'],
            'username' => $_SESSION['usuario_username'],
            'correo' => $_SESSION['usuario_correo'],
            'rol' => $_SESSION['usuario_rol']
        ];
    }
    return null;
}
?>