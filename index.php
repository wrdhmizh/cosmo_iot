<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login </title>
  <style>
    body {
      background: #f3f4f6;
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .login-box {
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      width: 300px;
    }
    .login-box h2 {
      text-align: center;
    }
    .login-box input {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    .login-box button {
      width: 100%;
      padding: 10px;
      background: #4f46e5;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 10px;
    }
    .login-box button:hover {
      background: #4338ca;
    }
    .signup-link {
      display: block;
      text-align: center;
      margin-top: 15px;
    }
    .signup-link a {
      text-decoration: none;
      color: #4f46e5;
      font-weight: bold;
    }
    .signup-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <form class="login-box" method="POST" action="login.php">
    <h2>Login</h2>
    <input type="text" name="username" placeholder="Username" required />
    <input type="password" name="password" placeholder="Password" required />
    <button type="submit">Sign In</button>
    <div class="signup-link">
      Don't have an account? <a href="signup.php">Sign Up</a>
    </div>
  </form>
</body>
</html>
