<?php
$host = 'localhost';
$dbname = 'IOT';  // Must match exactly, case-sensitive
$user = 'postgres';
$pass = 'root';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
