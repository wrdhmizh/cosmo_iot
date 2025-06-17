<?php
session_start();
require_once 'config.php';

// --- SETTINGS ---
$MAX_UNUSED = 10; // max allowed unused tokens at a time

// Simple admin session check (customize for your system)
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die("Access denied.");
}

// Handle new token creation
if (isset($_POST['new_token'])) {
    $new_token = trim($_POST['new_token']);
    $expiry = trim($_POST['expiry'] ?? '');
    // Check unused count
    $count_stmt = $pdo->query("SELECT COUNT(*) FROM public.signup_tokens WHERE is_used = FALSE AND (expiry IS NULL OR expiry >= CURRENT_DATE)");
    $unused_count = $count_stmt->fetchColumn();
    if ($unused_count >= $MAX_UNUSED) {
        $error = "You have reached the maximum number of unused tokens ($MAX_UNUSED).";
    } else {
        if ($new_token !== '') {
            $sql = "INSERT INTO public.signup_tokens (token, is_used" . ($expiry ? ", expiry" : "") . ") VALUES (:token, FALSE" . ($expiry ? ", :expiry" : "") . ")";
            $params = [':token' => $new_token];
            if ($expiry) $params[':expiry'] = $expiry;
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute($params);
                $message = "Token created successfully!";
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}

// Handle token deletion
if (isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $pdo->prepare("DELETE FROM public.signup_tokens WHERE id = :id");
    $stmt->execute([':id' => $delete_id]);
    $message = "Token deleted successfully!";
}

// Fetch all tokens
$stmt = $pdo->query("SELECT * FROM public.signup_tokens ORDER BY id DESC");
$tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Signup Tokens</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f9f6; }
        .container { width: 95%; max-width: 700px; margin: 2.5rem auto; background: #fff; padding: 2rem; border-radius: 16px; box-shadow: 0 8px 28px 0 rgba(60, 80, 80, 0.09);}
        h2 { color: #207c56; margin-bottom: 1.2rem; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem;}
        th, td { border: 1px solid #ddeee6; padding: 0.7rem; text-align: center;}
        th { background: #e9f6ee; }
        .btn { padding: 0.45rem 1.1rem; border: none; border-radius: 6px; cursor: pointer; }
        .btn-delete { background: #ff6961; color: #fff; }
        .btn-generate { background: #28c76f; color: #fff; }
        .btn-random { background: #2061ce; color: #fff; margin-left: 0.4rem;}
        .btn-copy { background: #00b2d6; color: #fff; }
        .btn-copy:hover { background: #0084a8; }
        .msg { margin-bottom: 1rem; color: #218d68;}
        .err { margin-bottom: 1rem; color: #c92828;}
        input[type="text"], input[type="date"] { padding: 0.45rem; border: 1px solid #c6eed9; border-radius: 6px;}
        .expired { color: #b92828; font-weight: bold; }
        .unused { color: #22b96f; }
        .used { color: #b14d25; }
    </style>
    <script>
        // Random token generator
        function generateToken() {
            let chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
            let rand = () => Array.from({length: 4}, () => chars.charAt(Math.floor(Math.random() * chars.length))).join('');
            let token = "TOKEN-" + rand() + "-" + Math.floor(1000 + Math.random()*9000);
            document.getElementById('new_token').value = token;
        }

        // Copy to clipboard function
        function copyToken(tokenId, btn) {
            var token = document.getElementById(tokenId).innerText;
            navigator.clipboard.writeText(token).then(function() {
                var oldText = btn.innerText;
                btn.innerText = "Copied!";
                btn.style.background = "#28c76f";
                setTimeout(function() {
                    btn.innerText = oldText;
                    btn.style.background = "";
                }, 1200);
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Manage Signup Tokens</h2>

        <?php if (!empty($message)) echo "<div class='msg'>$message</div>"; ?>
        <?php if (!empty($error)) echo "<div class='err'>$error</div>"; ?>

        <form method="post" style="margin-bottom: 2rem; display: flex; gap: 0.8rem; align-items: center;">
            <input type="text" name="new_token" id="new_token" placeholder="Enter new token or generate" required style="flex:2;">
            <button class="btn btn-random" type="button" onclick="generateToken()">Random</button>
            <input type="date" name="expiry" style="flex:1;">
            <button class="btn btn-generate" type="submit">Create Token</button>
        </form>

        <div style="font-size:0.97em; color:#777; margin-bottom:0.5rem;">
            * You may create up to <b><?=$MAX_UNUSED?></b> unused tokens.<br>
            * Expired tokens are automatically invalid.
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Token</th>
                    <th>Status</th>
                    <th>Expiry</th>
                    <th>Copy</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tokens)) { ?>
                    <tr><td colspan="6">No tokens found.</td></tr>
                <?php } else {
                    foreach ($tokens as $row) {
                        $is_expired = $row['expiry'] && strtotime($row['expiry']) < strtotime(date("Y-m-d"));
                        ?>
                        <tr>
                            <td><?=htmlspecialchars($row['id'])?></td>
                            <td id="token-<?=$row['id']?>"><?=htmlspecialchars($row['token'])?></td>
                            <td>
                                <?php
                                if ($is_expired) echo "<span class='expired'>Expired</span>";
                                else if ($row['is_used']) echo "<span class='used'>Used</span>";
                                else echo "<span class='unused'>Unused</span>";
                                ?>
                            </td>
                            <td>
                                <?=$row['expiry'] ? htmlspecialchars($row['expiry']) : '-'?>
                            </td>
                            <td>
                                <button class="btn btn-copy" type="button" onclick="copyToken('token-<?=$row['id']?>', this)">Copy</button>
                            </td>
                            <td>
                                <form method="post" style="margin:0;">
                                    <input type="hidden" name="delete_id" value="<?=$row['id']?>">
                                    <button class="btn btn-delete" type="submit" onclick="return confirm('Delete this token?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                <?php }} ?>
            </tbody>
        </table>
        <a href="admin_dashboard.php" style="text-decoration:none; color:#218d68;">&larr; Back to Dashboard</a>
    </div>
</body>
</html>
