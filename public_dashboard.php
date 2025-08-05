<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'], $_SESSION['user_type']) || $_SESSION['user_type'] !== 'public') {
    header('Location: public_login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $newUsername = trim($_POST['new_username'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');

    if ($newUsername === '') {
        $error = "Username cannot be empty.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
        $stmt->execute([':username' => $newUsername, ':id' => $userId]);
        if ($stmt->fetch()) {
            $error = "Username is already taken.";
        } else {
            if ($newPassword !== '') {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET username = :username, password = :password WHERE id = :id");
                $success = $stmt->execute([':username' => $newUsername, ':password' => $hashedPassword, ':id' => $userId]) ? "Profile updated successfully." : "Failed to update profile.";
            } else {
                $stmt = $pdo->prepare("UPDATE users SET username = :username WHERE id = :id");
                $success = $stmt->execute([':username' => $newUsername, ':id' => $userId]) ? "Profile updated successfully." : "Failed to update profile.";
            }

            if ($success === "Profile updated successfully.") {
                $_SESSION['username'] = $newUsername;
                $username = $newUsername;
            } else {
                $error = $success;
                $success = '';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Public Dashboard</title>
<style>
  html, body {
    margin: 0; padding: 0;
    font-family: 'Segoe UI', Arial, sans-serif;
    height: 100vh;
    background: url('pic.jpeg') no-repeat center center fixed;
    background-size: cover;
  }

  .navbar {
    background-color: rgba(29, 76, 53, 0.85);
    color: white;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 10;
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
  }

  .navbar h1 {
    margin: 0;
    font-size: 1.4rem;
  }

  .navbar a {
    color: #ffffff;
    text-decoration: underline;
    font-size: 0.95rem;
  }

  .container {
    max-width: 1000px;
    margin: 2rem auto;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    padding: 1rem;
    position: relative;
    z-index: 10;
  }

  /* Main menu styles */
  .menu {
    display: flex;
    border-bottom: 2px solid #4ed39a;
    margin-bottom: 1rem;
  }

  .menu button {
    flex: 1;
    background: none;
    border: none;
    padding: 1rem;
    font-size: 1.1rem;
    font-weight: 600;
    color: #26845d;
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s;
    border-bottom: 3px solid transparent;
  }

  .menu button:hover {
    background-color: #e6f8ee;
  }

  .menu button.active {
    border-bottom: 3px solid #4ed39a;
    color: #1d4c35;
    background-color: #d1f0d8;
  }

  /* Sections */
  .section {
    display: none;
  }

  .section.active {
    display: block;
  }

  form textarea,
  form input[type="text"],
  form input[type="password"] {
    width: 100%;
    padding: 0.75rem;
    margin: 0.5rem 0 1rem 0;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
    box-sizing: border-box;
  }

  form button {
    margin-top: 1rem;
    padding: 0.7rem 1.4rem;
    background-color: #4ed39a;
    border: none;
    color: white;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  form button:hover {
    background-color: #34b87a;
  }

  .locations ul {
    padding-left: 1.2rem;
  }

  .locations li {
    margin: 0.4rem 0;
  }

  .locations a {
    text-decoration: underline;
    color: #26845d;
  }

  .success-msg, .error-msg {
    padding: 0.8rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    font-weight: 600;
  }

  .success-msg {
    background-color: #e6fff1;
    color: #1e824c;
  }

  .error-msg {
    background-color: #ffe6e6;
    color: #c93434;
  }

  .footer {
    text-align: center;
    padding: 1rem;
    color: #666;
    font-size: 0.9rem;
    position: relative;
    z-index: 10;
  }

  /* OpenSenseMap iframe style */
  .osm-iframe {
    width: 100%;
    height: 450px;
    border: none;
    margin-top: 1rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  }
</style>
</head>
<body>

<div class="navbar">
  <h1>Welcome, <?= htmlspecialchars($username) ?></h1>
  <a href="logout.php">Logout</a>
</div>

<div class="container">
  <nav class="menu" role="navigation" aria-label="Main menu">
    <button type="button" data-section="feedback">Submit Feedback</button>
    <button type="button" data-section="locations">Smart Bin Locations</button>
    <button type="button" data-section="settings">Personal Settings</button>
  </nav>

 

  <section id="feedback" class="section" aria-label="Submit Feedback">
    <h2>Submit Feedback</h2>
    <form method="GET" action="mailto:dreamteams666666@gmail.com" enctype="text/plain">
      <textarea name="body" placeholder="Write your feedback here..." rows="5" required></textarea><br />
      <button type="submit">Send Feedback via Email</button>
    </form>
  </section>

  <section id="locations" class="section" aria-label="Smart Bin Locations">
    <h2>Smart Bin Locations</h2>
    <div class="locations">
      <ul>
        <li><strong>Brunei-Muara:</strong> <a href="https://maps.google.com/?q=stoneville+Brunei" target="_blank" rel="noopener noreferrer">Stoneville</a></li>
        <li><strong>Tutong:</strong> <a href="https://maps.google.com/?q=Hua+Ho+Tutong+Brunei" target="_blank" rel="noopener noreferrer">Hua Ho Tutong</a></li>
        <li><strong>Belait:</strong> <a href="https://maps.google.com/?q=KB+Central+Brunei" target="_blank" rel="noopener noreferrer">KB Central</a></li>
        <li><strong>Temburong:</strong> <a href="https://maps.google.com/?q=Temburong+Brunei" target="_blank" rel="noopener noreferrer">Temburong District</a></li>
      </ul>
    </div>

    <!-- OpenSenseMap Embed -->
    <iframe class="osm-iframe" 
      src="https://opensensemap.org/explore/embed?box=5f26d123b1a5bc001caf60ec" 
      title="OpenSenseMap Smart Bin Map"
      allowfullscreen
      loading="lazy"></iframe>
  </section>

  <section id="settings" class="section" aria-label="Personal Settings">
    <h2>Personal Settings</h2>

    <?php if ($success): ?>
      <div class="success-msg"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <label for="new_username">Username:</label>
      <input type="text" id="new_username" name="new_username" value="<?= htmlspecialchars($username) ?>" required />

      <label for="new_password">New Password (leave blank to keep current):</label>
      <input type="password" id="new_password" name="new_password" placeholder="New Password" />

      <button type="submit" name="update_profile">Save Changes</button>
    </form>
  </section>
</div>

<div class="footer">
  &copy; <?= date('Y') ?> SmartBin Public Portal. All rights reserved.
</div>

<script>
  const buttons = document.querySelectorAll('.menu button');
  const sections = document.querySelectorAll('.section');

  buttons.forEach(button => {
    button.addEventListener('click', () => {
      buttons.forEach(btn => btn.classList.remove('active'));
      sections.forEach(sec => sec.classList.remove('active'));

      button.classList.add('active');
      const sectionToShow = document.getElementById(button.getAttribute('data-section'));
      if (sectionToShow) {
        sectionToShow.classList.add('active');
      }
    });
  });
</script>

</body>
</html>
