<?php
session_start(); // Start the session
include 'db_connection.php';
$conn = connect_to_db();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $blogId = $_POST['blog_id'];
    $commentText = $_POST['comment'];
    $userId = $_SESSION['user_id']; // Replace with the actual logged-in user ID
    $clubId = $_POST['club_id'];

    // Check if the club ID exists in the clubs table
    $checkClubQuery = "SELECT id FROM clubs WHERE id = ?";
    $checkClubStmt = $conn->prepare($checkClubQuery);
    $checkClubStmt->bind_param("i", $clubId);
    $checkClubStmt->execute();
    $checkClubResult = $checkClubStmt->get_result();

    if ($checkClubResult->num_rows > 0) {
        // Club ID exists, proceed with comment insertion
        $stmt = $conn->prepare("INSERT INTO comments (blog_id, club_id, user_id, comment_text, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiis", $blogId, $clubId, $userId, $commentText);

        if ($stmt->execute()) {
            header("Location: blog_details.php?id=" . $blogId);
            exit();
        } else {
            echo "Error adding comment: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Club ID doesn't exist, handle the error
        echo "Error: Invalid club ID.";
    }

    $checkClubStmt->close();
}
$conn->close();
