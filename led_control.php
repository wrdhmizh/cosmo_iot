<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$ledStatus = $_SESSION['ledStatus'] ?? 'OFF';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ledStatus = ($ledStatus === 'OFF') ? 'ON' : 'OFF';
    $_SESSION['ledStatus'] = $ledStatus;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LED Control</title>
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
        .control-card {
            background: rgba(255,255,255,0.93);
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(44, 113, 88, 0.15);
            max-width: 360px;
            width: 100%;
            text-align: center;
        }
        .control-card h2 {
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
        .toggle-button {
            background: <?= $ledStatus === 'ON' ? '#c93434' : '#4ed39a' ?>;
            border: none;
            color: #fff;
            padding: 0.9rem 1.8rem;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.18s, transform 0.13s;
            box-shadow: 0 2px 10px rgba(76, 175, 80, 0.07);
        }
        .toggle-button:hover {
            transform: translateY(-2px) scale(1.04);
            opacity: 0.95;
        }
        .back-link {
            display: block;
            margin-top: 2rem;
            color: #1d4c35;
            font-weight: 500;
            font-size: 1.05rem;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="control-card">
        <h2>LED Control</h2>
        <div class="status">
            LED is currently: <b><?= htmlspecialchars($ledStatus) ?></b>
        </div>
        <form method="POST" action="">
            <button class="toggle-button" type="submit">
                <?= $ledStatus === 'ON' ? 'Turn OFF' : 'Turn ON' ?>
            </button>
        </form>
        <a class="back-link" href="dashboard.php">← Back to Main Menu</a>
    </div>
</body>
</html>
