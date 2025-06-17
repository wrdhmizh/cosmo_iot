<?php
session_start();
require_once 'config.php'; // Connect to DB

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $signup_token = trim($_POST['signup_token'] ?? '');

    if ($username === '' || $password === '' || $signup_token === '') {
        echo "<script>alert('Please fill in all fields.'); window.location.href='signup.php';</script>";
        exit();
    }

    // --- Token validation with expiry check + debug ---
    $stmt = $pdo->prepare("SELECT * FROM public.signup_tokens WHERE token = :token AND is_used = FALSE AND (expiry IS NULL OR expiry >= CURRENT_DATE) LIMIT 1");
    $stmt->execute([':token' => $signup_token]);
    $token_row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$token_row) {
        // Debug block
        $stmt = $pdo->prepare("SELECT * FROM public.signup_tokens WHERE token = :token");
        $stmt->execute([':token' => $signup_token]);
        $debug_token = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$debug_token) {
            die("<script>alert('Token does not exist in database. Check for spaces or typos!'); window.location.href='signup.php';</script>");
        } else {
            $used = $debug_token['is_used'] ? 'true' : 'false';
            $expiry = $debug_token['expiry'];
            die("<script>alert('Token found, but not valid. is_used: $used, expiry: $expiry. If used=true or expired, generate a new token.'); window.location.href='signup.php';</script>");
        }
    }
    // --- End token validation ---

    // Check if user already exists in the database
    $stmt = $pdo->prepare("SELECT id FROM public.users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    if ($stmt->fetch()) {
        echo "<script>alert('Username already exists.'); window.location.href='signup.php';</script>";
        exit();
    }

    // Save new user in the database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO public.users (username, password) VALUES (:username, :password)");
    $stmt->execute([':username' => $username, ':password' => $hashedPassword]);

    // Mark token as used
    $stmt = $pdo->prepare("UPDATE public.signup_tokens SET is_used = TRUE WHERE token = :token");
    $stmt->execute([':token' => $signup_token]);

    echo "<script>alert('Signup successful. You can now login.'); window.location.href='login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <style>
        html, body { min-height: 100vh; height: 100%; margin: 0; padding: 0;}
        body {
            background: url('pic.jpeg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
            width: 100vw; height: 100vh;
        }
        .signup-card {
            background: rgba(255,255,255,0.93);
            padding: 2rem 2rem 1.5rem 2rem;
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(44, 113, 88, 0.13);
            max-width: 350px; width: 100%;
            display: flex; flex-direction: column; align-items: center;
            margin: 24px;
        }
        .signup-card h2 { margin-bottom: 1.4rem; color: #1d4c35; text-align: center; font-weight: 700; letter-spacing: 1px;}
        .signup-card label { display: block; margin-bottom: 0.2rem; color: #26845d;}
        .signup-card input[type="text"], .signup-card input[type="password"] {
            width: 100%; padding: 0.65rem 0.9rem; margin-bottom: 1.1rem;
            border: 1.2px solid #c2ebd7; border-radius: 8px; background: #e6f8ee;
            font-size: 1rem; transition: border 0.2s; outline: none; box-sizing: border-box;
        }
        .signup-card input[type="text"]:focus, .signup-card input[type="password"]:focus { border-color: #60c993;}
        .password-wrapper { width: 100%; position: relative; margin-bottom: 1.1rem;}
        .password-wrapper input[type="password"] { padding-right: 2.8rem;}
        .eye-icon { position: absolute; right: 0.8rem; top: 50%; transform: translateY(-50%);
            width: 22px; height: 22px; cursor: pointer; fill: #5ec699; opacity: 0.8; transition: fill 0.18s, opacity 0.18s;}
        .eye-icon:hover { opacity: 1; fill: #26845d;}
        .signup-card button {
            width: 100%; padding: 0.8rem; background: #4ed39a; color: #fff; border: none; border-radius: 8px;
            font-size: 1.08rem; font-weight: 600; cursor: pointer; box-shadow: 0 2px 10px rgba(76, 175, 80, 0.07); transition: background 0.2s;
        }
        .signup-card button:hover { background: #34b87a;}
        /* Modal styles */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(55, 90, 70, 0.20); display: flex; align-items: center; justify-content: center; z-index: 10;
        }
        .modal {
            background: #fff; border-radius: 16px; box-shadow: 0 8px 40px 0 rgba(44, 113, 88, 0.16);
            padding: 2rem 2.5rem 1.5rem 2.5rem; max-width: 340px; width: 100%; text-align: center;
            animation: modalPop 0.35s cubic-bezier(0.23, 1, 0.32, 1);
        }
        @keyframes modalPop { 0% { transform: scale(0.6); opacity: 0;} 100% { transform: scale(1); opacity: 1;}}
        .modal h3 { margin-bottom: 1rem; color: #26845d; font-weight: 600;}
        .modal input[type="text"] { width: 80%; padding: 0.6rem; margin: 0.7rem 0 1.2rem 0; border: 1.2px solid #bcebd7; border-radius: 7px; background: #e6f8ee; font-size: 1rem;}
        .modal button { padding: 0.6rem 1.8rem; background: #4ed39a; color: #fff; border: none; border-radius: 8px; font-size: 1.05rem; font-weight: 600; cursor: pointer; transition: background 0.18s; margin-bottom: 0.5rem;}
        .modal button:hover { background: #34b87a;}
        .modal .error-msg { color: #c93434; font-size: 0.97rem; margin-bottom: 0.2rem; min-height: 1.2em;}
        .modal .request-token { color: #269872; margin-top: 0.6rem; font-size: 0.97rem;}
        .modal .request-token a { color: #167f52; text-decoration: underline; font-weight: 500;}
        .modal .request-token a:hover { color: #12b85c;}
        @media (max-width: 480px) { .modal, .signup-card { padding: 1.2rem; }}
    </style>
</head>
<body>
    <!-- Modal for token -->
    <div id="modalOverlay" class="modal-overlay">
        <div class="modal">
            <h3>Enter Signup Token</h3>
            <div class="error-msg" id="tokenError"></div>
            <input type="text" id="tokenInput" placeholder="Enter token" autofocus autocomplete="off">
            <br>
            <button type="button" onclick="checkToken()">Submit</button>
            <div class="request-token">
                Don’t have a token? <a href="https://wa.me/6738129566?text=Hello%20admin%2C%20I%20would%20like%20to%20request%20a%20signup%20token." target="_blank">Request one on WhatsApp</a>.
            </div>
        </div>
    </div>

    <form class="signup-card" method="POST" action="signup.php" id="signupForm" style="filter: blur(2px); pointer-events: none;">
        <h2>Signup</h2>
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Password:</label>
        <div class="password-wrapper">
            <input type="password" name="password" id="passwordInput" required>
            <!-- Eye icon SVG -->
            <svg class="eye-icon" id="togglePassword" viewBox="0 0 24 24">
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12zm11 5c3.314 0 6-2.686 6-6s-2.686-6-6-6-6 2.686-6 6 2.686 6 6 6zm0-10a4 4 0 100 8 4 4 0 000-8z"/>
            </svg>
        </div>
        <!-- Hidden token field to carry token from modal to PHP POST -->
        <input type="hidden" name="signup_token" id="signupTokenField" required>
        <button type="submit">Signup</button>
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

        // Modal logic
        window.onload = function() {
            document.getElementById('modalOverlay').style.display = 'flex';
            document.getElementById('signupForm').style.filter = 'blur(2px)';
            document.getElementById('signupForm').style.pointerEvents = 'none';
            document.getElementById('tokenInput').focus();
        };

        function checkToken() {
            const tokenInput = document.getElementById('tokenInput').value.trim();
            const errorDiv = document.getElementById('tokenError');
            if (tokenInput.length < 3) {
                errorDiv.textContent = "Please enter a valid token.";
                return;
            }
            // Set the hidden field and enable form
            document.getElementById('signupTokenField').value = tokenInput;
            document.getElementById('modalOverlay').style.display = 'none';
            document.getElementById('signupForm').style.filter = 'none';
            document.getElementById('signupForm').style.pointerEvents = 'auto';
        }

        document.getElementById('tokenInput').addEventListener('keyup', function(event) {
            if (event.key === "Enter") {
                checkToken();
            }
        });
    </script>
</body>
</html>
