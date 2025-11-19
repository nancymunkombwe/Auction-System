<?php
session_start();
include '../includes/db.php'; // uses auction_system database

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get auction ID from POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['auction_id']) || empty($_POST['auction_id'])) {
        die("Auction ID is missing.");
    }
    $auction_id = intval($_POST['auction_id']);
} else {
    die("Invalid request.");
}

// Fetch auction details
$stmt = $conn->prepare("SELECT * FROM auctions WHERE id = ?");
$stmt->bind_param("i", $auction_id);
$stmt->execute();
$result = $stmt->get_result();
$auction = $result->fetch_assoc();

if (!$auction) {
    die("Auction not found.");
}

$message = "";
$message_type = ""; // success or error

// Handle bid submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bid_amount = floatval($_POST['bid_amount']);
    $user_id = $_SESSION['user_id'];

    if ($bid_amount <= $auction['current_price']) {
        $message = "Your bid must be higher than the current price ($" . number_format($auction['current_price'], 2) . ").";
        $message_type = "error";
    } else {
        // Begin transaction
        $conn->begin_transaction();
        try {
            // Update current price in auctions table
            $updateStmt = $conn->prepare("UPDATE auctions SET current_price = ? WHERE id = ?");
            $updateStmt->bind_param("di", $bid_amount, $auction_id);
            $updateStmt->execute();

            // Insert bid into bids table (using your column names)
            $insertStmt = $conn->prepare(
                "INSERT INTO bids (auction_id, user_id, amount, created_at) VALUES (?, ?, ?, NOW())"
            );
            $insertStmt->bind_param("iid", $auction_id, $user_id, $bid_amount);
            $insertStmt->execute();

            $conn->commit();
            $message = "Bid placed successfully!";
            $message_type = "success";

            // Refresh auction details
            $stmt->execute();
            $auction = $stmt->get_result()->fetch_assoc();

        } catch (Exception $e) {
            $conn->rollback();
            $message = "Error placing bid: " . $e->getMessage();
            $message_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Place Bid - <?php echo htmlspecialchars($auction['title']); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Success / Error messages */
        .message-success {
            color: #27ae60; /* green */
            text-align: center;
            margin-bottom: 20px;
        }
        .message-error {
            color: #e74c3c; /* red */
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="page-container">

    <div class="page-header">
        <h1><?php echo htmlspecialchars($auction['title']); ?></h1>
    </div>

    <?php if (!empty($message)): ?>
        <p class="<?php echo $message_type === 'success' ? 'message-success' : 'message-error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <div style="margin-bottom:20px;">
        <?php if($auction['image']): ?>
            <img src="../assets/images/<?php echo htmlspecialchars($auction['image']); ?>" 
                 alt="<?php echo htmlspecialchars($auction['title']); ?>" style="max-width:100%; margin-bottom:15px;">
        <?php endif; ?>
        <p><?php echo nl2br(htmlspecialchars($auction['description'])); ?></p>
        <p><strong>Current Price:</strong> $<?php echo number_format($auction['current_price'], 2); ?></p>
        <p><strong>Ends at:</strong> <?php echo htmlspecialchars($auction['end_time']); ?></p>
    </div>

    <form action="" method="post" style="background:white; padding:25px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
        <!-- Hidden field with auction ID -->
        <input type="hidden" name="auction_id" value="<?php echo $auction['id']; ?>">

        <p><strong>Your Bid ($):</strong><br>
            <input type="number" step="0.01" name="bid_amount" required 
                   style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
        </p>
        <p>
            <button type="submit" style="padding:12px 20px; background:#27ae60; color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer;">
                Place Bid
            </button>
        </p>
    </form>

</div>

<footer>
    <p>&copy; 2025 Auctify</p>
</footer>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelector(".page-container").classList.add("loaded");
});
</script>

</body>
</html>






