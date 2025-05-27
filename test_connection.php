<?php
try {
    $pdo = new PDO("pgsql:host=localhost;dbname=iot", "postgres", "root");
    echo "✅ Connected successfully!";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>
