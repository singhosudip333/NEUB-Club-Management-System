<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Update with your database password
$dbname = "neub_club"; // Update with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the club_id associated with the logged-in admin from the admins table
$club_id_query = "SELECT club_id FROM admins WHERE id = ?";
$club_stmt = $conn->prepare($club_id_query);
$club_stmt->bind_param("i", $admin_id);
$club_stmt->execute();
$club_result = $club_stmt->get_result();

if ($club_result->num_rows > 0) {
    $club_row = $club_result->fetch_assoc();
    $club_id = $club_row['club_id']; // Get the club_id associated with this admin

    // If form is submitted, insert new meeting
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = $_POST['title'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $meeting_link = $_POST['meeting_link'];

        // Insert new meeting with the fetched club_id
        $sql = "INSERT INTO meetings (title, date, time, meeting_link, club_id, admin_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssii", $title, $date, $time, $meeting_link, $club_id, $admin_id);

        if ($stmt->execute()) {
            header("Location: manage_meetings.php"); // Redirect to the meeting list
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
} else {
    echo "No club found for this admin."; // Error message if no club is associated with the admin
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule New Meeting</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="manage_meetings.php">Back to Meetings</a>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <h2>Schedule a New Meeting</h2>
        <form method="POST" action="schedule_meeting.php">
            <div class="form-group">
                <label for="title">Meeting Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="time">Time</label>
                <input type="time" class="form-control" id="time" name="time" required>
            </div>
            <div class="form-group">
                <label for="meeting_link">Meeting Link</label>
                <input type="text" class="form-control" id="meeting_link" name="meeting_link" required>
            </div>
            <button type="submit" class="btn btn-primary">Schedule Meeting</button>
        </form>
    </div>

</body>

</html>