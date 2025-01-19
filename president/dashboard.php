<?php
// Start the session
session_start();

// Check if the user is logged in and has an admin name in the session
if (!isset($_SESSION['admin_name'])) {
    // Redirect to login page if the session is not set
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['admin_name'];  // Retrieve the admin's name
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>President Dashboard</title>
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

        .card {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="dashboard.php">President Dashboard</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#"><?php echo htmlspecialchars($admin_name); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container content">
        <!-- Display the admin name from session -->
        <h2>Welcome, <?php echo htmlspecialchars($admin_name); ?></h2><br>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Approve Event Permissions
                    </div>
                    <div class="card-body">
                        <p>View and approve pending event requests from the Organizer</p>
                        <a href="event_list.php" class="btn btn-primary">Manage Events</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Attendance
                    </div>
                    <div class="card-body">
                        <p>Fill out the attendance form for committee members at events.</p>
                        <a href="attendance_form.php" class="btn btn-primary">Fill Attendance</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Meeting Management
                    </div>
                    <div class="card-body">
                        <p>Schedule and manage committee meetings.</p>
                        <a href="manage_meetings.php" class="btn btn-primary">Manage Meetings</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>