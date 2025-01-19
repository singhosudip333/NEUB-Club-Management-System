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

// Fetch admins who are members of the club associated with the logged-in user
$club_id = $_SESSION['club_id']; // Assuming club_id is stored in session upon login

$sql = "SELECT id, admin_name FROM admins WHERE club_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $club_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch events excluding those with recorded attendance
$event_query = "
    SELECT e.id, e.name 
    FROM events e 
    LEFT JOIN attendance a ON e.id = a.event_id 
    WHERE e.club_id = ? AND a.event_id IS NULL
";
$event_stmt = $conn->prepare($event_query);
$event_stmt->bind_param("i", $club_id);
$event_stmt->execute();
$event_result = $event_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Form</title>
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
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="dashboard.php">Attendance Form</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Home</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="card mt-3">
            <div class="card-body">
                <h2 class="my-4">Fill Attendance for Event</h2>

                <!-- Display Success/Error Messages -->
                <?php
                if (isset($_SESSION['attendance_success'])) {
                    echo "<div class='alert alert-success'>" . $_SESSION['attendance_success'] . "</div>";
                    unset($_SESSION['attendance_success']);
                }
                if (isset($_SESSION['attendance_error'])) {
                    echo "<div class='alert alert-danger'>" . $_SESSION['attendance_error'] . "</div>";
                    unset($_SESSION['attendance_error']);
                }
                ?>

                <form id="attendanceForm" action="submit_attendance.php" method="POST">
                    <div class="form-group">
                        <label for="eventSelect">Select Event</label>
                        <select class="form-control" name="eventSelect" id="eventSelect" required>
                            <option value="">Select an event</option>
                            <?php
                            while ($event = $event_result->fetch_assoc()) {
                                echo "<option value='{$event['id']}'>" . htmlspecialchars($event['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Member Name</th>
                                <th>Attendance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($admin = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($admin['admin_name']) . "</td>
                                        <td>
                                            <select class='form-control' name='attendance[{$admin['id']}]' required>
                                                <option value=''>Select Status</option>
                                                <option value='Present'>Present</option>
                                                <option value='Absent'>Absent</option>
                                            </select>
                                        </td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">Submit Attendance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>