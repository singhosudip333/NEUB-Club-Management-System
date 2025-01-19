<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_name']) || !isset($_SESSION['club_id']) || !isset($_SESSION['admin_id'])) {
    // Redirect to login page if session variables are not set
    header("Location: login.php");
    exit();
}

$organizer_name = $_SESSION['admin_name'];
$club_id = $_SESSION['club_id'];
$organizer_id = $_SESSION['admin_id'];

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

// Fetch upcoming events
$upcomingEvents = [];
$sqlUpcoming = "SELECT * FROM events WHERE date >= CURDATE() AND club_id = ? ORDER BY date ASC";
$stmt = $conn->prepare($sqlUpcoming);
$stmt->bind_param("i", $club_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $upcomingEvents[] = $row;
}
$stmt->close();

// Fetch previous events (not restricted to 'Approved' status for debugging)
// Fetch previous approved events
$previousEvents = [];
$sqlPrevious = "SELECT * FROM events WHERE date < NOW() AND status = 'Approved' AND club_id = ? ORDER BY date DESC";
$stmt = $conn->prepare($sqlPrevious);
$stmt->bind_param("i", $club_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $previousEvents[] = $row;
}
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #343a40;
        }

        .navbar-brand,
        .nav-link {
            color: #fff !important;
        }

        .nav-link:hover {
            color: #f8f9fa;
            background-color: #495057;
            border-radius: 5px;
        }

        .content {
            padding: 20px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">Organizer Dashboard</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#"><?php echo htmlspecialchars($organizer_name); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container content">
        <h2>Welcome, <?php echo htmlspecialchars($organizer_name); ?></h2><br>


        <h3>Event Management</h3>
        <div class="mb-4">
            <a href="create_event.php" class="btn btn-primary">Create New Event</a>
        </div>

        <h3>Upcoming Events</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($upcomingEvents)): ?>
                    <tr>
                        <td colspan="4">No upcoming events</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($upcomingEvents as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['name']); ?></td>
                            <td><?php echo htmlspecialchars($event['date']); ?></td>
                            <td><?php echo htmlspecialchars($event['status']); ?></td>
                            <td>
                                <a href="edit_event.php?eventId=<?php echo $event['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_event.php?eventId=<?php echo $event['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('<?php echo htmlspecialchars($event['name']); ?>');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <h3>Previous Approved Events</h3>
        <?php if (empty($previousEvents)): ?>
            <p>No previous approved events</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($previousEvents as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['name']); ?></td>
                            <td><?php echo htmlspecialchars($event['date']); ?></td>
                            <td><?php echo htmlspecialchars($event['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function confirmDelete(eventName) {
            return confirm(`Are you sure you want to delete "${eventName}"? This action cannot be undone.`);
        }
    </script>
</body>

</html>