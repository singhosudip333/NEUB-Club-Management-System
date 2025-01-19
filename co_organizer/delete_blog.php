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

// Get the blog ID to delete
if (isset($_GET['id'])) {
    $blog_id = $_GET['id'];

    // Fetch the blog details to check if the admin is the author
    $sql_blog = "SELECT * FROM blogs WHERE id = ? AND author_id = ?";
    $stmt_blog = $conn->prepare($sql_blog);
    $stmt_blog->bind_param("ii", $blog_id, $admin_id);
    $stmt_blog->execute();
    $result_blog = $stmt_blog->get_result();
    $blog = $result_blog->fetch_assoc();

    if ($blog) {
        // Delete the blog from the database
        $delete_query = "DELETE FROM blogs WHERE id = ?";
        $stmt_delete = $conn->prepare($delete_query);
        $stmt_delete->bind_param("i", $blog_id);

        if ($stmt_delete->execute()) {
            // If there's an image associated with the blog, delete it from the server
            if ($blog['image_path'] && file_exists($blog['image_path'])) {
                unlink($blog['image_path']);
            }

            // Redirect to the blog management page
            header('Location: dashboard.php'); // Or wherever you want to redirect
            exit();
        } else {
            echo "<script>alert('Error deleting the blog post.'); window.location.href = 'dashboard.php';</script>";
        }

        $stmt_delete->close();
    } else {
        echo "<script>alert('Blog not found or you do not have permission to delete this blog.'); window.location.href = 'dashboard.php';</script>";
    }

    $stmt_blog->close();
} else {
    echo "<script>alert('Blog ID is required.'); window.location.href = 'dashboard.php';</script>";
}

// Close connection
$conn->close();
