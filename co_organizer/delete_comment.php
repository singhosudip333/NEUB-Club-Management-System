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

// Check if the comment ID is provided in the URL
if (isset($_GET['id'])) {
    $comment_id = $_GET['id'];

    // Create a new connection
    $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Delete the comment
    $sql = "DELETE FROM comments WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $comment_id);

    if ($stmt->execute()) {
        // Redirect back to the blog management page after deleting the comment
        header("Location: dashboard.php"); // Or wherever you need to redirect
    } else {
        echo "Error deleting comment: " . $conn->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "No comment ID specified.";
}
