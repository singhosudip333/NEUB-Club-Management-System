<?php
session_start();

// Check if the user is logged in and the session contains the admin's name
if (!isset($_SESSION['admin_name'])) {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit();
}

// Retrieve the admin's name from the session
$admin_name = $_SESSION['admin_name'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <!-- Bootstrap CSS for styling -->
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

        .content {
            padding: 20px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="dashboard.php">Organizer Dashboard</a>
    </nav>

    <!-- Main Content -->
    <div class="container content">
        <h2>Create New Event</h2>
        <form id="createEventForm" action="submit_event.php" method="POST">
            <div class="form-group">
                <label for="eventName">Event Name</label>
                <input type="text" class="form-control" id="eventName" name="eventName" placeholder="Enter event name" required>
            </div>
            <div class="form-group">
                <label for="eventDate">Event Date</label>
                <input type="date" class="form-control" id="eventDate" name="eventDate" required>
            </div>
            <div class="form-group">
                <label for="eventLocation">Event Location</label>
                <input type="text" class="form-control" id="eventLocation" name="eventLocation" placeholder="Enter event location" required>
            </div>
            <div class="form-group">
                <label for="eventDescription">Event Description</label>
                <textarea class="form-control" id="eventDescription" name="eventDescription" rows="4" placeholder="Enter event description" required></textarea>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Create Event</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>