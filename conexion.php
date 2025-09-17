<?php
// conexion.php - conexión PDO centralizada (configura según tu entorno)
$host = '127.0.0.1';
$db   = 'mielementos';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
  die("Error conexión BD: " . $e->getMessage());
}
?>