<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['superadmin_username'])) {
    header('Location: superadmin_login.php');
    exit();
}

// Database connection parameters
$servername = "localhost"; // Your database server
$usernameDB = "root"; // Your database username
$passwordDB = ""; // Your database password
$dbname = "neub_club"; // Your database name

// Create connection to the database
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the count of active clubs
$sql_clubs = "SELECT COUNT(*) as count FROM clubs WHERE club_status = 'active'";
$result_clubs = $conn->query($sql_clubs);

if ($result_clubs) { // Check if the query was successful
    $active_clubs_count = $result_clubs->fetch_assoc()['count'];
} else {
    die("Error fetching active clubs: " . $conn->error); // Output error message
}

// Fetch the count of upcoming events
$sql_events = "SELECT COUNT(*) as count FROM events WHERE date > NOW()";
$result_events = $conn->query($sql_events);

if ($result_events) { // Check if the query was successful
    $upcoming_events_count = $result_events->fetch_assoc()['count'];
} else {
    die("Error fetching upcoming events: " . $conn->error); // Output error message
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
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

        .sidebar {
            height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
        }

        .sidebar a {
            color: white;
            padding: 15px;
            display: block;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .content {
            padding: 20px;
        }

        .card-header {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">Super Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#"><?php echo htmlspecialchars($_SESSION['superadmin_username']); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Layout -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <a href="./manageclub.php">Manage Clubs</a>
                <a href="./add_commiteeadmin.php">Add Club Admin</a>
                <a href="./manage_post.php">Manage Posts</a>
                <a href="./manage_event.php">Manage Events</a>
                <a href="./view_report.php">View Reports</a>
                <a href="./manage_user.php">User Management</a>
            </div>

            <!-- Main Content Area -->
            <div class="col-md-10 content">
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">Clubs</div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $active_clubs_count; ?> Active Clubs</h5>
                                <a href="manageclub.php" class="btn btn-info">Manage Clubs</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">Events</div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $upcoming_events_count; ?> Upcoming Events</h5>
                                <a href="manage_event.php" class="btn btn-info">Manage Events</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">Reports</div>
                            <div class="card-body">
                                <h5 class="card-title">View Analytics</h5>
                                <a href="view_report.php" class="btn btn-info">View Reports</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>