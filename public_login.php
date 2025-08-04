<?php
session_start();
require_once 'config.php';

// Redirect if already logged in as public user
if (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] === 'public') {
    header('Location: public_dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = :username AND user_type = 'public' LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = 'public';
            $_SESSION['username'] = $username;
            header('Location: public_dashboard.php'); // ✅ Successful login redirect
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Public Login</title>
<style>
    html, body {
        min-height: 100vh; height: 100%; margin: 0; padding: 0;
        background: url('pic.jpeg') no-repeat center center fixed;
        background-size: cover;
        display: flex; align-items: center; justify-content: center;
        font-family: 'Segoe UI', Arial, sans-serif;
        width: 100vw; height: 100vh;
    }
    .login-card {
        background: rgba(255,255,255,0.9);
        padding: 2rem 2rem 1.5rem 2rem;
        border-radius: 18px;
        box-shadow: 0 8px 32px 0 rgba(44, 113, 88, 0.13);
        max-width: 350px; width: 100%;
        display: flex; flex-direction: column; align-items: center;
        margin: 24px;
    }
    .login-card h2 {
        margin-bottom: 1.4rem;
        color: #1d4c35;
        text-align: center;
        font-weight: 700;
        letter-spacing: 1px;
    }
    .login-card input[type="text"], .login-card input[type="password"] {
        width: 100%;
        padding: 0.65rem 0.9rem;
        margin-bottom: 1.1rem;
        border: 1.2px solid #c2ebd7;
        border-radius: 8px;
        background: #e6f8ee;
        font-size: 1rem;
        outline: none;
        box-sizing: border-box;
        transition: border 0.2s;
    }
    .login-card input[type="text"]:focus, .login-card input[type="password"]:focus {
        border-color: #60c993;
    }
    .login-card button {
        width: 100%;
        padding: 0.8rem;
        background: #4ed39a;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 1.08rem;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(76, 175, 80, 0.07);
        transition: background 0.2s;
    }
    .login-card button:hover {
        background: #34b87a;
    }
    .error-msg {
        background-color: #ffe6e6;
        color: #c93434;
        padding: 0.7rem 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        width: 100%;
        text-align: center;
        font-weight: 600;
    }
    .signup-link {
        margin-top: 1rem;
        font-weight: 600;
        color: #26845d;
        text-align: center;
    }
    .signup-link a {
        color: #1fb772;
        text-decoration: none;
    }
    .signup-link a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
    <div class="login-card">
        <h2>Public User Login</h2>

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required autofocus />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit">Login</button>
        </form>

        <div class="signup-link">
            Don't have an account? <a href="public_signup.php">Sign Up</a>
        </div>
    </div>
</body>
</html>
