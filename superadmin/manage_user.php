<?php
session_start(); // Start the session

require_once('../db_connection.php'); // Include your database connection file
$conn = connect_to_db(); // Connect to the database

// Check if the admin is logged in
if (!isset($_SESSION['superadmin_username'])) {
    // Redirect to login page if not logged in
    header("Location: superadmin_login.php");
    exit();
}

$admin_name = $_SESSION['superadmin_username']; // Retrieve the logged-in admin's username

// Handle user deletion if a delete request is made
if (isset($_POST['delete_user'])) {
    $studentId = $_POST['student_id'];
    $deleteQuery = "DELETE FROM users WHERE student_id = ?";
    $deleteStmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($deleteStmt, "s", $studentId);

    if (mysqli_stmt_execute($deleteStmt)) {
        echo "<script>alert('User deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error deleting user: " . mysqli_error($conn) . "');</script>";
    }
    mysqli_stmt_close($deleteStmt); // Close the prepared statement
}

// Fetch all users
$query = "SELECT name, department, student_id, session FROM users";
$result = mysqli_query($conn, $query);
$users = []; // Initialize an array to hold user data
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row; // Add each user to the array
    }
}

mysqli_close($conn); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #343a40;
        }

        .navbar-brand,
        .nav-link {
            color: #fff !important;
        }

        .nav-link:hover {
            color: #f8f9fa;
            background-color: #495057;
            border-radius: 5px;
        }

        .content {
            padding: 20px;
        }

        .sidebar {
            height: 200vh;
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

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="./superadmin.php">Super Admin Dashboard</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#"><?php echo htmlspecialchars($admin_name); // Show the admin's username 
                                                    ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a> <!-- Link to log out the admin -->
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>
            <!-- Main Content Area -->
            <div class="col-md-10 content">
                <h2>User Management</h2>

                <!-- Search Bar -->
                <div class="mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search by Student ID" onkeyup="searchUser()" />
                </div>

                <!-- User Table -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Name</th> <!-- Name column: 25% -->
                            <th style="width: 20%;">Department</th> <!-- Department column: 20% -->
                            <th style="width: 25%;">ID</th> <!-- ID column: 25% -->
                            <th style="width: 15%;">Section</th> <!-- Section column: 15% -->
                            <th style="width: 15%;">Action</th> <!-- Action column: 15% -->
                        </tr>
                    </thead>
                    <tbody id="usersContainer">
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['name']; ?></td>
                                <td><?php echo $user['department']; ?></td>
                                <td><?php echo $user['student_id']; ?></td>
                                <td><?php echo $user['session']; ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="student_id" value="<?php echo $user['student_id']; ?>">
                                        <button type="submit" name="delete_user" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete User</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Function to search for a user by ID
        function searchUser() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const usersContainer = document.getElementById('usersContainer');
            const rows = usersContainer.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                const studentIdCell = cells[2]; // Adjust index based on your columns

                if (studentIdCell) {
                    const studentIdText = studentIdCell.textContent || studentIdCell.innerText;
                    rows[i].style.display = studentIdText.toLowerCase().includes(searchValue) ? "" : "none"; // Show or hide rows based on search
                }
            }
        }
    </script>
</body>

</html>