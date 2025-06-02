<?php
require_once 'config.php'; // Connect to DB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM public.users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Login success
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
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
        html, body {
            min-height: 100vh;
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            background: url('pic.jpeg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
            width: 100vw;
            height: 100vh;
        }
        .login-card {
            background: rgba(255,255,255,0.90);
            padding: 2.5rem 2rem 2rem 2rem;
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(44, 113, 88, 0.13);
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
        .login-card input[type="text"] {
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
        .login-card input[type="text"]:focus {
            border-color: #60c993;
        }
        /* Password wrapper and eye icon for alignment */
        .password-wrapper {
            width: 100%;
            position: relative;
            margin-bottom: 1.2rem;
        }
        .password-wrapper input[type="password"] {
            width: 100%;
            padding: 0.7rem 2.8rem 0.7rem 1rem;
            border: 1.2px solid #c2ebd7;
            border-radius: 8px;
            background: #e6f8ee;
            font-size: 1rem;
            transition: border 0.2s;
            outline: none;
            box-sizing: border-box;
        }
        .password-wrapper input[type="password"]:focus {
            border-color: #60c993;
        }
        .eye-icon {
            position: absolute;
            right: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            cursor: pointer;
            fill: #5ec699;
            opacity: 0.8;
            transition: fill 0.18s, opacity 0.18s;
        }
        .eye-icon:hover {
            opacity: 1;
            fill: #26845d;
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
        <input type="text" name="username" placeholder="Username" required autocomplete="username">
        <div class="password-wrapper">
            <input type="password" name="password" id="passwordInput" placeholder="Password" required autocomplete="current-password">
            <!-- Eye icon SVG -->
            <svg class="eye-icon" id="togglePassword" viewBox="0 0 24 24">
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12zm11 5c3.314 0 6-2.686 6-6s-2.686-6-6-6-6 2.686-6 6 2.686 6 6 6zm0-10a4 4 0 100 8 4 4 0 000-8z"/>
            </svg>
        </div>
        <button type="submit">Sign In</button>
        <div class="bottom-link">
            Don't have an account? <a href="signup.php">Sign Up</a>
        </div>
    </form>
    <script>
        // Show/hide password logic
        const passwordInput = document.getElementById('passwordInput');
        const togglePassword = document.getElementById('togglePassword');
        let passwordVisible = false;

        togglePassword.addEventListener('click', function () {
            passwordVisible = !passwordVisible;
            if (passwordVisible) {
                passwordInput.type = 'text';
                togglePassword.style.fill = '#26845d';
            } else {
                passwordInput.type = 'password';
                togglePassword.style.fill = '#5ec699';
            }
        });
    </script>
</body>
</html>
