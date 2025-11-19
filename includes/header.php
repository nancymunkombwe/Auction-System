<header>
    <div class="logo">
        <a href="/auction-system/index.php">
            <img src="/auction-system/assets/images/logo.png" alt="Auctify Logo">
        </a>
    </div>

    <nav>
        <a href="/auction-system/index.php">Home</a>
        <a href="/auction-system/about.php">About</a>
        <a href="/auction-system/contact.php">Contact</a>

        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="/auction-system/logout.php">Logout</a>
        <?php else: ?>
            <a href="/auction-system/login.php">Login</a>
            <a href="/auction-system/register.php">Register</a>
        <?php endif; ?>
    </nav>

    <!-- Hamburger menu for mobile -->
    <div class="hamburger" onclick="toggleMenu()">
        <div></div>
        <div></div>
        <div></div>
    </div>
</header>

<script>
function toggleMenu() {
    const nav = document.querySelector('header nav');
    nav.classList.toggle('active');
}
</script>
