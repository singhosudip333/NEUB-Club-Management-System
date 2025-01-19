<?php
session_start();

$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "neub_club";

// Create connection
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch roles
$roles_sql = "SELECT id, role_name FROM roles";
$roles_result = $conn->query($roles_sql);

// Fetch clubs
$clubs_sql = "SELECT id, club_name FROM clubs";
$clubs_result = $conn->query($clubs_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Admin Login</div>
                    <div class="card-body">
                        <form method="POST" action="process_login.php">
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" id="email" name="admin_email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" class="form-control" id="password" name="admin_password" required>
                            </div>
                            <div class="form-group">
                                <label for="role">Role:</label>
                                <select class="form-control" id="role" name="role_id" required>
                                    <option value="">Select Role</option>
                                    <?php while ($role = $roles_result->fetch_assoc()): ?>
                                        <option value="<?= $role['id']; ?>"><?= $role['role_name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="club">Club:</label>
                                <select class="form-control" id="club" name="club_id" required>
                                    <option value="">Select Club</option>
                                    <?php while ($club = $clubs_result->fetch_assoc()): ?>
                                        <option value="<?= $club['id']; ?>"><?= $club['club_name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Login</button>
                                <button type="button" class="btn btn-secondary" onclick="window.history.back();">Back</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<?php $conn->close(); ?>