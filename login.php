<?php
session_start();

// Dummy user credentials for demo
$valid_username = "admin";
$valid_password = "cafe123";

// Get input
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Check credentials
if ($username === $valid_username && $password === $valid_password) {
    $_SESSION['user'] = $username;
    header("Location: dashboard.php");
    exit();
} else {
    echo "<script>alert('Invalid username or password'); window.location.href='index.php';</script>";
}
?>
