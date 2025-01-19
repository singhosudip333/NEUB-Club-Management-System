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

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in admin ID from the session
$admin_id = $_SESSION['admin_id'];

// Fetch admin and club details
$sql = "SELECT admins.admin_name, clubs.club_name 
        FROM admins 
        JOIN clubs ON admins.club_id = clubs.id 
        WHERE admins.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin_data = $result->fetch_assoc();
    $admin_name = $admin_data['admin_name'];
    $club_name = $admin_data['club_name'];
} else {
    // Handle case if admin or club data is not found (optional)
    $admin_name = "Admin";
    $club_name = "Club";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Committee Admin Dashboard</title>
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

        .sidebar {
            background-color: #343a40;
            height: 100vh;
            padding-top: 20px;
        }

        .sidebar .nav-link {
            color: #ffffff;
            margin: 10px 0;
        }

        .sidebar .nav-link:hover {
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
        <a class="navbar-brand" href="dashboard.php">Committee Admin Dashboard</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto d-flex align-items-center">
                <li class="nav-item mr-2">
                    <button type="button" class="btn btn-info"><?php echo $admin_name; ?></button>
                </li>
                <li class="nav-item mr-2">
                    <button type="button" class="btn btn-success"><?php echo $club_name; ?></button>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>

    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 sidebar">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./add_committee_member.php">Add Committee Members</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./edit_committee.php">Edit Committee</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./view_participation.php">View Participation</a>
                    </li>
                </ul>
            </nav>

            <!-- Main Content Area -->
            <main class="col-md-10 content">
                <h3>Welcome <b><?php echo $admin_name; ?></b> to <b><?php echo $club_name; ?></b> Admin Dashboard</h3> <br><!-- Dynamic admin name and club -->

                <!-- Dashboard Overview (for future use) -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Members</h5>
                                <p class="card-text">50 Members</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Events Participated</h5>
                                <p class="card-text">8 Events</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Notifications Sent</h5>
                                <p class="card-text">15 Notifications</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.com/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>