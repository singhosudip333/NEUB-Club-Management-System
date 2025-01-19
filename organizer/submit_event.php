<?php
// Start the session to access session variables
session_start();

// Check if the session variables for admin_id and club_id are set
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['club_id'])) {
    die("Organizer or club information is not available. Please ensure you're logged in.");
}

// Database connection parameters
$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "neub_club";

// Retrieve organizer and club info from session
$organizer_id = $_SESSION['admin_id'];  // Use admin_id as organizer_id
$club_id = $_SESSION['club_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $eventName = $_POST['eventName'];
    $eventDate = $_POST['eventDate'];
    $eventLocation = $_POST['eventLocation'];
    $eventDescription = $_POST['eventDescription'];

    try {
        // Create a new PDO instance
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $usernameDB, $passwordDB);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL insert statement
        $sql = "INSERT INTO events (name, date, location, description, status, organizer_id, club_id, created_at, updated_at)
                VALUES (:name, :date, :location, :description, 'Pending', :organizer_id, :club_id, NOW(), NOW())";

        $stmt = $pdo->prepare($sql);

        // Bind parameters to the prepared statement
        $stmt->bindParam(':name', $eventName);
        $stmt->bindParam(':date', $eventDate);
        $stmt->bindParam(':location', $eventLocation);
        $stmt->bindParam(':description', $eventDescription);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->bindParam(':club_id', $club_id);

        // Execute the statement
        $stmt->execute();

        // If event is created successfully, return a JavaScript alert and redirect
        echo "<script>
                alert('Event created successfully!');
                window.location.href = 'dashboard.php'; // Redirect to the dashboard
              </script>";
    } catch (PDOException $e) {
        // Handle any errors
        echo "<script>
                alert('Error: " . $e->getMessage() . "');
                window.location.href = './organizer/dashboard.php'; // Redirect to the dashboard
              </script>";
    }

    // Close the database connection
    $pdo = null;
}
