<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Home</title>
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
        .home-card {
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
        .home-card h1 {
            margin-bottom: 1.6rem;
            color: #1d4c35;
            text-align: center;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .home-card a {
            display: block;
            color: #1fb772;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1rem;
            font-size: 1.1rem;
            transition: color 0.2s ease-in-out;
        }
        .home-card a:hover {
            text-decoration: underline;
            color: #146b3f;
        }
    </style>
</head>
<body>
    <div class="home-card">
        <h1>Welcome to the IoT Dashboard</h1>
        <a href="login.php">Private Login</a>
        <a href="public_login.php">Public Login</a>
        <a href="public_signup.php">Public Sign Up</a>
        <a href="private_signup.php">Private Sign Up</a>
    </div>
</body>
</html>
