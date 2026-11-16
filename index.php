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
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Auctify — Discover. Bid. Win.</title><link rel="stylesheet" href="assets/css/style.css"><link rel="icon" type="image/svg+xml" href="assets/images/auctify-mark.svg"></head><body>
<?php include 'includes/header.php'; ?>
<main class="page-container marketplace"><section class="hero"><div><span class="eyebrow">THE MARKETPLACE FOR BETTER FINDS</span><h1>Your next great<br>find awaits.</h1><p>Find something worth bidding for. Discover unique items, compete for great deals and list your own.</p><a class="btn hero-btn" href="#lots">Explore auctions →</a><div class="hero-meta"><span>01 Discover</span><span>02 Bid</span><span>03 Win</span></div></div><div class="hero-art" aria-hidden="true"><img src="assets/images/auction-hero.svg" alt=""></div></section>
<?php if(isset($_GET['created'])): ?><p class="notice">Your auction has been published.</p><?php endif; ?>
<div class="page-header" id="lots"><div><span class="eyebrow blue">EXPLORE THE MARKETPLACE</span><h2>Discover auctions</h2></div><a class="btn" href="<?= isset($_SESSION['user_id']) ? 'auctions/create.php' : 'login.php' ?>">+ Sell an item</a></div>
<form class="searchbar" method="GET"><input name="search" placeholder="Search by item or description" value="<?= htmlspecialchars($search) ?>"><button class="btn">Search</button><?php if($search !== ''): ?><a href="index.php">Clear</a><?php endif; ?></form>
<div class="auction-grid"><?php if($result && $result->num_rows): while($auction=$result->fetch_assoc()): $end=strtotime($auction['end_time']); $closed=$end<=time(); ?>
<article class="lot"><div class="lot-image"><?php if(!empty($auction['image'])): ?><img src="assets/images/<?= rawurlencode(basename($auction['image'])) ?>" alt="<?= htmlspecialchars($auction['title']) ?>"><?php else: ?><img class="placeholder-mark" src="assets/images/auctify-mark.svg" alt="No item photo"><?php endif; ?><span class="status <?= $closed?'closed':'' ?>"><?= $closed?'CLOSED':'● LIVE' ?></span></div><div class="lot-body"><span class="eyebrow blue">LOT <?= (int)$auction['id'] ?></span><h3><?= htmlspecialchars($auction['title']) ?></h3><p class="description"><?= htmlspecialchars($auction['description']) ?></p><small>Current price (ZMW)</small><div class="price">K<?= number_format($auction['current_price'],2) ?></div><div class="countdown" data-end="<?= $end ?>"><?= $closed?'Auction ended':'Loading…' ?></div><a class="btn full" href="auctions/view.php?id=<?= (int)$auction['id'] ?>"><?= $closed?'View result':'View auction →' ?></a></div></article>
<?php endwhile; else: ?><div class="card"><div class="empty-icon"><img src="assets/images/auctify-mark.svg" alt=""></div><h3>No auctions found</h3><p>Try another search or be the first to list something.</p><a class="btn" href="<?= isset($_SESSION['user_id']) ? 'auctions/create.php' : 'login.php' ?>">List an item →</a></div><?php endif; ?></div></main><footer>Auctify. · Discover. Bid. Win. · © 2026</footer><script src="assets/js/countdown.js"></script></body></html>