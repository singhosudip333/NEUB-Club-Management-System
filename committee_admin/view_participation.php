<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "neub_club";

// Create connection
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in admin ID from the session
$admin_id = $_SESSION['admin_id'];

// Fetch events for the club
$sql_events = "SELECT e.id, e.name 
                FROM events e
                INNER JOIN clubs c ON e.club_id = c.id
                INNER JOIN admins a ON c.id = a.club_id
                WHERE a.id = ?";

$stmt_events = $conn->prepare($sql_events);

if (!$stmt_events) {
    die("SQL preparation error: " . $conn->error);
}

$stmt_events->bind_param("i", $admin_id);
$stmt_events->execute();

if ($stmt_events->error) {
    die("SQL execution error: " . $stmt_events->error);
}

$result_events = $stmt_events->get_result();

// Check if events are retrieved
if ($result_events->num_rows === 0) {
    $events_message = "No events found for this admin.";
} else {
    $events_message = "Events successfully fetched.";
}

// Prepare attendance data
$attendance_data = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eventSelect'])) {
    $event_id = $_POST['eventSelect'];

    // Fetch attendance records for the selected event including position
    $sql_attendance = "SELECT admins.admin_name, attendance.status, roles.role_name AS position 
                       FROM attendance 
                       JOIN admins ON attendance.admin_id = admins.id 
                       JOIN roles ON admins.role_id = roles.id
                       WHERE attendance.event_id = ?";
    $stmt_attendance = $conn->prepare($sql_attendance);

    if (!$stmt_attendance) {
        die("SQL preparation error: " . $conn->error);
    }

    $stmt_attendance->bind_param("i", $event_id);
    $stmt_attendance->execute();

    if ($stmt_attendance->error) {
        die("SQL execution error: " . $stmt_attendance->error);
    }

    $result_attendance = $stmt_attendance->get_result();

    // Store attendance records
    while ($row = $result_attendance->fetch_assoc()) {
        $attendance_data[] = $row;
    }

    $stmt_attendance->close();
}

$stmt_events->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Participation</title>
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

        .table th,
        .table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="dashboard.php">Committee Admin Dashboard</a>
    </nav>

    <div class="container content">
        <h2>View Participation</h2>

        <!-- Display events fetching status -->
        <div class="alert alert-info">
            <?php echo htmlspecialchars($events_message); ?>
        </div>

        <form action="view_participation.php" method="POST">
            <div class="form-group">
                <label for="eventSelect">Select Event</label>
                <select class="form-control" id="eventSelect" name="eventSelect" required>
                    <option value="">Select an event</option>
                    <?php while ($event = $result_events->fetch_assoc()): ?>
                        <option value="<?php echo $event['id']; ?>"><?php echo $event['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="text-center mb-4">
                <button type="submit" class="btn btn-primary">View Participation</button>
            </div>
        </form>

        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Member Name</th>
                    <th scope="col">Position</th>
                    <th scope="col">Participation Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance_data as $attendance): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($attendance['admin_name']); ?></td>
                        <td><?php echo htmlspecialchars($attendance['position']); ?></td>
                        <td><?php echo htmlspecialchars($attendance['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>