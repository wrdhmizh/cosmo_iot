<?php
require_once 'config.php'; // Connect to DB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM public.users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Login success
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(120deg, #e9f8ef 0%, #d7f6e8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .login-card {
            background: #fff;
            padding: 2.5rem 2rem 2rem 2rem;
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(44, 113, 88, 0.09);
            max-width: 350px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 24px;
        }
        .login-card h2 {
            margin-bottom: 1.6rem;
            color: #1d4c35;
            text-align: center;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .login-card input[type="email"],
        .login-card input[type="password"] {
            width: 100%;
            padding: 0.7rem 1rem;
            margin-bottom: 1.2rem;
            border: 1.2px solid #c2ebd7;
            border-radius: 8px;
            background: #e6f8ee;
            font-size: 1rem;
            transition: border 0.2s;
            outline: none;
        }
        .login-card input[type="email"]:focus,
        .login-card input[type="password"]:focus {
            border-color: #60c993;
        }
        .login-card button {
            width: 100%;
            padding: 0.85rem;
            background: #4ed39a;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 1rem;
            margin-top: 0.5rem;
            box-shadow: 0 2px 10px rgba(76, 175, 80, 0.07);
            transition: background 0.2s;
        }
        .login-card button:hover {
            background: #34b87a;
        }
        .login-card .bottom-link {
            margin-top: 0.6rem;
            color: #222;
            font-size: 1rem;
            text-align: center;
        }
        .login-card .bottom-link a {
            color: #1fb772;
            text-decoration: none;
            font-weight: 600;
        }
        .login-card .bottom-link a:hover {
            text-decoration: underline;
        }
        .error-msg {
            background: #fff3f3;
            color: #c93434;
            border-radius: 6px;
            padding: 0.5rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid #ffd2d2;
            text-align: center;
            width: 100%;
        }
    </style>
</head>
<body>
    <form class="login-card" method="post" action="">
        <h2>Login</h2>
        <?php if (!empty($error)): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <input type="email" name="email" placeholder="Email" required autocomplete="username">
        <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
        <button type="submit">Sign In</button>
        <div class="bottom-link">
            Don't have an account? <a href="signup.php">Sign Up</a>
        </div>
    </form>
</body>
</html>
