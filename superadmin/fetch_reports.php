<?php
session_start();

// Check if the super admin is logged in
if (!isset($_SESSION['superadmin_username'])) {
    // Redirect to the login page if the user is not logged in
    header('Location: superadmin_login.php');
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

// Get the club ID from POST data
$club_id = $_POST['club_id'];

// Fetch total members for the club
$sql_members = "SELECT COUNT(*) as total_members FROM admins WHERE club_id = ?";
$stmt_members = $conn->prepare($sql_members);
$stmt_members->bind_param("i", $club_id);
$stmt_members->execute();
$result_members = $stmt_members->get_result();
$members = $result_members->fetch_assoc()['total_members'];

// Fetch total events for the club
$sql_events = "SELECT COUNT(*) as total_events FROM events WHERE club_id = ?";
$stmt_events = $conn->prepare($sql_events);
$stmt_events->bind_param("i", $club_id);
$stmt_events->execute();
$result_events = $stmt_events->get_result();
$events = $result_events->fetch_assoc()['total_events'];

// Fetch total blogs for the club
$sql_blogs = "SELECT COUNT(*) as total_blogs FROM blogs WHERE club_id = ?";
$stmt_blogs = $conn->prepare($sql_blogs);
$stmt_blogs->bind_param("i", $club_id);
$stmt_blogs->execute();
$result_blogs = $stmt_blogs->get_result();
$blogs = $result_blogs->fetch_assoc()['total_blogs'];

// Return the data as JSON
echo json_encode([
    'members' => $members,
    'events' => $events,
    'posts' => $blogs,
]);

$conn->close();
?>
