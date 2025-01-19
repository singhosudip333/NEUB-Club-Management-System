<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the meeting ID is provided
if (!isset($_GET['id'])) {
    echo "Meeting ID not provided.";
    exit();
}

$meeting_id = $_GET['id'];

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Update with your database password
$dbname = "neub_club"; // Update with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete the meeting
$delete_query = "DELETE FROM meetings WHERE id = ?";
$delete_stmt = $conn->prepare($delete_query);
$delete_stmt->bind_param("i", $meeting_id);

if ($delete_stmt->execute()) {
    // Redirect back to manage_meetings.php with a success message
    header("Location: manage_meetings.php?message=Meeting+deleted+successfully");
} else {
    echo "Error deleting meeting: " . $conn->error;
}

$conn->close();
