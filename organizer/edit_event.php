<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_name']) || !isset($_SESSION['club_id']) || !isset($_SESSION['admin_id'])) {
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

// Initialize event variables
$eventId = '';
$eventName = '';
$eventDate = '';
$eventLocation = '';
$eventDescription = '';

// Check if the form is submitted to update the event
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $eventId = $_POST['eventId'];
    $eventName = $_POST['eventName'];
    $eventDate = $_POST['eventDate'];
    $eventLocation = $_POST['eventLocation'];
    $eventDescription = $_POST['eventDescription'];

    try {
        // Create a new PDO instance
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $usernameDB, $passwordDB);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL update statement
        $sql = "UPDATE events SET name = :name, date = :date, location = :location, description = :description, updated_at = NOW()
                WHERE id = :eventId AND club_id = :club_id";

        $stmt = $pdo->prepare($sql);

        // Bind parameters to the prepared statement
        $stmt->bindParam(':name', $eventName);
        $stmt->bindParam(':date', $eventDate);
        $stmt->bindParam(':location', $eventLocation);
        $stmt->bindParam(':description', $eventDescription);
        $stmt->bindParam(':eventId', $eventId);
        $stmt->bindParam(':club_id', $club_id);

        // Execute the statement
        $stmt->execute();

        // Redirect or display a success message
        echo "<script>alert('Event updated successfully!'); window.location.href='dashboard.php';</script>";
    } catch (PDOException $e) {
        // Handle any errors
        echo "Error: " . $e->getMessage();
    }

    // Close the database connection
    $pdo = null;
}

// Check if an event ID is provided for editing
if (isset($_GET['eventId'])) {
    $eventId = $_GET['eventId'];

    try {
        // Create a new PDO instance
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $usernameDB, $passwordDB);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL select statement
        $sql = "SELECT name, date, location, description FROM events WHERE id = :eventId AND club_id = :club_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':eventId', $eventId);
        $stmt->bindParam(':club_id', $club_id);
        $stmt->execute();

        // Fetch the event data
        if ($stmt->rowCount() > 0) {
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            $eventName = $event['name'];
            $eventDate = $event['date'];
            $eventLocation = $event['location'];
            $eventDescription = $event['description'];
        } else {
            echo "<script>alert('No event found with this ID.'); window.location.href='dashboard.php';</script>";
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Close the database connection
    $pdo = null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
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
        <a class="navbar-brand" href="dashboard.php">Organizer Dashboard</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#"><?php echo htmlspecialchars($organizer_name); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container content">
        <h2>Edit Event</h2>
        <form id="editEventForm" action="edit_event.php" method="POST">
            <div class="form-group">
                <label for="eventId">Event ID</label>
                <input type="text" class="form-control" name="eventId" value="<?php echo htmlspecialchars($eventId); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="eventName">Event Name</label>
                <input type="text" class="form-control" name="eventName" value="<?php echo htmlspecialchars($eventName); ?>" required>
            </div>
            <div class="form-group">
                <label for="eventDate">Event Date</label>
                <input type="date" class="form-control" name="eventDate" value="<?php echo htmlspecialchars($eventDate); ?>" required>
            </div>
            <div class="form-group">
                <label for="eventLocation">Event Location</label>
                <input type="text" class="form-control" name="eventLocation" value="<?php echo htmlspecialchars($eventLocation); ?>" required>
            </div>
            <div class="form-group">
                <label for="eventDescription">Event Description</label>
                <textarea class="form-control" name="eventDescription" rows="4" required><?php echo htmlspecialchars($eventDescription); ?></textarea>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update Event</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>