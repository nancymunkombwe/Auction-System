<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact - Auctify</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="page-container loaded">
    <h1>Contact Us</h1>

    <div class="card">
        <p>We’d love to hear from you! You can reach us using the following methods:</p>

        <ul>
            <li><strong>Email:</strong> nancymunkombwe14@gmail.com</li>
            <li><strong>Phone:</strong> +260 76 342 8860</li>
            <li><strong>Whasapp:</strong> +260 77 241 4755</li>
            <li><strong>Address:</strong> Lusaka West, Mumbwa Road, Lusaka, Zambia</li>
            <br>                                             
        </ul>

        <p>Our team is available Monday to Friday, 8:00 AM – 5:00 PM.</p>
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