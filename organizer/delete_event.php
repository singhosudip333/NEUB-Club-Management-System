<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_name']) || !isset($_SESSION['club_id']) || !isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$club_id = $_SESSION['club_id'];

// Database connection parameters
$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "neub_club";

// Check if event ID is provided
if (isset($_GET['eventId'])) {
    $eventId = $_GET['eventId'];

    // Create connection
    $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare the delete statement
    $sql = "DELETE FROM events WHERE id = ? AND club_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $eventId, $club_id);

        // Execute the deletion
        if ($stmt->execute()) {
            echo "<script>alert('Event deleted successfully!'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Error deleting event: " . $stmt->error . "'); window.history.back();</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Error preparing SQL statement: " . $conn->error . "'); window.history.back();</script>";
    }

    $conn->close();
} else {
    echo "<script>alert('No event ID provided.'); window.history.back();</script>";
}
