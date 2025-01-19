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

// Fetch the blog to be edited
if (isset($_GET['id'])) {
    $blog_id = $_GET['id'];

    // Fetch the blog details
    $sql_blog = "SELECT * FROM blogs WHERE id = ? AND author_id = ?";
    $stmt_blog = $conn->prepare($sql_blog);
    $stmt_blog->bind_param("ii", $blog_id, $admin_id);
    $stmt_blog->execute();
    $result_blog = $stmt_blog->get_result();
    $blog = $result_blog->fetch_assoc();

    if (!$blog) {
        die("Blog not found or you do not have permission to edit this blog.");
    }

    $stmt_blog->close();
} else {
    die("Blog ID is required.");
}

// Update the blog after form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image_path = $blog['image_path']; // Keep the existing image if not changed

    // Handle file upload if a new photo is provided
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $upload_dir = 'uploads/';
        $file_name = basename($_FILES['photo']['name']);
        $upload_path = $upload_dir . $file_name;

        // Move the uploaded file to the server's uploads directory
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
            $image_path = $upload_path;
        } else {
            die("File upload failed.");
        }
    }

    // Update the blog in the database
    $update_query = "UPDATE blogs SET title = ?, content = ?, image_path = ?, updated_at = NOW() WHERE id = ? AND author_id = ?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->bind_param("sssii", $title, $content, $image_path, $blog_id, $admin_id);

    if ($stmt_update->execute()) {
        echo "<script>
                alert('Blog post updated successfully!');
                window.location.href = 'dashboard.php'; // Redirect to the dashboard after successful update
              </script>";
    } else {
        echo "<script>
                alert('Error: " . $stmt_update->error . "');
              </script>";
    }

    $stmt_update->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Edit Blog Post</h2>
        <form action="edit_blog.php?id=<?php echo $blog['id']; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($blog['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="content">Content</label>
                <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars($blog['content']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="photo">Upload New Photo (Optional)</label>
                <input type="file" class="form-control-file" id="photo" name="photo" accept="image/*">
                <br>
                <?php if ($blog['image_path']): ?>
                    <img src="<?php echo $blog['image_path']; ?>" alt="Current Image" width="500" class="mt-2">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Update Blog</button>
        </form>
    </div>
</body>

</html>