<?php
session_start();
require_once 'config.php'; // PostgreSQL connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer is installed via Composer

$message = '';

// Handle token creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_token'])) {
    $token = strtoupper(bin2hex(random_bytes(5)));
    $user_type = $_POST['user_type'] ?? 'public';
    $requested_by = $_POST['requested_by'] ?? 'anonymous';
    $expiry = $_POST['expiry'] ?? null;

    $stmt = $pdo->prepare("INSERT INTO public.signup_tokens (token, is_used, user_type, expiry, requested_by)
                           VALUES (:token, FALSE, :user_type, :expiry, :requested_by)");
    $stmt->execute([
        ':token' => $token,
        ':user_type' => $user_type,
        ':expiry' => $expiry ?: null,
        ':requested_by' => $requested_by
    ]);

    $message = "Token created: $token";
}

// Handle sending token email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_token'])) {
    $email = $_POST['email'] ?? '';
    $tokenToSend = $_POST['token'] ?? '';

    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($tokenToSend)) {
        $mail = new PHPMailer(true);

        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'dreamteams666666@gmail.com';
            $mail->Password = 'tezw hqbd mwda ftte'; // <- Your app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('dreamteams666666@gmail.com', 'Token System');
            $mail->addAddress($email);

            $mail->isHTML(false);
            $mail->Subject = 'Your Signup Token';
            $mail->Body = "Hello,\n\nYour signup token is: $tokenToSend\nPlease use this token to complete your registration.\n\nThank you.";

            $mail->send();
            $message = "Token sent successfully to $email";
        } catch (Exception $e) {
            $message = "Failed to send email: {$mail->ErrorInfo}";
        }
    } else {
        $message = "Invalid email or token.";
    }
}

// Fetch all tokens
$stmt = $pdo->query("SELECT * FROM public.signup_tokens ORDER BY created_at DESC");
$tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Token Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 2rem;
            background: #f5f9f6;
        }
        h2 {
            color: #1d4c35;
            margin-bottom: 1rem;
        }
        .new-user-btn {
            display: inline-block;
            margin-bottom: 1rem;
            padding: 0.6rem 1.2rem;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
        }
        .new-user-btn:hover {
            background-color: #2563eb;
        }
        .create-form {
            background: #fff;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            max-width: 500px;
        }
        .create-form input, .create-form select {
            width: 100%;
            padding: 0.6rem;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .create-form button {
            background: #4ed39a;
            color: #fff;
            border: none;
            padding: 0.7rem 1.4rem;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
        }
        .create-form button:hover {
            background: #34b87a;
        }
        .message {
            margin-bottom: 1rem;
            color: #167f52;
            font-weight: bold;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        }
        th, td {
            padding: 0.8rem;
            border-bottom: 1px solid #e3e3e3;
            text-align: left;
        }
        th {
            background: #e8f5ee;
            color: #146b4d;
        }
        tr:hover {
            background: #f2fdf8;
        }
        .status-used { color: #c93434; font-weight: bold; }
        .status-unused { color: #228e55; font-weight: bold; }
        .back-link {
            display: inline-block;
            margin-top: 2rem;
            text-decoration: none;
            font-weight: 500;
            color: #1d4c35;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        form.send-form {
            margin: 0;
        }
        button.send-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
        }
        button.send-btn:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <h2>Signup Token Dashboard</h2>

    <a href="signup.php" class="new-user-btn">New User Signup</a>

    <?php if (!empty($message)): ?>
        <div class="message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="create-form">
        <form method="POST">
            <label>User Type:</label>
            <select name="user_type" required>
                <option value="public">Public</option>
                <option value="private">Private</option>
            </select>

            <label>Requested By:</label>
            <input type="text" name="requested_by" placeholder="e.g. student01@example.com" required>

            <label>Expiry Date:</label>
            <input type="date" name="expiry">

            <button type="submit" name="create_token">Generate Token</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Token</th>
                <th>User Type</th>
                <th>Used</th>
                <th>Requested By</th>
                <th>Expiry</th>
                <th>Created At</th>
                <th>Send Token</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tokens as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['token'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['user_type'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="<?= $row['is_used'] ? 'status-used' : 'status-unused' ?>">
                            <?= $row['is_used'] ? 'USED' : 'NOT USED' ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($row['requested_by'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['expiry'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['created_at'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <form method="POST" class="send-form" onsubmit="return confirm('Send token to <?= htmlspecialchars($row['requested_by'], ENT_QUOTES, 'UTF-8') ?>?');">
                            <input type="hidden" name="email" value="<?= htmlspecialchars($row['requested_by'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($row['token'], ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" name="send_token" class="send-btn">Send</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="back-link">← Back to Main Menu</a>
</body>
</html>
