<?php
require_once('../db_connection.php');
$conn = connect_to_db();

if (isset($_POST['event_id']) && isset($_POST['action'])) {
    $event_id = intval($_POST['event_id']);
    $status = ($_POST['action'] == 'approve') ? 'Approved' : 'Rejected';

    $sql = "UPDATE events SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $event_id);

    if ($stmt->execute()) {
        echo "Event successfully {$status}.";
    } else {
        echo "Error updating event status.";
    }

    $stmt->close();
}

$conn->close();
