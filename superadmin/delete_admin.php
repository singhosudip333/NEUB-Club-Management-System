<?php
// delete_admin.php

// Include your database connection file
require_once('../db_connection.php');

// Check if admin_id is set in the POST request
if (isset($_POST['admin_id'])) {
    // Get the admin ID from the POST request
    $admin_id = $_POST['admin_id'];

    // Connect to the database
    $conn = connect_to_db();

    // SQL query to delete the admin based on the provided admin_id
    $deleteQuery = "DELETE FROM admins WHERE id = ?";

    // Prepare the SQL statement
    if ($stmt = mysqli_prepare($conn, $deleteQuery)) {
        // Bind the admin ID to the statement
        mysqli_stmt_bind_param($stmt, "i", $admin_id);

        // Execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Success, redirect back to the page showing the admin list (adjust the path as needed)
            header("Location: add_commiteeadmin.php");
            exit();
        } else {
            // If the query execution fails
            echo "Error deleting record: " . mysqli_error($conn);
        }

        // Close the prepared statement
        mysqli_stmt_close($stmt);
    } else {
        // If the statement could not be prepared
        echo "Error preparing statement: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
} else {
    // If no admin_id is provided, redirect to the same page
    header("Location: add_club_admin_page.php?error=No+admin+ID+provided");
    exit();
}
