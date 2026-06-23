<?php
echo "<h2>🔧 Prueba de conexión - AquaClean</h2>";

$host = "localhost";
$user = "root";
$pass = "";
$db = "aquaclean_db";

echo "<p>📡 Conectando a MySQL en $host...</p>";

$conn = @new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo "<p style='color:red'>❌ ERROR: " . $conn->connect_error . "</p>";
    echo "<hr><h3>📋 Soluciones:</h3>";
    echo "<ul>";
    echo "<li>✅ ¿XAMPP está corriendo? (Apache y MySQL deben estar VERDES)</li>";
    echo "<li>✅ ¿La base de datos 'aquaclean_db' existe en phpMyAdmin?</li>";
    echo "<li>✅ ¿Importaste el archivo SQL?</li>";
    echo "<li>✅ Usuario: '$user' | Contraseña: '" . ($pass ? '****' : 'vacía') . "'</li>";
    echo "</ul>";
} else {
    echo "<p style='color:green'>✅ Conexión exitosa a MySQL</p>";
    echo "<p>📚 Base de datos: <strong>$db</strong></p>";
    
    $result = $conn->query("SHOW TABLES");
    echo "<p>📋 Tablas encontradas: <strong>" . $result->num_rows . "</strong></p>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
    $row = $result->fetch_assoc();
    echo "<p>👥 Usuarios registrados: <strong>" . $row['total'] . "</strong></p>";
    
    $conn->close();
    echo "<hr><p>✅ Todo funciona correctamente. Puedes ir al <a href='login.html'>Login</a></p>";
}
?>