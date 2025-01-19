<?php
require_once('../db_connection.php');
$conn = connect_to_db();

$sql = "SELECT events.id, events.name, events.date, admins.admin_name AS requested_by, events.status 
        FROM events 
        JOIN admins ON events.organizer_id = admins.id 
        WHERE events.status = 'Pending'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['date']}</td>
                <td>{$row['requested_by']}</td>
                <td>{$row['status']}</td>
                <td>
                    <button class='btn btn-success' onclick='approveEvent({$row['id']})'>Approve</button>
                    <button class='btn btn-danger' onclick='rejectEvent({$row['id']})'>Reject</button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6'>No pending events found.</td></tr>";
}

$conn->close();
