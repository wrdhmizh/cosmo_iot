<?php
$host = 'localhost';
$port = '5433';  // Default PostgreSQL port
$dbname = 'IOT';  // Must match your database name
$user = 'postgres';
$pass = 'root';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
