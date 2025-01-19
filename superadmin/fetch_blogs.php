<?php
require_once('../db_connection.php');
$conn = connect_to_db();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['club_id'])) {
    $club_id = intval($_POST['club_id']);
    $query = "SELECT * FROM blogs WHERE club_id = $club_id ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($blog = mysqli_fetch_assoc($result)) {
            echo "<div class='blog-card'>";
            echo "<h5 class='mb-3'>" . htmlspecialchars($blog['title']) . "</h5>";
            if ($blog['image_path']) {
                echo "<img src='../co_organizer/" . htmlspecialchars($blog['image_path']) . "' alt='Blog Image' class='blog-image'>";
            }
            echo "<p class='flex-grow-1'>" . substr(htmlspecialchars($blog['content']), 0, 150) . "...</p>";
            echo "<div class='mt-auto'>";
            echo "<small class='text-muted d-block mb-2'>Posted on: " . date('M d, Y', strtotime($blog['created_at'])) . "</small>";
            echo "<button class='btn btn-danger' onclick='deleteBlog(" . $blog['id'] . ")'>Delete Blog</button>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>No blogs available for this club.</p>";
    }
}
?>
