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
    <title>Approve Event Permissions</title>
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

        .table {
            margin-top: 20px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="dashboard.php">Approve Event Permissions</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="#"><?php echo htmlspecialchars($admin_name); ?></a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container content">
        <h2>Pending Event Requests</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Requested By</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Event rows will be loaded here dynamically -->
            </tbody>
        </table>
    </div>

    <script>
        // Load events from the database on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadEvents();
        });

        function loadEvents() {
            $.ajax({
                url: 'fatch_event.php',
                method: 'GET',
                success: function(response) {
                    document.querySelector('tbody').innerHTML = response;
                },
                error: function() {
                    alert('Error loading events.');
                }
            });
        }

        function approveEvent(eventId) {
            updateEventStatus(eventId, 'approve');
        }

        function rejectEvent(eventId) {
            updateEventStatus(eventId, 'reject');
        }

        function updateEventStatus(eventId, action) {
            $.ajax({
                url: 'update_event_status.php',
                type: 'POST',
                data: {
                    event_id: eventId,
                    action: action
                },
                success: function(response) {
                    alert(response);
                    loadEvents(); // Reload events to reflect status change
                },
                error: function() {
                    alert('Error updating event status.');
                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>