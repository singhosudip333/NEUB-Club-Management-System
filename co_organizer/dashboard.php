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

// Fetch the admin's name and club_id
$sql_admin = "SELECT admin_name, club_id FROM admins WHERE id = ?";
$stmt_admin = $conn->prepare($sql_admin);
$stmt_admin->bind_param("i", $admin_id);
$stmt_admin->execute();
$result_admin = $stmt_admin->get_result();
$admin_data = $result_admin->fetch_assoc();
$admin_name = $admin_data['admin_name'];
$club_id = $admin_data['club_id'];

$stmt_admin->close();

// Fetch all blogs related to the club
$sql_blogs = "SELECT * FROM blogs WHERE club_id = ? ORDER BY created_at DESC";
$stmt_blogs = $conn->prepare($sql_blogs);
$stmt_blogs->bind_param("i", $club_id);
$stmt_blogs->execute();
$result_blogs = $stmt_blogs->get_result();

// Close the statement for blogs
$stmt_blogs->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Co-Organizer Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">Co-Organizer Dashboard</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#"><?php echo htmlspecialchars($admin_name); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container content">
        <h2>Blog Management</h2>
        <div class="mb-4 my-3">
            <a href="create_blog.php" class="btn btn-primary">Create New Blog Post</a>
        </div>

        <h3>Recent Blog Posts</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Blog Title</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_blogs->num_rows > 0): ?>
                    <?php while ($blog = $result_blogs->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($blog['title']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($blog['created_at'])); ?></td>
                            <td>
                                <a href="edit_blog.php?id=<?php echo $blog['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="#" class="btn btn-danger btn-sm" onclick="deleteBlog(event, '<?php echo addslashes($blog['title']); ?>', <?php echo $blog['id']; ?>)">Delete</a>
                                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#commentsModal<?php echo $blog['id']; ?>">View Comments</button>
                            </td>

                            <!-- Modal to display comments -->
                            <div class="modal fade" id="commentsModal<?php echo $blog['id']; ?>" tabindex="-1" aria-labelledby="commentsModalLabel<?php echo $blog['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="commentsModalLabel<?php echo $blog['id']; ?>">Comments for "<?php echo htmlspecialchars($blog['title']); ?>"</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <ul class="list-group">
                                                <?php
                                                // Reopen the connection to fetch comm ents for the specific blog
                                                $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
                                                $sql_comments = "SELECT c.comment_text, u.name, c.id AS comment_id FROM comments c INNER JOIN users u ON c.user_id = u.id WHERE c.blog_id = ? ORDER BY c.created_at DESC";
                                                $stmt_comments = $conn->prepare($sql_comments);
                                                $stmt_comments->bind_param("i", $blog['id']);
                                                $stmt_comments->execute();
                                                $result_comments = $stmt_comments->get_result();

                                                if ($result_comments->num_rows > 0) {
                                                    while ($comment = $result_comments->fetch_assoc()) {
                                                        echo "<li class='list-group-item'>
                                                                <strong>" . htmlspecialchars($comment['name']) . ":</strong> " . htmlspecialchars($comment['comment_text']) . "
                                                                <a href='delete_comment.php?id=" . $comment['comment_id'] . "' class='btn btn-danger btn-sm float-right'>Delete</a>
                                                              </li>";
                                                    }
                                                } else {
                                                    echo "<li class='list-group-item'>No comments yet.</li>";
                                                }

                                                $stmt_comments->close();
                                                $conn->close();
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">No blog posts found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function deleteBlog(event, blogTitle, blogId) {
            if (confirm('Are you sure you want to delete the blog: "' + blogTitle + '"?')) {
                // Make a request to delete the blog post
                window.location.href = 'delete_blog.php?id=' + blogId;
            }
        }
    </script>

</body>

</html>