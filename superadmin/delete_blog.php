<?php
require_once('../db_connection.php');
$conn = connect_to_db();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['blog_id'])) {
    $blog_id = intval($_POST['blog_id']);
    $query = "DELETE FROM blogs WHERE id = $blog_id";

    if (mysqli_query($conn, $query)) {
        echo "Blog deleted successfully.";
    } else {
        http_response_code(500);
        echo "Error deleting blog.";
    }
}
