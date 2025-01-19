<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "neub_club";

// Create connection
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the admin ID to delete
$admin_id = $_POST['admin_id'];

// Prepare and execute the delete statement
$sql = "DELETE FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);

if ($stmt->execute()) {
    echo json_encode('Member deleted successfully');
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting member: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
