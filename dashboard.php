<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// This is a placeholder for real LED control
$ledStatus = $_SESSION['ledStatus'] ?? 'OFF';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'on') {
            $ledStatus = 'ON';
        } elseif ($_POST['action'] === 'off') {
            $ledStatus = 'OFF';
        }
        $_SESSION['ledStatus'] = $ledStatus;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LED Dashboard</title>
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
        .dashboard-card {
            background: rgba(255,255,255,0.93);
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(44, 113, 88, 0.15);
            max-width: 360px;
            width: 100%;
            text-align: center;
        }
        .dashboard-card h2 {
            color: #1d4c35;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }
        .status {
            margin: 1.3rem 0 1.8rem 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: #228e55;
            background: #e6f8ee;
            padding: 0.7rem 0;
            border-radius: 10px;
        }
        .dashboard-card form {
            display: flex;
            justify-content: center;
            gap: 1.2rem;
        }
        .dashboard-card button {
            background: #4ed39a;
            border: none;
            color: #fff;
            padding: 0.9rem 1.7rem;
            border-radius: 10px;
            font-size: 1.15rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.18s, transform 0.13s;
            box-shadow: 0 2px 10px rgba(76, 175, 80, 0.07);
        }
        .dashboard-card button:hover {
            background: #34b87a;
            transform: translateY(-2px) scale(1.04);
        }
        .dashboard-card .logout-link {
            display: block;
            margin-top: 2.2rem;
            color: #c93434;
            font-weight: 500;
            text-decoration: none;
            font-size: 1.05rem;
        }
        .dashboard-card .logout-link:hover {
            text-decoration: underline;
            color: #a31d1d;
        }
        .dashboard-card .sensor-link {
            display: block;
            margin-top: 1.1rem;
            background: #228e55;
            color: #fff;
            padding: 0.9rem 0;
            border-radius: 10px;
            font-size: 1.07rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 2px 10px rgba(44,113,88,0.09);
            transition: background 0.18s;
        }
        .dashboard-card .sensor-link:hover {
            background: #1d7948;
        }
    </style>
</head>
<body>
    <div class="dashboard-card">
        <h2>LED Control (Pin D)</h2>
        <div class="status">
            LED is currently: <b><?= htmlspecialchars($ledStatus) ?></b>
        </div>
        <form method="POST" action="dashboard.php">
            <button type="submit" name="action" value="on" <?= $ledStatus === 'ON' ? 'disabled style="opacity:0.6;"' : '' ?>>Turn ON</button>
            <button type="submit" name="action" value="off" <?= $ledStatus === 'OFF' ? 'disabled style="opacity:0.6;"' : '' ?>>Turn OFF</button>
        </form>
        <a class="logout-link" href="logout.php">Logout</a>
        <!-- Sensor Dashboard button -->
        <a class="sensor-link" href="sensor_dashboard.php">➔ View Sensor Dashboard</a>
    </div>
</body>
</html>
