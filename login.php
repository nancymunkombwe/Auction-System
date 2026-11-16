<?php
session_start();
require 'includes/security.php';
include 'includes/db.php';

// If user is already logged in, redirect to homepage
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check database for user
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Check password
        if (password_verify($password, $user['password'])) {
            // Login success
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>Login - Auctify</title>
    <link rel="stylesheet" href="assets/css/style.css">
<link rel="icon" type="image/svg+xml" href="assets/images/auctify-mark.svg"></head>
<body>

<?php include 'includes/header.php'; ?>

<section class="auth-page">
    <!-- Logo -->
    <div class="brand auth-brand"><img class="brand-mark" src="assets/images/auctify-mark.svg" alt=""><span class="wordmark">auctify<span>.</span></span></div>

    <h1>Welcome to Auctify</h1>

    <!-- Error message -->
    <?php if ($error != ''): ?>
        <p style="color:red; text-align:center;"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- Login form -->
    <form method="POST" action="login.php">
<input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php" style="color:#2563eb;">Register here</a></p>
</section>

<footer>
    <p>&copy; 2026 Auctify</p>
</footer>

</body>
</html>
