<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        echo "<script>alert('Please fill in all fields.'); window.location.href='signup.php';</script>";
        exit();
    }

    $userFile = 'users.txt';

    // Check if user already exists
    if (file_exists($userFile)) {
        $users = file($userFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($users as $user) {
            list($existingUser, ) = explode(':', $user);
            if ($username === $existingUser) {
                echo "<script>alert('Username already exists.'); window.location.href='signup.php';</script>";
                exit();
            }
        }
    }

    // Save new user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    file_put_contents($userFile, "$username:$hashedPassword\n", FILE_APPEND);

    echo "<script>alert('Signup successful. You can now login.'); window.location.href='index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(120deg, #e9f8ef 0%, #d7f6e8 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .signup-card {
            background: #fff;
            padding: 2rem 2rem 1.5rem 2rem;
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(44, 113, 88, 0.09);
            max-width: 350px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 24px;
        }
        .signup-card h2 {
            margin-bottom: 1.4rem;
            color: #1d4c35;
            text-align: center;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .signup-card label {
            display: block;
            margin-bottom: 0.2rem;
            color: #26845d;
        }
        .signup-card input[type="text"],
        .signup-card input[type="password"] {
            width: 100%;
            padding: 0.65rem 0.9rem;
            margin-bottom: 1.1rem;
            border: 1.2px solid #c2ebd7;
            border-radius: 8px;
            background: #e6f8ee;
            font-size: 1rem;
            transition: border 0.2s;
            outline: none;
        }
        .signup-card input[type="text"]:focus,
        .signup-card input[type="password"]:focus {
            border-color: #60c993;
        }
        .signup-card button {
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
        .signup-card button:hover {
            background: #34b87a;
        }

        /* Modal styles */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            background: rgba(55, 90, 70, 0.20);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        .modal {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 40px 0 rgba(44, 113, 88, 0.16);
            padding: 2rem 2.5rem 1.5rem 2.5rem;
            max-width: 340px;
            width: 100%;
            text-align: center;
            animation: modalPop 0.35s cubic-bezier(0.23, 1, 0.32, 1);
        }
        @keyframes modalPop {
            0% { transform: scale(0.6); opacity: 0;}
            100% { transform: scale(1); opacity: 1;}
        }
        .modal h3 {
            margin-bottom: 1rem;
            color: #26845d;
            font-weight: 600;
        }
        .modal input[type="text"] {
            width: 80%;
            padding: 0.6rem;
            margin: 0.7rem 0 1.2rem 0;
            border: 1.2px solid #bcebd7;
            border-radius: 7px;
            background: #e6f8ee;
            font-size: 1rem;
        }
        .modal button {
            padding: 0.6rem 1.8rem;
            background: #4ed39a;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.18s;
            margin-bottom: 0.5rem;
        }
        .modal button:hover {
            background: #34b87a;
        }
        .modal .error-msg {
            color: #c93434;
            font-size: 0.97rem;
            margin-bottom: 0.2rem;
            min-height: 1.2em;
        }
        .modal .request-token {
            color: #269872;
            margin-top: 0.6rem;
            font-size: 0.97rem;
        }
        .modal .request-token a {
            color: #167f52;
            text-decoration: underline;
            font-weight: 500;
        }
        .modal .request-token a:hover {
            color: #12b85c;
        }
        @media (max-width: 480px) {
            .modal, .signup-card { padding: 1.2rem; }
        }
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
        <input type="password" name="password" required>
        <button type="submit">Signup</button>
    </form>

    <script>
        // Replace this with your actual token!
        const VALID_TOKEN = "123456"; // Change this value to your desired token

        // Show modal immediately on page load (default)
        window.onload = function() {
            document.getElementById('modalOverlay').style.display = 'flex';
            document.getElementById('signupForm').style.filter = 'blur(2px)';
            document.getElementById('signupForm').style.pointerEvents = 'none';
            document.getElementById('tokenInput').focus();
        };

        function checkToken() {
            const tokenInput = document.getElementById('tokenInput').value.trim();
            const errorDiv = document.getElementById('tokenError');
            if (tokenInput === VALID_TOKEN) {
                document.getElementById('modalOverlay').style.display = 'none';
                document.getElementById('signupForm').style.filter = 'none';
                document.getElementById('signupForm').style.pointerEvents = 'auto';
            } else {
                errorDiv.textContent = "Invalid token. Please try again.";
                document.getElementById('tokenInput').value = '';
                document.getElementById('tokenInput').focus();
            }
        }

        // Enter key support for modal input
        document.getElementById('tokenInput').addEventListener('keyup', function(event) {
            if (event.key === "Enter") {
                checkToken();
            }
        });
    </script>
</body>
</html>
