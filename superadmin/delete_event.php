<?php
require_once('../db_connection.php');
$conn = connect_to_db();

if (isset($_POST['event_id'])) {
    $event_id = intval($_POST['event_id']);
    $query = "DELETE FROM events WHERE id = $event_id";
    if (mysqli_query($conn, $query)) {
        echo 'Event deleted successfully';
    } else {
        echo 'Error deleting event';
    }
}
?>
