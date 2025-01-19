<?php
session_start();

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

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $club_id = $_POST['club_id'];
    $club_name = $_POST['clubName'];
    $club_description = $_POST['clubDescription'];
    $club_category = $_POST['clubCategory'];
    $club_status = $_POST['clubStatus'];

    // Prepare the SQL update statement
    $update_sql = "UPDATE clubs SET club_name = ?, description = ?, club_category = ?, club_status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $club_name, $club_description, $club_category, $club_status, $club_id);

    // Execute the statement and check for success
    if ($update_stmt->execute()) {
        echo "Club updated successfully!";
    } else {
        echo "Error updating club: " . $conn->error;
    }

    // Close the prepared statement
    $update_stmt->close();
}

// Close the database connection
$conn->close();
?>
