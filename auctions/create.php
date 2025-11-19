<?php
session_start();
include '../includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Initialize variables
$title = "";
$description = "";
$starting_price = "";
$end_time = "";
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $starting_price = floatval($_POST['starting_price']);
    $end_time = $_POST['end_time'];

    // Handle image upload
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "../assets/images/" . $imageName);
    }

    // Insert auction into DB
    $stmt = $conn->prepare(
        "INSERT INTO auctions (title, description, current_price, end_time, image) 
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("ssdss", $title, $description, $starting_price, $end_time, $imageName);

    if ($stmt->execute()) {
        header("Location: ../index.php?created=1");
        exit();
    } else {
        $message = "Error creating auction. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Auction</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="page-container">

    <div class="page-header">
        <h1>Create New Auction</h1>
    </div>

    <?php if (!empty($message)): ?>
        <p style="color:red; text-align:center; margin-bottom:20px;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="create.php" method="post" enctype="multipart/form-data" 
          style="background:white; padding:25px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);">

        <p><strong>Title:</strong><br>
            <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" 
                   required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
        </p>

        <p><strong>Description:</strong><br>
            <textarea name="description" required
                      style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; height:120px;"><?php echo htmlspecialchars($description); ?></textarea>
        </p>

        <p><strong>Starting Price ($):</strong><br>
            <input type="number" step="0.01" name="starting_price" value="<?php echo htmlspecialchars($starting_price); ?>" 
                   required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
        </p>

        <p><strong>End Time:</strong><br>
            <input type="datetime-local" name="end_time" value="<?php echo htmlspecialchars($end_time); ?>" 
                   required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
        </p>

        <p><strong>Image (optional):</strong><br>
            <input type="file" name="image">
        </p>

        <p>
            <button type="submit" 
                    style="padding:12px 20px; background:#27ae60; color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer;">
                Create Auction
            </button>
        </p>

    </form>

</div>

<footer>
    <p>&copy; 2025 Auctify</p>
</footer>

<!-- Animate fade-in -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelector(".page-container").classList.add("loaded");
});
</script>

</body>
</html>


