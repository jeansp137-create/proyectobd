<?php
// Configuración de conexión a PostgreSQL
$host = 'localhost';
$port = 5432;
$database = 'proyectobd';
$user = 'postgres';
$password = '0192';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$database";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa a PostgreSQL";
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
