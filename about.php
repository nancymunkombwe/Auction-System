<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>About - Auctify</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="page-container loaded">
    <h1>About Auctify</h1>

    <div class="card">
        <p>
            Auctify is a modern online auction platform designed to connect buyers and sellers.
            Our mission is to make bidding simple, secure, and exciting.
        </p>

        <p>
            Whether you're selling items or competing in live auctions, 
            Auctify gives you a smooth, user-friendly experience.
        </p>

        <p>
            Built with passion, styled with care, powered by code. ⚡
        </p>
    </div>
</div>

<footer>
    <p>&copy; 2025 Auctify</p>
</footer>

<script>
document.querySelector(".page-container").classList.add("loaded");
</script>

</body>
</html>
