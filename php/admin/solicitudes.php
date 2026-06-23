<?php
session_start();
require_once '../php/conexion.php';

if (!isset($_SESSION['usuario_autenticado']) || $_SESSION['usuario_rol'] != 'ADMIN') {
    header("Location: ../login.html");
    exit();
}

$sql = "SELECT * FROM solicitudes_servicio ORDER BY fecha_solicitud DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes - Admin AquaClean</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        h1 {
            color: var(--color-texto-oscuro);
            margin-bottom: 30px;
        }
        .solicitudes-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .solicitudes-table th {
            background: var(--color-primario);
            color: white;
            padding: 12px;
            text-align: left;
        }
        .solicitudes-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .logout-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 10px;
        }
        .logout-btn:hover {
            background: #c82333;
        }
    </style>
</head>
<body>

<div class="admin-container">
    <a href="../php/cerrar_sesion.php" class="logout-btn">Cerrar Sesión</a>
    <h1>Solicitudes de Servicio</h1>
    
    <table class="solicitudes-table">
        <thead>
            <tr><th>ID</th><th>Fecha</th><th>Cliente</th><th>Teléfono</th><th>Servicio</th><th>Cantidad</th><th>Total</th><th>Mensaje</th></tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id_solicitud']; ?></td>
                <td><?php echo $row['fecha_solicitud']; ?></td>
                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                <td><?php echo $row['telefono']; ?></td>
                <td><?php echo $row['tipo_servicio']; ?></td>
                <td><?php echo $row['cantidad']; ?></td>
                <td>$<?php echo number_format($row['total'], 2); ?></td>
                <td><?php echo htmlspecialchars(substr($row['mensaje'], 0, 50)); ?>...</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>