<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
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

// Get the logged-in admin's details
$admin_id = $_SESSION['admin_id'];
$sql = "SELECT club_id, role_id FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($club_id, $logged_in_role_id);
$stmt->fetch();
$stmt->close();

// Fetch committee members excluding the logged-in admin
$sql = "SELECT admins.id, admins.admin_name, roles.role_name, admins.admin_email 
        FROM admins 
        JOIN roles ON admins.role_id = roles.id 
        WHERE admins.club_id = ? AND admins.id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $club_id, $admin_id);
$stmt->execute();
$result = $stmt->get_result();

$committee_members = [];
while ($row = $result->fetch_assoc()) {
    $committee_members[] = $row;
}

// Fetch roles excluding the logged-in admin's role
$sql = "SELECT id, role_name FROM roles WHERE id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $logged_in_role_id);
$stmt->execute();
$result_roles = $stmt->get_result();

$roles = [];
while ($row = $result_roles->fetch_assoc()) {
    $roles[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Committee Members</title>
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

        .table th,
        .table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="dashboard.php">Committee Admin Dashboard</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="#">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="./logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container content">
        <h2>Edit Committee Members</h2><br>

        <!-- Committee Members Table -->
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Member Name</th>
                    <th scope="col">Role</th>
                    <th scope="col">Email</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($committee_members as $member): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($member['admin_name']); ?></td>
                        <td><?php echo htmlspecialchars($member['role_name']); ?></td>
                        <td><?php echo htmlspecialchars($member['admin_email']); ?></td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="deleteMember(<?php echo $member['id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function deleteMember(memberId) {
            if (confirm('Are you sure you want to delete this member?')) {
                $.ajax({
                    url: 'delete_member.php', // PHP script to handle deletion
                    type: 'POST',
                    data: {
                        admin_id: memberId
                    },
                    success: function(response) {
                        alert(response); // Optional: Confirm the deletion
                        location.reload(); // Refresh the page to see changes
                    },
                    error: function() {
                        alert('Error deleting member. Please try again.');
                    }
                });
            }
        }
    </script>
</body>

</html>