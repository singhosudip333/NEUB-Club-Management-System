<?php
// Start the session
session_start();

// Check if the user is logged in (assuming you set the username in the session during login)
if (!isset($_SESSION['superadmin_username'])) {
    // Redirect to the login page if the user is not logged in
    header('Location: superadmin_login.php');
    exit();
}

// Get the admin name from the session
$admin_name = $_SESSION['superadmin_username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Club Admin</title>
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

        .admin-form {
            border: 1px solid #ced4da;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background-color: #ffffff;
        }

        .table-container {
            margin-top: 30px;
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
                    <a class="nav-link" href="#"><?php echo htmlspecialchars($admin_name); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
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
                <h2>Add Club Admin</h2>
                <form id="addClubAdminForm" action="add_committee_admin.php" method="POST">
                    <div class="form-group">
                        <label for="clubSelect">Select Club</label>
                        <select class="form-control" name="club_id" id="clubSelect" required>
                            <option value="">Select a club</option>
                            <!-- Dynamic club options from the database -->
                            <?php
                            require_once('../db_connection.php');
                            $conn = connect_to_db();
                            $query = "SELECT id, club_name FROM clubs";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['id'] . "'>" . $row['club_name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="roleSelect">Select Role</label>
                        <select class="form-control" name="role_id" id="roleSelect" required>
                            <option value="">Select a role</option>
                            <?php
                            // Only show the 'Club Admin' role
                            $roleQuery = "SELECT id, role_name FROM roles WHERE role_name = 'Club Admin'";
                            $roleResult = mysqli_query($conn, $roleQuery);
                            while ($roleRow = mysqli_fetch_assoc($roleResult)) {
                                echo "<option value='" . $roleRow['id'] . "'>" . $roleRow['role_name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="admin-form">
                        <div class="form-group">
                            <label for="adminName">Admin Name</label>
                            <input type="text" class="form-control" name="admin_name" placeholder="Enter admin name" required>
                        </div>
                        <div class="form-group">
                            <label for="adminEmail">Admin Email</label>
                            <input type="email" class="form-control" name="admin_email" placeholder="Enter admin email" required>
                        </div>
                        <div class="form-group">
                            <label for="adminPassword">Admin Password</label>
                            <input type="password" class="form-control" name="admin_password" placeholder="Enter admin password" required>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary m-auto">Add Club Admin</button>
                    </div>
                </form>

                <!-- Club Admin List Table -->
                <div class="table-container">
                    <h3>Club Admins List</h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Club</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamic Club Admin Data from Database -->
                            <?php
                            $adminQuery = "SELECT admins.id, admins.admin_name, admins.admin_email, clubs.club_name, roles.role_name
                            FROM admins
                            JOIN clubs ON admins.club_id = clubs.id
                            JOIN roles ON admins.role_id = roles.id";

                            $adminResult = mysqli_query($conn, $adminQuery);
                            while ($adminRow = mysqli_fetch_assoc($adminResult)) {
                                echo "<tr>";
                                echo "<td>" . $adminRow['admin_name'] . "</td>";
                                echo "<td>" . $adminRow['club_name'] . "</td>";
                                echo "<td>" . $adminRow['admin_email'] . "</td>";
                                echo "<td>" . $adminRow['role_name'] . "</td>";
                                echo "<td>
                                        <form action='delete_admin.php' method='POST' style='display:inline-block;'>
                                            <input type='hidden' name='admin_id' value='" . $adminRow['id'] . "'>
                                            <button type='submit' class='btn btn-danger btn-sm' onclick='return confirmDelete()'>Delete</button>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.com/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this admin?");
        }
    </script>

</body>

</html>