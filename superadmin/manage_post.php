<?php
// Start session and check if user is logged in
session_start();
if (!isset($_SESSION['superadmin_username'])) {
    header('Location: superadmin_login.php');
    exit();
}
$admin_name = $_SESSION['superadmin_username'];

// Connect to the database
require_once('../db_connection.php');
$conn = connect_to_db();

// Fetch clubs for dropdown
$clubs_query = "SELECT id, club_name FROM clubs";
$clubs_result = mysqli_query($conn, $clubs_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blogs</title>
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

        .blog-card {
            border: 1px solid #ced4da;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .blog-card:hover {
            transform: scale(1.02);
        }

        .blog-image {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .sidebar {
            height: 100vh;
            background-color: #495057;
            padding-top: 20px;
        }

        .sidebar a {
            color: white;
            padding: 15px;
            display: block;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #6c757d;
        }

        .sidebar a.active {
            background-color: #6c757d;
            color: #fff;
        }

        #blogsContainer {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px 0;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="./superadmin.php">Super Admin Dashboard</a>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="#"><?php echo htmlspecialchars($admin_name); ?></a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>
            <!-- Main Content Area -->
            <div class="col-md-10 content">
                <h2>Manage Blogs</h2>
                <form id="blogForm">
                    <div class="form-group">
                        <label for="clubSelect">Select Club</label>
                        <select class="form-control" name="clubSelect" id="clubSelect" onchange="loadBlogs()" required>
                            <option value="">Select a club</option>
                            <?php while ($club = mysqli_fetch_assoc($clubs_result)) {
                                echo "<option value='" . $club['id'] . "'>" . $club['club_name'] . "</option>";
                            } ?>
                        </select>
                    </div>
                </form>
                <div id="blogsContainer"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        function loadBlogs() {
            const clubId = document.getElementById('clubSelect').value;
            if (clubId) {
                $.ajax({
                    url: 'fetch_blogs.php',
                    type: 'POST',
                    data: {
                        club_id: clubId
                    },
                    success: function(response) {
                        document.getElementById('blogsContainer').innerHTML = response;
                    },
                    error: function() {
                        document.getElementById('blogsContainer').innerHTML =
                            '<p>Error fetching blogs. Please try again.</p>';
                    }
                });
            } else {
                document.getElementById('blogsContainer').innerHTML = '<p>Select a club to see its blogs.</p>';
            }
        }

        function deleteBlog(blogId) {
            if (confirm('Are you sure you want to delete this blog?')) {
                $.ajax({
                    url: 'delete_blog.php',
                    type: 'POST',
                    data: {
                        blog_id: blogId
                    },
                    success: function() {
                        loadBlogs(); // Reload blogs after deletion
                    },
                    error: function() {
                        alert('Error deleting blog. Please try again.');
                    }
                });
            }
        }
    </script>
</body>

</html>