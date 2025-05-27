<?php
require_once 'config.php'; // Connect to PostgreSQL

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $email && $password) {
        try {
            // Hash the password securely
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Prepare and execute the SQL statement
         //   $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt = $pdo->prepare("INSERT INTO public.users (username, email, password) VALUES (:username, :email, :password)");

            $stmt->execute([
                ':username' => $username,
                ':email'    => $email,
                ':password' => $hashedPassword
            ]);

            $message = "<p style='color:green;'>✅ User registered successfully!</p>";
        } catch (PDOException $e) {
            // Catch duplicate email error or other DB exceptions
            if ($e->getCode() == 23505) {
                $message = "<p style='color:red;'>❌ This email is already registered.</p>";
            } else {
                $message = "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
            }
        }
    } else {
        $message = "<p style='color:red;'>⚠️ All fields are required.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; padding: 50px; }
        form {
            background: white; padding: 20px; border-radius: 8px;
            max-width: 400px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input[type=text], input[type=email], input[type=password] {
            width: 100%; padding: 10px; margin: 8px 0; box-sizing: border-box;
        }
        input[type=submit] {
            background-color: #007BFF; color: white; padding: 10px;
            border: none; border-radius: 4px; cursor: pointer;
        }
        input[type=submit]:hover {
            background-color: #0056b3;
        }
        .password-container {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            top: 50%; right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            user-select: none;
        }
        #passwordStrength {
            font-size: 13px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Signup Form</h2>

<?php if (!empty($message)) echo $message; ?>

<form method="post" action="signup.php">
    <label>Username:</label>
    <input type="text" name="username" required>

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Password:</label>
    <div class="password-container">
        <input type="password" id="password" name="password" required>
        <span class="toggle-password" onclick="togglePassword()">👁️</span>
    </div>
    <div id="passwordStrength"></div>

    <input type="submit" value="Sign Up">
</form>

<script>
function togglePassword() {
    const pw = document.getElementById("password");
    pw.type = pw.type === "password" ? "text" : "password";
}

document.getElementById("password").addEventListener("input", function () {
    const val = this.value;
    const strength = document.getElementById("passwordStrength");

    if (val.length < 6) {
        strength.textContent = "Password too short";
        strength.style.color = "red";
    } else if (!/[A-Z]/.test(val) || !/[0-9]/.test(val)) {
        strength.textContent = "Include uppercase and number";
        strength.style.color = "orange";
    } else {
        strength.textContent = "Strong password";
        strength.style.color = "green";
    }
});
</script>

</body>
</html>
