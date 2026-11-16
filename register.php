<?php
session_start();
require 'includes/security.php';
include 'includes/db.php';

$error = '';
$success = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? OR email=? LIMIT 1");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Username or email already exists. Please choose another.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        if ($stmt->execute()) {
            $success = "Registration successful! Redirecting to login...";
            // Redirect after 2 seconds
            header("refresh:2; url=login.php");
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>Register - Auctify</title>
    <link rel="stylesheet" href="assets/css/style.css">
<link rel="icon" type="image/svg+xml" href="assets/images/auctify-mark.svg"></head>
<body>

<?php include 'includes/header.php'; ?>

<section class="auth-page">
    <!-- Logo -->
    <div class="brand auth-brand"><img class="brand-mark" src="assets/images/auctify-mark.svg" alt=""><span class="wordmark">auctify<span>.</span></span></div>

    <h1>Create an Account</h1>

    <!-- Display error or success -->
    <?php if ($error != ''): ?>
        <p style="color:red; text-align:center;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($success != ''): ?>
        <p style="color:green; text-align:center;"><?php echo $success; ?></p>
    <?php endif; ?>

    <!-- Registration Form -->
    <form method="POST" action="register.php">
<input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php" style="color:#2563eb;">Login here</a></p>
</section>

<footer>
    <p>&copy; 2026 Auctify</p>
</footer>

</body>
</html>
