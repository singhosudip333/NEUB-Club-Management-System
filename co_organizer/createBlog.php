<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("Access Denied. Please log in first.");
}

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

// Get the logged-in admin ID and club ID from session
$admin_id = $_SESSION['admin_id'];
$club_id = $_SESSION['club_id'];

// Validate if the admin exists in the admins table
$check_query = "SELECT id FROM admins WHERE id = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Invalid admin ID. Admin not found in the system.");
}
$stmt->close();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Handle photo upload
    $image_path = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $upload_dir = 'uploads/';
        $file_name = basename($_FILES['photo']['name']);
        $upload_path = $upload_dir . $file_name;

        // Move the uploaded file to the server's uploads directory
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
            $image_path = $upload_path;
        } else {
            die("File upload failed.");
        }
    }

    // Insert the blog into the blogs table
    $insert_query = "INSERT INTO blogs (title, content, author_id, club_id, image_path, created_at, updated_at) 
                     VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt_insert = $conn->prepare($insert_query);
    $stmt_insert->bind_param("ssiss", $title, $content, $admin_id, $club_id, $image_path);

    // Check for insertion success
    if ($stmt_insert->execute()) {
        // Success, redirect with a message
        echo "<script>
                alert('Blog post created successfully!');
                window.location.href = 'dashboard.php'; // Redirect to the dashboard
              </script>";
    } else {
        echo "<script>
                alert('Error: " . $stmt_insert->error . "');
              </script>";
    }

    $stmt_insert->close();
}

// Close connection
$conn->close();
