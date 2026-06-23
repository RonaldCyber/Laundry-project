<?php
// Incluir en todas las páginas que necesiten verificar autenticación
require_once 'verificar_auth.php';

// Verificar autenticación automáticamente
$usuario_actual = obtenerUsuarioActual();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .user-menu {
            position: relative;
            display: inline-block;
        }
        .user-name {
            cursor: pointer;
            padding: 10px 15px;
            background: var(--color-primario);
            color: white;
            border-radius: 25px;
            font-size: 14px;
        }
        .user-dropdown {
            display: none;
            position: absolute;
            right: 0;
            background: white;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            border-radius: 10px;
            z-index: 1000;
        }
        .user-menu:hover .user-dropdown {
            display: block;
        }
        .user-dropdown a {
            display: block;
            padding: 12px 16px;
            color: var(--color-texto-oscuro);
            text-decoration: none;
            border-bottom: 1px solid #eee;
        }
        .user-dropdown a:hover {
            background: var(--color-fondo-claro);
            color: var(--color-primario);
        }
        .user-dropdown .logout {
            color: #dc3545;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 20px;
        }
    </style>
</head>
<body>

<header class="header header-small">
    <div class="menu container"> 
        <a href="../index.html" class="logo">AquaClean</a>
        <input type="checkbox" id="menu" />
        <label for="menu">
            <img src="../img/menu.png" class="menu-icono" alt="menú">
        </label>
        <nav class="navbar">
            <ul>
                <li><a href="../index.html">Inicio</a></li>
                <li><a href="../servicios.html">Servicios</a></li>
                <li><a href="../contacto.html">Contacto</a></li>
                <?php if ($usuario_actual): ?>
                    <li class="user-menu">
                        <span class="user-name">
                            <i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($usuario_actual['nombre']); ?>
                        </span>
                        <div class="user-dropdown">
                            <a href="mi_perfil.php">
                                <i class="fa-solid fa-id-card"></i> Mi Perfil
                            </a>
                            <a href="mis_ordenes.php">
                                <i class="fa-solid fa-receipt"></i> Mis Órdenes
                            </a>
                            <?php if ($usuario_actual['rol'] == 'ADMIN'): ?>
                                <a href="admin/solicitudes.php">
                                    <i class="fa-solid fa-chart-line"></i> Panel Admin
                                </a>
                            <?php endif; ?>
                            <a href="cerrar_sesion.php" class="logout">
                                <i class="fa-solid fa-sign-out-alt"></i> Cerrar Sesión
                            </a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="../login.html">Iniciar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>