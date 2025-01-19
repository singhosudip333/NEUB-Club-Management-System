<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session and check if user is logged in
session_start();
if (!isset($_SESSION['superadmin_username'])) {
    header('Location: superadmin_login.php');
    exit();
}
$admin_name = $_SESSION['superadmin_username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
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

        .event-card {
            border: 1px solid #ced4da;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background-color: #ffffff;
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

        .sidebar a.active {
            background-color: #495057;
            /* Change background color for active link */
            color: #fff;
            /* Change text color for active link */
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="./superadmin.php">Super Admin Dashboard</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#"><?php echo htmlspecialchars($admin_name); // Display admin name 
                                                    ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a> <!-- Ensure you have a logout.php file to handle session termination -->
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>
            <!-- Main Content Area -->
            <div class="col-md-10 content">
                <h2>Manage Events</h2>
                <form id="manageEventsForm">
                    <div class="form-group">
                        <label for="clubSelect">Select Club</label>
                        <select class="form-control" name="clubSelect" id="clubSelect" onchange="loadEvents()" required>
                            <option value="">Select a club</option>
                            <?php
                            // Connect to the database and fetch clubs
                            require_once('../db_connection.php');
                            $conn = connect_to_db();
                            $query = "SELECT id, club_name FROM clubs";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['id'] . "'>" . $row['club_name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </form>
                <div id="eventsContainer"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        // Function to load events based on selected club
        function loadEvents() {
            const clubSelect = document.getElementById('clubSelect');
            const clubId = clubSelect.value;
            const eventsContainer = document.getElementById('eventsContainer');

            // Clear existing events
            eventsContainer.innerHTML = '';

            // Fetch events for the selected club using AJAX
            if (clubId) {
                $.ajax({
                    url: 'fatch_events.php',
                    type: 'POST',
                    data: {
                        club_id: clubId
                    },
                    success: function(response) {
                        eventsContainer.innerHTML = response; // Display fetched events
                    },
                    error: function() {
                        eventsContainer.innerHTML = '<p>Error fetching events. Please try again.</p>';
                    }
                });
            }
        }

        // Function to delete an event
        function deleteEvent(eventId) {
            if (confirm("Are you sure you want to delete this event?")) {
                $.ajax({
                    url: 'delete_event.php',
                    type: 'POST',
                    data: {
                        event_id: eventId
                    },
                    success: function() {
                        loadEvents(); // Reload events to reflect changes
                    },
                    error: function() {
                        alert('Error deleting event. Please try again.');
                    }
                });
            }
        }
    </script>
</body>

</html>