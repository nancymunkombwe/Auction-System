<?php
session_start();
require '../includes/security.php';
include '../includes/db.php';

if (!isset($_GET['id'])) {
    echo "No auction specified.";
    exit;
}

$auction_id = (int)$_GET['id'];

$sql = "SELECT * FROM auctions WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $auction_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    echo "Auction not found.";
    exit;
}

$auction = $result->fetch_assoc();

$sql_bids = "SELECT b.amount, u.username, b.created_at 
             FROM bids b 
             JOIN users u ON b.user_id = u.id 
             WHERE b.auction_id = ? 
             ORDER BY b.amount DESC";
$stmt_bids = $conn->prepare($sql_bids);
$stmt_bids->bind_param("i", $auction_id);
$stmt_bids->execute();
$bids_result = $stmt_bids->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($auction['title']); ?> - Auction</title>
    <link rel="stylesheet" href="../assets/css/style.css">
<link rel="icon" type="image/svg+xml" href="../assets/images/auctify-mark.svg"></head>
<body>

<?php include '../includes/header.php'; ?>

<div class="page-container loaded">

    <h1><?php echo htmlspecialchars($auction['title']); ?></h1>

    <div class="card">
        <?php if ($auction['image']) { ?>
            <img src="../assets/images/<?= rawurlencode(basename($auction['image'])) ?>" alt="Auction Image">
        <?php } ?>

        <p><?php echo nl2br(htmlspecialchars($auction['description'])); ?></p>

        <p><strong>Starting Price:</strong> K<?= htmlspecialchars((string)($auction['starting_price'] ?? 'Not recorded')) ?></p>
        <p><strong>Current Price:</strong> K<?php echo $auction['current_price']; ?></p>
        <p><strong>Ends at:</strong> <?php echo $auction['end_time']; ?></p>
    </div>

    <div class="countdown" data-end="<?= strtotime($auction['end_time']) ?>"></div><?php if(isset($_SESSION['flash'])): ?><p class="notice"><?= htmlspecialchars($_SESSION['flash']) ?></p><?php unset($_SESSION['flash']); endif; ?><h2>Bid history</h2>
    <div class="card">
        <?php
        if ($bids_result->num_rows > 0) {
            echo "<ul>";
            while ($bid = $bids_result->fetch_assoc()) {
                echo "<li><strong>" . htmlspecialchars($bid['username']) . "</strong> bid K" . $bid['amount'] . " <br><small>(" . $bid['created_at'] . ")</small></li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No bids yet.</p>";
        }
        ?>
    </div>

    <?php if (strtotime($auction['end_time']) <= time()) { ?><p class="notice">Auction ended.</p><?php } elseif (isset($_SESSION['user_id'])) { ?>
        <h2>Place a Bid</h2>
        <div class="card">
            <form action="bid.php" method="post" class="contact-form" data-bid-form>
<input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="auction_id" value="<?php echo $auction_id; ?>">
                <input type="number" step="0.01" min="<?= number_format($auction['current_price']+0.01,2,'.','') ?>" required name="bid_amount" placeholder="Your Bid">
                <button class="btn" type="submit">Place Bid</button>
            </form>
        </div>
    <?php } else { ?>
        <p><a class="btn" href="../login.php">Login to place a bid</a></p>
    <?php } ?>

    <p style="margin-top:20px;"><a class="btn" href="../index.php">Back to Homepage</a></p>

</div>

<footer>
    <p>&copy; 2026 Auctify</p>
</footer>

<script>
document.querySelector(".page-container").classList.add("loaded");
</script>

<script src="../assets/js/countdown.js"></script></body>
</html>