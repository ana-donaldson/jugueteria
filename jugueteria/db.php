<?php
// config/db.php - Conexión a la base de datos MySQL

$host = 'localhost';
$dbname = 'jugueteria';        // El nombre de tu base de datos
$user = 'root';                // Usuario de MySQL (en XAMPP es 'root')
$password = '';                // Contraseña (en XAMPP suele estar vacía)

try {
    // Conexión usando PDO (recomendado por seguridad)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si falla la conexión, devolvemos un JSON de error
    echo json_encode(['error' => 'Error de conexión a la base de datos: ' . $e->getMessage()]);
    exit;
}
?>