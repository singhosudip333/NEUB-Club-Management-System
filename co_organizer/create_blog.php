<?php
session_start();

// Check if the user is logged in and the session contains the admin's name
if (!isset($_SESSION['admin_name'])) {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit();
}

// Retrieve the admin's name from the session
$admin_name = $_SESSION['admin_name'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <!-- Bootstrap CSS for styling -->
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

        .content {
            padding: 20px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="dashboard.php">Co-Organizer Dashboard</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#"><?php echo htmlspecialchars($admin_name); ?></a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container content">
        <h2>Create New Blog</h2>
        <form action="createBlog.php" method="POST" enctype="multipart/form-data">
            <!-- Hidden Fields for admin_id and club_id -->
            <input type="hidden" name="admin_id" value="<?php echo $_SESSION['admin_id']; ?>">
            <input type="hidden" name="club_id" value="<?php echo $_SESSION['club_id']; ?>">

            <!-- Blog Title -->
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>

            <!-- Blog Content -->
            <div class="form-group">
                <label for="content">Content</label>
                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
            </div>

            <!-- Photo Upload -->
            <div class="form-group">
                <label for="photo">Upload Photo</label>
                <input type="file" class="form-control-file" id="photo" name="photo" accept="image/*">
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>