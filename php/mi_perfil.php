<?php
require_once 'verificar_auth.php';

// Verificar que el usuario esté autenticado
if (!verificarAutenticacion()) {
    header("Location: ../login.html");
    exit();
}

$usuario = obtenerUsuarioActual();
require_once 'conexion.php';

// Obtener datos completos del usuario
$stmt = $conn->prepare("SELECT u.*, c.nombre as ciudad_nombre, c.provincia 
                        FROM usuarios u 
                        LEFT JOIN ciudades c ON u.id_ciudad = c.id_ciudad 
                        WHERE u.id_usuario = ?");
$stmt->bind_param("i", $usuario['id']);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - AquaClean</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        .perfil-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
        }
        .perfil-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .perfil-avatar {
            width: 100px;
            height: 100px;
            background: var(--color-primario);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .perfil-avatar i {
            font-size: 50px;
            color: white;
        }
        .perfil-info {
            display: grid;
            gap: 15px;
        }
        .info-row {
            display: flex;
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: 600;
            width: 150px;
            color: var(--color-texto-oscuro);
        }
        .info-value {
            color: var(--color-texto-gris);
            flex: 1;
        }
        .btn-editar {
            display: inline-block;
            padding: 10px 20px;
            background: var(--color-primario);
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            margin-top: 20px;
            text-decoration: none;
        }
        .btn-editar:hover {
            background: var(--color-secundario);
        }
    </style>
</head>
<body>

<?php include 'header_sesion.php'; ?>

<div class="perfil-container">
    <div class="perfil-header">
        <div class="perfil-avatar">
            <i class="fa-solid fa-user"></i>
        </div>
        <h2>Mi Perfil</h2>
        <p><?php echo htmlspecialchars($userData['correo']); ?></p>
    </div>

    <div class="perfil-info">
        <div class="info-row">
            <div class="info-label">Nombres:</div>
            <div class="info-value"><?php echo htmlspecialchars($userData['nombres']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Apellidos:</div>
            <div class="info-value"><?php echo htmlspecialchars($userData['apellidos']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Cédula:</div>
            <div class="info-value"><?php echo htmlspecialchars($userData['cedula']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Teléfono:</div>
            <div class="info-value"><?php echo htmlspecialchars($userData['telefono']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Dirección:</div>
            <div class="info-value"><?php echo htmlspecialchars($userData['direccion']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Ciudad:</div>
            <div class="info-value"><?php echo htmlspecialchars($userData['ciudad_nombre'] . ' - ' . $userData['provincia']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Usuario:</div>
            <div class="info-value"><?php echo htmlspecialchars($userData['username']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Rol:</div>
            <div class="info-value"><?php echo htmlspecialchars($userData['rol']); ?></div>
        </div>
    </div>

    <div style="text-align: center;">
        <a href="editar_perfil.php" class="btn-editar">
            <i class="fa-solid fa-pen"></i> Editar Perfil
        </a>
    </div>
</div>

<footer class="footer">
    <div class="footer-content container">
        <div class="link">
            <a href="../index.html" class="logo">AquaClean</a>
        </div>
        <div class="link">
            <ul>
                <li><a href="../index.html">Inicio</a></li>
                <li><a href="../servicios.html">Servicios</a></li>
                <li><a href="../contacto.html">Contacto</a></li>
            </ul>
        </div>
    </div>
    <p class="copy">&copy; 2026 AquaClean. Todos los derechos reservados.</p>
</footer>

</body>
</html>