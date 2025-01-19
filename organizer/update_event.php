<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_name']) || !isset($_SESSION['club_id']) || !isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection parameters
$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "neub_club";

// Create connection
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['eventId'];
    $eventName = $_POST['eventName'];
    $eventDate = $_POST['eventDate'];
    $eventLocation = $_POST['eventLocation'];
    $eventDescription = $_POST['eventDescription'];

    // Update event query
    $sql = "UPDATE events SET name = ?, date = ?, location = ?, description = ? WHERE id = ? AND club_id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssis", $eventName, $eventDate, $eventLocation, $eventDescription, $eventId, $_SESSION['club_id']);

        if ($stmt->execute()) {
            echo "<script>alert('Event updated successfully!'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Error updating event: " . $stmt->error . "'); window.history.back();</script>";
        }

        $stmt->close(); // Close statement after use
    } else {
        echo "<script>alert('Error preparing SQL statement: " . $conn->error . "'); window.history.back();</script>";
    }
}

$conn->close();
