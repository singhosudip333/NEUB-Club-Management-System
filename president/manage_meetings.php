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

// Fetch the club_id associated with the admin
$club_id_query = "SELECT club_id FROM admins WHERE id = ?";
$club_stmt = $conn->prepare($club_id_query);
$club_stmt->bind_param("i", $admin_id);
$club_stmt->execute();
$club_result = $club_stmt->get_result();

if ($club_result->num_rows > 0) {
    $club_row = $club_result->fetch_assoc();
    $club_id = $club_row['club_id'];

    // Fetch meetings for this club
    $meeting_query = "SELECT * FROM meetings WHERE club_id = ?";
    $meeting_stmt = $conn->prepare($meeting_query);
    $meeting_stmt->bind_param("i", $club_id);
    $meeting_stmt->execute();
    $meetings = $meeting_stmt->get_result();
} else {
    echo "No club associated with this admin.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Meetings</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="dashboard.php">President Dashboard</a>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <h2>Meeting Schedule</h2>
        <button class="btn btn-primary mb-3" onclick="location.href='schedule_meeting.php'">Schedule New Meeting</button>
        <div class="meeting-table">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Meeting ID</th>
                        <th scope="col">Meeting Title</th>
                        <th scope="col">Date</th>
                        <th scope="col">Time</th>
                        <th scope="col">Meeting Link</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($meeting = $meetings->fetch_assoc()) { ?>
                        <tr>
                            <th scope="row"><?php echo $meeting['id']; ?></th>
                            <td><?php echo htmlspecialchars($meeting['title']); ?></td>
                            <td><?php echo htmlspecialchars($meeting['date']); ?></td>
                            <td><?php echo htmlspecialchars($meeting['time']); ?></td>
                            <td><a href="<?php echo htmlspecialchars($meeting['meeting_link']); ?>" target="_blank">Join Meeting</a></td>
                            <td>
                                <!-- Edit button with meeting ID passed in the URL -->
                                <button class="btn btn-success" onclick="location.href='edit_meeting.php?id=<?php echo $meeting['id']; ?>'">Edit</button>
                                <button class="btn btn-danger" onclick="confirmDelete(<?php echo $meeting['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function confirmDelete(meetingId) {
            if (confirm('Are you sure you want to delete this meeting?')) {
                window.location.href = 'delete_meeting.php?id=' + meetingId;
            }
        }
    </script>

</body>

</html>