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
    <title>Register New Club</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
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

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="./superadmin.php">Super Admin Dashboard</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#"><?php echo htmlspecialchars($admin_name); // Show the username 
                                                    ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
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
                <h2>Register New Club</h2>
                <form id="createClubForm" action="register_club.php" method="POST">
                    <div class="form-group">
                        <label for="clubName">Club Name</label>
                        <input type="text" class="form-control" name="clubName" id="clubName" placeholder="Enter club name" required>
                    </div>
                    <div class="form-group">
                        <label for="clubDescription">Description</label>
                        <textarea class="form-control" name="clubDescription" id="clubDescription" rows="3" placeholder="Enter club description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="clubCategory">Category</label>
                        <select class="form-control" id="clubCategoryEdit" name="clubCategory">
                            <option value="Sports">Sports</option>
                            <option value="Cultural">Cultural</option>
                            <option value="Social">Social</option>
                            <option value="Educational">Educational</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="clubStatus">Status</label>
                        <select class="form-control" name="clubStatus" id="clubStatus" required>
                            <option value="">Select status</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Register Club</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>