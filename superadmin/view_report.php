<?php
session_start();

// Check if the super admin is logged in
if (!isset($_SESSION['superadmin_username'])) {
    // Redirect to the login page if the user is not logged in
    header('Location: superadmin_login.php');
    exit();
}

// Database connection parameters
$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "neub_club";

// Create connection to the database
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the list of clubs from the admins table and join with the clubs table
$sql_clubs = "SELECT DISTINCT admins.club_id, clubs.club_name FROM admins 
              JOIN clubs ON admins.club_id = clubs.id";
$result_clubs = $conn->query($sql_clubs);
$clubs = []; // Initialize an array to hold club data
while ($row = $result_clubs->fetch_assoc()) {
    $clubs[] = $row; // Add each club to the array
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reports</title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            /* Light background color */
        }

        .navbar {
            background-color: #343a40;
            /* Dark navbar */
        }

        .navbar-brand,
        .nav-link {
            color: #fff !important;
            /* White text for navbar items */
        }

        .nav-link:hover {
            color: #f8f9fa;
            /* Light text on hover */
            background-color: #495057;
            /* Darker background on hover */
            border-radius: 5px;
            /* Rounded corners */
        }

        .content {
            padding: 20px;
            /* Padding for main content */
        }

        .report-card {
            border: 1px solid #ced4da;
            /* Border for report cards */
            padding: 15px;
            /* Padding inside report cards */
            margin-bottom: 15px;
            /* Space between report cards */
            border-radius: 5px;
            /* Rounded corners */
            background-color: #ffffff;
            /* White background for report cards */
        }

        .sidebar {
            height: 100vh;
            /* Full height sidebar */
            background-color: #343a40;
            /* Dark sidebar */
            padding-top: 20px;
            /* Padding at the top */
        }

        .sidebar a {
            color: white;
            /* White text for sidebar links */
            padding: 15px;
            /* Padding for sidebar links */
            display: block;
            /* Block display for links */
            text-decoration: none;
            /* No underline for links */
        }

        .sidebar a:hover {
            background-color: #495057;
            /* Darker background on hover */
        }

        .sidebar a.active {
            background-color: #495057;
            /* Active link background color */
            color: #fff;
            /* Active link text color */
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="./superadmin.php">Super Admin Dashboard</a>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>
            <!-- Main Content Area -->
            <div class="col-md-10 content">
                <h2>View Reports</h2><br>
                <form id="viewReportsForm" action="view_reports.php" method="POST">
                    <div class="form-group">
                        <label for="clubSelect">Select Club</label>
                        <select class="form-control" name="clubSelect" id="clubSelect" onchange="loadReports()" required>
                            <option value="">Select a club</option>
                            <?php foreach ($clubs as $club): ?>
                                <option value="<?php echo $club['club_id']; ?>">
                                    <?php echo $club['club_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
                <div id="reportsContainer"></div> <!-- Container for reports -->
            </div>
        </div>
    </div>

    <!-- Modal for displaying reports -->
    <div class="modal fade" id="reportsModal" tabindex="-1" role="dialog" aria-labelledby="reportsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportsModalLabel">Reports</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- Report details will be injected here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Function to load reports for the selected club
        function loadReports() {
            const clubSelect = document.getElementById('clubSelect');
            const selectedClub = clubSelect.value; // Get selected club ID
            const selectedClubName = clubSelect.options[clubSelect.selectedIndex].text; // Get selected club name
            console.log(selectedClub, selectedClubName); // Log both the selected club ID and club name

            if (selectedClub) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "fetch_reports.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        const data = JSON.parse(xhr.responseText);
                        console.log(data); // Log the fetched data

                        const modalBody = document.getElementById('modalBody');
                        modalBody.innerHTML = ''; // Clear previous content

                        if (data) {
                            modalBody.innerHTML = `
                                <h5>Reports for ${selectedClubName}</h5><br>
                                <p><strong>Total Committee Members:</strong> ${data.members}</p>
                                <p><strong>Total Events:</strong> ${data.events}</p>
                                <p><strong>Total Posts:</strong> ${data.posts}</p>
                            `;
                            $('#reportsModal').modal('show'); // Show the modal
                        } else {
                            modalBody.innerHTML = '<p>No reports available for this club.</p>';
                            $('#reportsModal').modal('show'); // Show the modal
                        }
                    }
                };
                xhr.send("club_id=" + selectedClub); // Send the selected club ID to the server
            }
        }
    </script>
</body>

</html>