<?php
require_once('../db_connection.php'); // Include your database connection script

// Establish the database connection
$conn = connect_to_db();

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    // Get username and password from the form
    $username = $_POST['username'];
    $password = $_POST['password']; // Plain text password for this simple example

    // Prepare the SQL query to get the user by username
    $sql = "SELECT * FROM superadmin WHERE username = ?";

    // Check if the connection was successful
    if ($conn) {
        // Prepare the statement
        $stmt = mysqli_prepare($conn, $sql);

        // Bind parameters
        mysqli_stmt_bind_param($stmt, "s", $username);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Get the result
        $result = mysqli_stmt_get_result($stmt);

        // Check if a user with that username exists
        if (mysqli_num_rows($result) == 1) {
            // Fetch the user data
            $user = mysqli_fetch_assoc($result);

            // Compare the plain text password directly (no hashing)
            if ($password == $user['password']) {
                // Password is correct, login successful
                session_start();
                $_SESSION['superadmin_username'] = $username; // Set session variable for verification

                // Redirect to the superadmin dashboard
                header('Location: superadmin.php');
                exit(); // Ensure the script stops after redirection
            } else {
                // Invalid password
                echo "<script>alert('Invalid password. Please try again.');</script>";
            }
        } else {
            // No user found with the provided username
            echo "<script>alert('Invalid username. Please try again.');</script>";
        }

        // Close the statement and connection
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    } else {
        // Connection failed
        echo "Database connection error: " . mysqli_connect_error();
    }
} else {
    echo "Please fill out the form correctly.";
}
