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

// Fetch meeting details if an ID is provided
if (isset($_GET['id'])) {
    $meeting_id = $_GET['id'];

    // Fetch the meeting details from the database
    $sql = "SELECT * FROM meetings WHERE id = ? AND admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $meeting_id, $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $meeting = $result->fetch_assoc();
    } else {
        echo "Meeting not found or you do not have permission to edit this meeting.";
        exit();
    }
} else {
    echo "No meeting ID provided.";
    exit();
}

// Update the meeting details if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $meeting_link = $_POST['meeting_link'];

    $update_sql = "UPDATE meetings SET title = ?, date = ?, time = ?, meeting_link = ? WHERE id = ? AND admin_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssiii", $title, $date, $time, $meeting_link, $meeting_id, $admin_id);

    if ($update_stmt->execute()) {
        header("Location: manage_meetings.php"); // Redirect to the meetings list after update
        exit();
    } else {
        echo "Error: " . $update_stmt->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Meeting</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="manage_meetings.php">Back to Meetings</a>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <h2>Edit Meeting</h2>
        <form method="POST" action="edit_meeting.php?id=<?php echo htmlspecialchars($meeting_id); ?>">
            <div class="form-group">
                <label for="title">Meeting Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($meeting['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($meeting['date']); ?>" required>
            </div>
            <div class="form-group">
                <label for="time">Time</label>
                <input type="time" class="form-control" id="time" name="time" value="<?php echo htmlspecialchars($meeting['time']); ?>" required>
            </div>
            <div class="form-group">
                <label for="meeting_link">Meeting Link</label>
                <input type="texts" class="form-control" id="meeting_link" name="meeting_link" value="<?php echo htmlspecialchars($meeting['meeting_link']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Meeting</button>
        </form>
    </div>

</body>

</html>