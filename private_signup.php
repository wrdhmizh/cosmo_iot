<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $signup_token = trim($_POST['signup_token'] ?? '');

    if ($username === '' || $password === '' || $signup_token === '') {
        echo "<script>alert('Please fill in all fields.'); window.location.href='private_signup.php';</script>";
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM public.signup_tokens WHERE token = :token AND is_used = FALSE AND user_type = 'private' AND (expiry IS NULL OR expiry >= CURRENT_DATE) LIMIT 1");
    $stmt->execute([':token' => $signup_token]);
    $token_row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$token_row) {
        echo "<script>alert('Invalid, used or expired PRIVATE token.'); window.location.href='private_signup.php';</script>";
        exit();
    }

    $stmt = $pdo->prepare("SELECT id FROM public.users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    if ($stmt->fetch()) {
        echo "<script>alert('Username already exists.'); window.location.href='private_signup.php';</script>";
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO public.users (username, password) VALUES (:username, :password)");
    $stmt->execute([':username' => $username, ':password' => $hashedPassword]);

    $stmt = $pdo->prepare("UPDATE public.signup_tokens SET is_used = TRUE WHERE token = :token");
    $stmt->execute([':token' => $signup_token]);

    echo "<script>alert('Private signup successful. You can now login.'); window.location.href='login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Private Signup</title>
    <style>
        html, body { margin: 0; padding: 0; height: 100vh; font-family: Arial, sans-serif; }
        body {
            background: url('pic.jpeg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .signup-card {
            background: rgba(255,255,255,0.95);
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            width: 320px;
        }
        .signup-card h2 { color: #1d4c35; margin-bottom: 1rem; text-align: center; }
        .signup-card input {
            width: 100%; padding: 0.6rem; margin-bottom: 1rem;
            border: 1px solid #c2ebd7; border-radius: 8px; background: #e6f8ee;
        }
        .signup-card button {
            width: 100%; padding: 0.7rem;
            background: #4ed39a; border: none; color: white;
            font-weight: bold; border-radius: 8px;
            cursor: pointer;
        }
        .signup-card button:hover { background: #34b87a; }
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4); display: flex; justify-content: center; align-items: center; z-index: 999;
        }
        .modal {
            background: white; padding: 2rem; border-radius: 12px; width: 300px;
            text-align: center; box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
        }
        .modal input {
            width: 80%; padding: 0.6rem; margin-top: 1rem; border: 1px solid #ccc; border-radius: 8px;
        }
        .modal button {
            margin-top: 1rem; padding: 0.5rem 1.5rem; border: none; background: #4ed39a; color: white; border-radius: 8px;
            font-weight: bold; cursor: pointer;
        }
        .modal .request-token {
            margin-top: 0.8rem; font-size: 0.9rem;
        }
        .modal .request-token a { color: #167f52; text-decoration: underline; }
    </style>
</head>
<body>

<div id="modalOverlay" class="modal-overlay">
    <div class="modal">
        <h3>Enter Signup Token</h3>
        <div id="tokenError" style="color:red; font-size: 0.9rem;"></div>
        <input type="text" id="tokenInput" placeholder="Enter token">
        <br>
        <button onclick="checkToken()">Submit</button>
        <div class="request-token">
            Need one? <a href="mailto:dreamteams666666@gmail.com?subject=Request%20Private%20Signup%20Token&body=Hi,%20I%20would%20like%20to%20request%20a%20private%20signup%20token." target="_blank">Request Token via Email</a>
        </div>
    </div>
</div>

<form class="signup-card" method="POST" style="filter: blur(2px); pointer-events: none;">
    <h2>Private Signup</h2>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="hidden" name="signup_token" id="signupTokenField">
    <button type="submit">Sign Up</button>
</form>

<script>
    function checkToken() {
        const token = document.getElementById('tokenInput').value.trim();
        const error = document.getElementById('tokenError');
        if (token.length < 3) {
            error.textContent = "Token too short.";
            return;
        }
        document.getElementById('signupTokenField').value = token;
        document.getElementById('modalOverlay').style.display = 'none';
        const form = document.querySelector('form');
        form.style.filter = 'none';
        form.style.pointerEvents = 'auto';
    }

    document.getElementById('tokenInput').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') checkToken();
    });
</script>

</body>
</html>
