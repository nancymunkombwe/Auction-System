<?php
// Database credentials
$servername = "localhost";
$username = "root"; // default XAMPP MySQL user
$password = "";     // default XAMPP MySQL password is empty
$dbname = "auction_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// echo "Connected successfully"; // uncomment to test connection
?>
