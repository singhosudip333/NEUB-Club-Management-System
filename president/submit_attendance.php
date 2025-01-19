<?php
session_start();
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eventSelect']) && isset($_POST['attendance'])) {
    $event_id = $_POST['eventSelect'];
    $attendance = $_POST['attendance']; // Contains member_id => status
    $club_id = $_SESSION['club_id']; // Assuming club_id is stored in session

    // Verify if attendance for this event has already been recorded
    $check_query = "SELECT 1 FROM attendance WHERE event_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $event_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Redirect back with error if attendance already exists
        $_SESSION['attendance_error'] = "Attendance for this event has already been recorded.";
        header("Location: attendance_form.php");
        exit();
    }

    // Insert attendance for each member
    $insert_query = "INSERT INTO attendance (event_id, admin_id, status) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);

    foreach ($attendance as $admin_id => $status) {
        $insert_stmt->bind_param("iis", $event_id, $admin_id, $status);
        $insert_stmt->execute();
    }

    $insert_stmt->close();
    $check_stmt->close();

    // Redirect back with success message
    $_SESSION['attendance_success'] = "Attendance successfully recorded!";
    header("Location: attendance_form.php");
    exit();
} else {
    // If accessed without POST, redirect to form
    header("Location: attendance_form.php");
    exit();
}

$conn->close();
