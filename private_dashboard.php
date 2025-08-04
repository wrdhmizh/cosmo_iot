<?php
session_start();

// Check if user is logged in and user_type is private
if (!isset($_SESSION['user_id'], $_SESSION['user_type']) || $_SESSION['user_type'] !== 'private') {
    header('Location: private_login.php');
    exit();
}

// Optional: you can fetch more user info from DB here if needed
$username = htmlspecialchars($_SESSION['username'] ?? 'User');

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Private Dashboard</title>
<style>
    html, body {
        min-height: 100vh; height: 100%; margin: 0; padding: 0;
        background: url('pic.jpeg') no-repeat center center fixed;
        background-size: cover;
        display: flex; align-items: center; justify-content: center;
        font-family: 'Segoe UI', Arial, sans-serif;
        width: 100vw; height: 100vh;
    }
    .dashboard-card {
        background: rgba(255,255,255,0.9);
        padding: 2.5rem 2rem 2rem 2rem;
        border-radius: 18px;
        box-shadow: 0 8px 32px 0 rgba(44, 113, 88, 0.13);
        max-width: 400px; width: 100%;
        text-align: center;
        margin: 24px;
    }
    .dashboard-card h1 {
        color: #1d4c35;
        font-weight: 700;
        margin-bottom: 1.6rem;
    }
    .dashboard-card p {
        font-size: 1.1rem;
        margin-bottom: 2rem;
        color: #26845d;
    }
    .logout-btn {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
        font-weight: 600;
        background-color: #4ed39a;
        color: white;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(76, 175, 80, 0.07);
        transition: background-color 0.2s ease-in-out;
    }
    .logout-btn:hover {
        background-color: #34b87a;
    }
</style>
</head>
<body>
    <div class="dashboard-card">
        <h1>Welcome, <?= $username ?>!</h1>
        <p>You are logged in to the Private Dashboard.</p>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>
