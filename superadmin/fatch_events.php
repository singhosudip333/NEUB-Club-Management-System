<?php
require_once('../db_connection.php');
$conn = connect_to_db();

if (isset($_POST['club_id'])) {
  $club_id = intval($_POST['club_id']);
  $query = "SELECT id, name, date, location FROM events WHERE club_id = $club_id";
  $result = mysqli_query($conn, $query);

  if ($result) {
    if (mysqli_num_rows($result) > 0) {
      // Build event cards with delete button
      while ($row = mysqli_fetch_assoc($result)) {
        $event_card = "<div class='event-card'>
                          <h5>{$row['name']}</h5>
                          <p><strong>Date:</strong> {$row['date']}</p>
                          <p><strong>Location:</strong> {$row['location']}</p>
                          <button class='btn btn-danger' onclick='deleteEvent({$row['id']})'>Delete Event</button>
                        </div>";
        echo $event_card;
      }
    } else {
      // No events found
      echo '<p>No events available for this club.</p>';
    }
  } else {
    echo '<p>Error fetching events from the database.</p>';
  }
} else {
  echo '<p>Club ID not specified.</p>';
}
?>