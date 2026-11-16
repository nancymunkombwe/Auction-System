<?php
session_start();
require '../includes/security.php';
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
    csrf_check();

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $starting_price = floatval($_POST['starting_price']);
    $end_time = $_POST['end_time'];

    if ($title === '' || $description === '' || !is_finite($starting_price) || $starting_price <= 0 || !strtotime($end_time) || strtotime($end_time)<=time()) { http_response_code(422); exit('Enter a title, description, positive price and future closing time.'); }
    $end_time=date('Y-m-d H:i:s',strtotime($end_time));
    $imageName=null;
    if(!empty($_FILES['image']['name'])){
        $file=$_FILES['image'];
        if($file['error']!==UPLOAD_ERR_OK || $file['size']>5*1024*1024) exit('Image upload failed or exceeds 5 MB.');
        $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/avif'=>'avif'];
        $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if(!isset($allowed[$mime])) exit('Only JPEG, PNG, WebP or AVIF images are accepted.');
        $imageName=bin2hex(random_bytes(16)).'.'.$allowed[$mime];
        if(!move_uploaded_file($file['tmp_name'], '../assets/images/'.$imageName)) exit('Unable to save image.');
    }
    // Insert auction into DB
    $stmt = $conn->prepare(
        "INSERT INTO auctions (title, description, starting_price, current_price, end_time, image) 
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("ssddss", $title, $description, $starting_price, $starting_price, $end_time, $imageName);

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
<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Auction</title>
    <link rel="stylesheet" href="../assets/css/style.css">
<link rel="icon" type="image/svg+xml" href="../assets/images/auctify-mark.svg"></head>
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
<input type="hidden" name="csrf" value="<?= csrf_token() ?>">

        <p><strong>Title:</strong><br>
            <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" 
                   required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
        </p>

        <p><strong>Description:</strong><br>
            <textarea name="description" required
                      style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; height:120px;"><?php echo htmlspecialchars($description); ?></textarea>
        </p>

        <p><strong>Starting Price (ZMW):</strong><br>
            <input type="number" step="0.01" name="starting_price" value="<?php echo htmlspecialchars($starting_price); ?>" 
                   required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
        </p>

        <p><strong>End Time:</strong><br>
            <input type="datetime-local" name="end_time" value="<?php echo htmlspecialchars($end_time); ?>" 
                   required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
        </p>

        <p><strong>Image (optional):</strong><br>
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/avif">
        </p>

        <p>
            <button type="submit" 
                    style="padding:12px 20px; background:#2563eb; color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer;">
                Create Auction
            </button>
        </p>

    </form>

</div>

<footer>
    <p>&copy; 2026 Auctify</p>
</footer>

<!-- Animate fade-in -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelector(".page-container").classList.add("loaded");
});
</script>

</body>
</html>


