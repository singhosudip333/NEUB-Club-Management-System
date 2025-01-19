<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "neub_club";

// Create connection
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in admin ID from the session
$admin_id = $_SESSION['admin_id'];

// Fetch admin and club details
$sql = "SELECT admins.admin_name, clubs.id AS club_id, clubs.club_name 
        FROM admins 
        JOIN clubs ON admins.club_id = clubs.id 
        WHERE admins.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin_data = $result->fetch_assoc();
    $admin_name = $admin_data['admin_name'];
    $club_id = $admin_data['club_id'];
    $club_name = $admin_data['club_name'];
} else {
    // Handle case if admin or club data is not found
    $admin_name = "Admin";
    $club_name = "Club";
}

$stmt->close();

// Fetch roles from the roles table, excluding 'Club Admin'
$role_sql = "SELECT id, role_name FROM roles WHERE role_name != 'Club Admin'";
$roles_result = $conn->query($role_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Committee Members</title>
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

        .form-container {
            border: 2px solid #007bff;
            /* The blue border around the form */
            border-radius: 10px;
            padding: 20px;
            background-color: #ffffff;
        }

        .member-form {
            border: 1px solid #ced4da;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background-color: #ffffff;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="dashboard.php">Committee Admin Dashboard</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto d-flex align-items-center">
                <li class="nav-item mr-2">
                    <button type="button" class="btn btn-info"><?php echo $admin_name; ?></button>
                </li>
                <li class="nav-item mr-2">
                    <button type="button" class="btn btn-success"><?php echo $club_name; ?></button>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container content">
        <h2 class="text-center">Add Committee Members</h2><br>
        <h4 class="bg-warning text-center">Remember you are an admin of <b><?php echo $club_name; ?></b>. So, you are about to create the <?php echo $club_name; ?> Committee!</h4> <br>

        <!-- Form Container with Border -->
        <div class="form-container">
            <form id="addClubAdminForm" action="./insert.php" method="POST">

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
                    <div class="form-group">
                        <label for="roleSelect">Select Role</label>
                        <select class="form-control" name="role_id" id="roleSelect" required>
                            <option value="">Select a role</option>
                            <?php
                            if ($roles_result->num_rows > 0) {
                                while ($row = $roles_result->fetch_assoc()) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['role_name'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Pass club ID via hidden field -->
                <input type="hidden" name="club_id" value="<?php echo $club_id; ?>">

                <div id="memberFields"></div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Add Members</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.com/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>

</html>

<?php
$conn->close();
