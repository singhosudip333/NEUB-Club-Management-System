<?php
// Include database connection file
require_once('../db_connection.php');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $club_name = $_POST['clubName'];
    $club_description = $_POST['clubDescription'];
    $club_category = $_POST['clubCategory'];
    $club_status = $_POST['clubStatus'];

    // Establish the database connection
    $conn = connect_to_db();

    if ($conn) {
        // Prepare the SQL query to insert the club data
        $sql = "INSERT INTO clubs (club_name, description, club_category, club_status, created_at) VALUES (?, ?, ?, ?, NOW())";

        // Prepare the statement
        $stmt = mysqli_prepare($conn, $sql);

        // Bind the form data to the SQL query
        mysqli_stmt_bind_param($stmt, 'ssss', $club_name, $club_description, $club_category, $club_status);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Success - Display a JavaScript alert message and redirect
            echo '<script>alert("Club registered successfully!"); window.location.href = "manageclub.php";</script>';
        } else {
            // Error - Display an error message
            echo "Error: " . mysqli_error($conn);
        }

        // Close the statement and connection
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    } else {
        // Connection failed
        echo "Database connection error: " . mysqli_connect_error();
    }
} else {
    // Invalid request
    echo "Invalid request method.";
}
