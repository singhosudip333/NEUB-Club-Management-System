<?php
// Include the database connection file
include 'db_connection.php'; // Adjust the path if necessary

// Get the search query
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Prepare and execute the SQL statement
$sql = "SELECT * FROM clubs WHERE club_name LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $query . "%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// Display results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='club'>";
        echo "<h3>" . htmlspecialchars($row['club_name']) . "</h3>"; // Use htmlspecialchars to prevent XSS
        echo "<p>" . htmlspecialchars($row['description']) . "</p>"; // Use htmlspecialchars to prevent XSS
        echo "</div>";
    }
} else {
    echo "No clubs found.";
}

$stmt->close();
$conn->close();
?>