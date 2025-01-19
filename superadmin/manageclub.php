<?php
// Start session (if necessary)
session_start();

// Database connection setup
$servername = "localhost"; // Your database server name
$usernameDB = "root"; // Your database username (default for XAMPP is 'root')
$passwordDB = ""; // Your database password (default is empty for XAMPP)
$dbname = "neub_club"; // Your database name

// Create connection
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the database (use actual column names)
if (isset($_SESSION["superadmin_username"])) {
    $admin_name = $_SESSION["superadmin_username"];
} else {
    // Handle case where username is not in session (e.g., redirect to login)
}

// Update club information
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $club_id = $_POST['club_id'];
    $club_name = $_POST['clubName'];
    $club_description = $_POST['clubDescription'];
    $club_category = $_POST['clubCategory'];
    $club_status = $_POST['clubStatus'];

    $update_sql = "UPDATE clubs SET club_name = ?, description = ?, club_category = ?, club_status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $club_name, $club_description, $club_category, $club_status, $club_id);

    if ($update_stmt->execute()) {
        echo '<script>alert("Club updated successfully!");</script>';
    } else {
        echo '<script>alert("Error updating club: ' . $conn->error . '");</script>';
    }

    $update_stmt->close();
}

// Fetch clubs data
$sql = "SELECT id, club_name, description, club_category, club_status FROM clubs";
$result = $conn->query($sql);
if (!$result) {
    die("Error fetching data: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Manage Clubs</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            background-color: #343a40;
        }

        .navbar-brand,
        .nav-link {
            color: #fff !important;
        }

        .nav-link:hover {
            background-color: #495057;
            border-radius: 5px;
        }

        .content {
            padding: 20px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .sidebar {
            height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
        }

        .sidebar a {
            color: white;
            padding: 15px;
            display: block;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .sidebar a.active {
            background-color: #495057;
            /* Change background color for active link */
            color: #fff;
            /* Change text color for active link */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="./superadmin.php">Super Admin Dashboard</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="#"><?php echo htmlspecialchars($admin_name); // Display admin name 
                                                                    ?></a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Layout -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>
            <!-- Main Content Area -->
            <div class="col-md-10 content">
                <h2>Manage Clubs</h2>
                <button class="btn btn-primary mb-3" onclick="window.location.href='create_club.php'">Create New Club</button><br><br>
                <table class="table table-striped table-hover" id="clubsTable">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Club Name</th>
                            <th scope="col">Category</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <th scope='row'>{$row["id"]}</th>
                                    <td>{$row["club_name"]}</td>
                                    <td>{$row["club_category"]}</td>
                                    <td><span class='badge " . ($row["club_status"] == "Active" ? "badge-success" : "badge-secondary") . "'>{$row["club_status"]}</span></td>
                                    <td>
                                        <button class='btn btn-info btn-sm' data-toggle='modal' data-target='#viewClubModal' onclick=\"viewClub('{$row["club_name"]}', '{$row["description"]}', '{$row["club_category"]}', '{$row["club_status"]}')\">View</button>
                                        <button class='btn btn-warning btn-sm' data-toggle='modal' data-target='#editClubModal' onclick=\"editClub('{$row["id"]}', '{$row["club_name"]}', '{$row["description"]}', '{$row["club_category"]}', '{$row["club_status"]}')\">Edit</button>
                                        <button class='btn btn-danger btn-sm' onclick=\"confirmDelete('{$row["id"]}', '{$row["club_name"]}')\">Delete</button>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No clubs found.</td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>

            <!-- View Club Modal -->
            <div class="modal fade" id="viewClubModal" tabindex="-1" role="dialog" aria-labelledby="viewClubModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewClubModalLabel">Club Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Club Name:</strong> <span id="clubName"></span></p>
                            <p><strong>Description:</strong> <span id="clubDescription"></span></p>
                            <p><strong>Category:</strong> <span id="clubCategory"></span></p>
                            <p><strong>Status:</strong> <span id="clubStatus"></span></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Club Modal -->
            <div class="modal fade" id="editClubModal" tabindex="-1" role="dialog" aria-labelledby="editClubModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editClubModalLabel">Edit Club</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="editClubForm" method="POST" action="edit_club.php">
                                <input type="hidden" name="club_id" id="clubId">
                                <div class="form-group">
                                    <label for="clubNameEdit">Club Name:</label>
                                    <input type="text" class="form-control" id="clubNameEdit" name="clubName" required>
                                </div>
                                <div class="form-group">
                                    <label for="clubDescriptionEdit">Description:</label>
                                    <textarea class="form-control" id="clubDescriptionEdit" name="clubDescription" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="clubCategoryEdit">Category:</label>
                                    <select class="form-control" id="clubCategoryEdit" name="clubCategory">
                                        <option value="Sports">Sports</option>
                                        <option value="Cultural">Cultural</option>
                                        <option value="Social">Social</option>
                                        <option value="Educational">Educational</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="clubStatusEdit">Status:</label>
                                    <select class="form-control" id="clubStatusEdit" name="clubStatus">
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="submitEditClubForm()">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function viewClub(name, description, category, status) {
            document.getElementById('clubName').innerText = name;
            document.getElementById('clubDescription').innerText = description;
            document.getElementById('clubCategory').innerText = category;
            document.getElementById('clubStatus').innerText = status;
        }

        function editClub(id, name, description, category, status) {
            document.getElementById('clubId').value = id;
            document.getElementById('clubNameEdit').value = name;
            document.getElementById('clubDescriptionEdit').value = description;
            document.getElementById('clubCategoryEdit').value = category;
            document.getElementById('clubStatusEdit').value = status;
        }

        function submitEditClubForm() {
            var form = document.getElementById('editClubForm');
            var formData = new FormData(form);
            formData.append('action', 'edit'); // Add this line

            fetch('edit_club.php', { // Change 'your_php_file.php' to your actual file name
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert(data); // Show the response message
                    window.location.reload(); // Reload the page to reflect changes
                })
                .catch(error => console.error('Error:', error));
        }

        function confirmDelete(clubId, clubName) {
            if (confirm("Are you sure you want to delete the club: " + clubName + "?")) {
                // Prepare the form data for the delete request
                var formData = new FormData();
                formData.append('club_id', clubId);
                formData.append('action', 'delete');

                // Send the delete request using fetch API
                fetch('delete_club.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert(data); // Show the response message
                        window.location.reload(); // Reload the page to reflect changes
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
    </script>
</body>

</html>