<?php
// Start session safely
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'includes/db.php';

// Handle search query if submitted
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Build SQL query
$sql = "SELECT * FROM auctions";
if ($search != '') {
    $sql .= " WHERE title LIKE ? OR description LIKE ?";
}
$sql .= " ORDER BY end_time ASC";

if ($search != '') {
    $stmt = $conn->prepare($sql);
    $likeSearch = "%$search%";
    $stmt->bind_param("ss", $likeSearch, $likeSearch);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <title>Auctify - Active Auctions</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelector(".page-container").classList.add("loaded");
});
</script>

<body>

<?php include 'includes/header.php'; ?>

<div class="page-container">

    <!-- Page header with title and create button -->
    <div class="page-header">
        <h1>Active Auctions</h1>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="auctions/create.php" class="create-auction-btn">Create Auction</a>
        <?php endif; ?>
    </div>

    <!-- Search bar -->
    <form method="GET" action="" style="margin-bottom: 25px; text-align: center;">
        <input type="text" name="search" placeholder="Search auctions..." value="<?php echo htmlspecialchars($search); ?>" style="padding:10px 15px; width: 60%; max-width:400px; border-radius:8px; border:1px solid #ccc;">
        <button type="submit" style="padding:10px 15px; border-radius:8px; border:none; background-color:#f39c12; color:white; font-weight:bold;">Search</button>
    </form>

    <!-- Auction cards -->
    <?php
    if ($result && $result->num_rows > 0):
        while ($auction = $result->fetch_assoc()):
    ?>
        <div class="card">
            <?php if($auction['image']): ?>
                <img src="assets/images/<?php echo htmlspecialchars($auction['image']); ?>" alt="<?php echo htmlspecialchars($auction['title']); ?>">
            <?php endif; ?>

            <h3><?php echo htmlspecialchars($auction['title']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($auction['description'])); ?></p>
            <p><strong>Current Price:</strong> $<?php echo htmlspecialchars($auction['current_price']); ?></p>
            <p><strong>Ends at:</strong> <?php echo htmlspecialchars($auction['end_time']); ?></p>

            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="auctions/view.php?id=<?php echo $auction['id']; ?>" class="btn">View Auction</a>
            <?php else: ?>
                <a href="login.php" class="btn">Login to view</a>
            <?php endif; ?>
        </div>
    <?php
        endwhile;
    else:
    ?>
        <p style="text-align:center; color:#333;">No active auctions at the moment.</p>
    <?php endif; ?>

</div>

<footer>
    <p>&copy; 2025 Auctify</p>
</footer>

<!-- Animate auction cards -->
<script>
const cards = document.querySelectorAll('.card');
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if(entry.isIntersecting) {
            entry.target.style.transform = 'translateY(0)';
            entry.target.style.opacity = '1';
        }
    });
}, { threshold: 0.1 });

cards.forEach(card => observer.observe(card));
</script>

</body>
</html>


