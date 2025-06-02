<?php
require_once 'config.php';
// Very basic admin check (improve for real apps)
session_start();
if (!isset($_SESSION['is_admin'])) {
    // Uncomment below after testing, or use your real admin login system
    // die('Access denied. Only admin can use this page.');
}

// Generate a new token
if (isset($_POST['generate'])) {
    $token = strtoupper(bin2hex(random_bytes(4))); // Example: 8-character token
    $issued_to = trim($_POST['issued_to'] ?? '');
    $stmt = $pdo->prepare("INSERT INTO public.signup_tokens (token, issued_to) VALUES (:token, :issued_to)");
    $stmt->execute([':token' => $token, ':issued_to' => $issued_to]);
    $success = "New signup token: <b>$token</b>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Token Generator</title>
    <style>
        body {
            background: #e7faed;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0; padding: 0;
            display: flex; flex-direction: column; align-items: center;
        }
        .token-card {
            background: #fff;
            padding: 2rem 2.5rem 2rem 2.5rem;
            border-radius: 18px;
            margin-top: 40px;
            box-shadow: 0 8px 32px 0 rgba(44, 113, 88, 0.13);
            max-width: 420px;
            width: 100%;
        }
        h2 { color: #21986a; text-align: center; }
        form { margin-bottom: 1.2rem; }
        label { color: #21855d; }
        input[type="text"] { 
            padding: 0.6rem; width: 85%; margin-top: 7px; border-radius: 8px; 
            border: 1.2px solid #bcebd7; margin-bottom: 1.2rem; font-size: 1rem;
        }
        button {
            background: #4ed39a; color: #fff; border: none; border-radius: 8px;
            padding: 0.8rem 2.1rem; font-weight: 600; font-size: 1.08rem; cursor: pointer;
        }
        button:hover { background: #34b87a; }
        .success { color: #19845a; background: #d7f6e8; padding: 0.9rem; border-radius: 8px; margin-bottom: 10px; text-align: center; }
        .token-list { background: #f8fffc; border-radius: 10px; padding: 1.1rem; margin-top: 1.4rem;}
        .token-list h3 { margin-bottom: 0.7rem; }
        .token-used { color: #d42a2a; font-weight: 600;}
        .token-active { color: #19845a; font-weight: 600;}
        .issued-to { color: #3c5772; font-size: 0.98rem;}
    </style>
</head>
<body>
    <div class="token-card">
        <h2>Admin: Generate Signup Token</h2>
        <?php if (!empty($success)) echo "<div class='success'>$success</div>"; ?>
        <form method="post">
            <label>Assign to (optional, WhatsApp number or name):</label><br>
            <input type="text" name="issued_to" placeholder="e.g. +6738129566 or John"><br>
            <button type="submit" name="generate">Generate Token</button>
        </form>

        <div class="token-list">
            <h3>Latest Tokens</h3>
            <ul>
            <?php
            $tokens = $pdo->query("SELECT token, issued_to, is_used, created_at FROM public.signup_tokens ORDER BY created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($tokens as $t) {
                $status = $t['is_used'] ? "<span class='token-used'>Used</span>" : "<span class='token-active'>Active</span>";
                $assigned = $t['issued_to'] ? "<span class='issued-to'> &ndash; For: ".htmlspecialchars($t['issued_to'])."</span>" : "";
                echo "<li><b>".htmlspecialchars($t['token'])."</b> $status $assigned</li>";
            }
            ?>
            </ul>
        </div>
    </div>
</body>
</html>
