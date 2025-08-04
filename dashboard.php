<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Main Menu</title>
    <style>
        body {
            min-height: 100vh;
            background: url('pic.jpeg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu-card {
            background: rgba(255,255,255,0.93);
            padding: 3rem 2.5rem 2.5rem 2.5rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(44, 113, 88, 0.15);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .menu-card h2 {
            color: #1d4c35;
            font-size: 2rem;
            margin-bottom: 2rem;
            font-weight: 700;
        }

        .menu-card a {
            display: block;
            background: #4ed39a;
            color: #fff;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 10px;
            font-size: 1.15rem;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 2px 10px rgba(76, 175, 80, 0.07);
            transition: background 0.18s ease-in-out;
        }

        .menu-card a:hover {
            background: #34b87a;
        }

        .logout-link {
            display: inline-block;
            margin-top: 2rem;
            color: #c93434;
            font-weight: 500;
            text-decoration: none;
            font-size: 1.05rem;
        }

        .logout-link:hover {
            text-decoration: underline;
            color: #a31d1d;
        }
    </style>
</head>
<body>
    <div class="menu-card">
        <h2>Main Menu</h2>
        <a href="led_control.php">💡 LED Control</a>
        <a href="sensor_dashboard.php">📊 Sensor Dashboard</a>
        <a class="logout-link" href="logout.php">Logout</a>
    </div>
</body>
</html>
