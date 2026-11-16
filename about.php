<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About - Auctify</title>
    <link rel="stylesheet" href="assets/css/style.css">
<link rel="icon" type="image/svg+xml" href="assets/images/auctify-mark.svg"></head>
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
    <p>&copy; 2026 Auctify</p>
</footer>

<script>
document.querySelector(".page-container").classList.add("loaded");
</script>

</body>
</html>
