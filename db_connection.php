<?php
function connect_to_db() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "neub_club";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// Initialize the connection
$conn = connect_to_db();
?>
