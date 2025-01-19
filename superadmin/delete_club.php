<?php
// Include database connection file
require_once('../db_connection.php');

$conn = connect_to_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    // Get the club ID from the POST request
    $club_id = $_POST['club_id'];

    // Prepare the SQL statement to prevent SQL injection
    if ($stmt = $conn->prepare("DELETE FROM clubs WHERE id = ?")) {
        $stmt->bind_param("i", $club_id);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Club deleted successfully.";
        } else {
            echo "Error deleting club: " . $conn->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Failed to prepare the delete statement.";
    }
}

// Close the database connection
$conn->close();
