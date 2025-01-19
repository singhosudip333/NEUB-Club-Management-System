<?php
include 'db_connection.php';
$conn = connect_to_db();


if (isset($_GET['id'])) {
    $blogId = $_GET['id'];

    // Fetch blog details 
    $stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->bind_param("i", $blogId);
    $stmt->execute();
    $result = $stmt->get_result();
    $blog = $result->fetch_assoc();

    if ($blog) {
        // Fetch comments for this blog
        $comments_query = "
            SELECT c.comment_text, c.created_at, u.name AS user_name
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.blog_id = ? 
            ORDER BY c.created_at DESC";
        $comments_stmt = $conn->prepare($comments_query);
        $comments_stmt->bind_param("i", $blogId);
        $comments_stmt->execute();
        $comments_result = $comments_stmt->get_result();
?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Blog Details</title>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
            <style>
                body {
                    background-color: #f8f9fa;
                    color: #333;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                }

                .blog-container {
                    background: white;
                    border: none;
                    padding: 30px;
                    margin: 40px 0;
                    border-radius: 15px;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
                }

                .blog-container h1 {
                    color: #2c3e50;
                    font-size: 2.5rem;
                    font-weight: 700;
                    margin-bottom: 20px;
                }

                .blog-container p {
                    font-size: 1.1rem;
                    line-height: 1.8;
                    color: #505050;
                }

                .blog-container img {
                    border-radius: 12px;
                    margin: 25px 0;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                }

                .back-btn {
                    background-color: #3498db;
                    border: none;
                    padding: 10px 25px;
                    border-radius: 8px;
                    transition: all 0.3s ease;
                }

                .back-btn:hover {
                    background-color: #2980b9;
                    transform: translateY(-2px);
                }

                .comments-section {
                    background: white;
                    padding: 30px;
                    border-radius: 15px;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
                    margin-bottom: 40px;
                }

                .comments-section h2 {
                    color: #2c3e50;
                    font-size: 2rem;
                    font-weight: 600;
                    margin-bottom: 30px;
                    padding-bottom: 15px;
                    border-bottom: 3px solid #3498db;
                }

                .comments-list .media {
                    border: none;
                    padding: 20px;
                    border-radius: 12px;
                    background-color: #f8f9fa;
                    margin-bottom: 20px;
                    transition: transform 0.2s ease;
                }

                .comments-list .media:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
                }

                .comments-list .media img {
                    width: 60px;
                    height: 60px;
                    border-radius: 50%;
                    border: 3px solid #3498db;
                }

                .comments-list .media h5 {
                    color: #2c3e50;
                    font-weight: 600;
                    margin-bottom: 10px;
                }

                .add-comment {
                    background: white;
                    padding: 30px;
                    border-radius: 15px;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
                }

                .add-comment h3 {
                    color: #2c3e50;
                    font-weight: 600;
                    margin-bottom: 25px;
                }

                .add-comment textarea {
                    border: 2px solid #e9ecef;
                    border-radius: 10px;
                    padding: 15px;
                    font-size: 1.1rem;
                    transition: border-color 0.3s ease;
                }

                .add-comment textarea:focus {
                    border-color: #3498db;
                    box-shadow: none;
                }

                .add-comment button {
                    background-color: #3498db;
                    border: none;
                    padding: 12px 30px;
                    border-radius: 8px;
                    font-weight: 600;
                    transition: all 0.3s ease;
                }

                .add-comment button:hover {
                    background-color: #2980b9;
                    transform: translateY(-2px);
                }

                .post-meta {
                    display: flex;
                    align-items: center;
                    color: #6c757d;
                    font-size: 0.9rem;
                    margin: 15px 0;
                }

                .post-meta i {
                    margin-right: 5px;
                    color: #3498db;
                }
            </style>
        </head>

        <body>
            <div class="container">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="blog-container">
                            <h1><?= htmlspecialchars($blog['title']) ?></h1>
                            <div class="post-meta">
                                <i class="far fa-calendar-alt"></i>
                                <span>Posted on <?= date('M d, Y', strtotime($blog['created_at'])) ?></span>
                            </div>
                            <p><?= nl2br(htmlspecialchars($blog['content'])) ?></p>
                            <?php if (!empty($blog['image_path'])): ?>
                                <img src="co_organizer/<?= htmlspecialchars($blog['image_path']) ?>" alt="Blog Image"
                                    class="img-fluid my-3">
                            <?php endif; ?>
                            <button onclick="history.back()" class="btn back-btn">
                                <i class="fas fa-arrow-left mr-2"></i>Back
                            </button>
                        </div>
                    </div>
                </div>

                <section class="comments-section container mt-5">
                    <h2>Comments</h2>
                    <div class="comments-list mt-4">
                        <?php while ($comment = $comments_result->fetch_assoc()): ?>
                            <div class="media mb-4 p-3 shadow-sm rounded bg-white">
                                <img src="https://via.placeholder.com/50" alt="User Avatar" class="mr-3 rounded-circle">
                                <div class="media-body">
                                    <h5 class="mt-0"><?php echo htmlspecialchars($comment['user_name']); ?></h5>
                                    <p><?php echo htmlspecialchars($comment['comment_text']); ?></p>
                                    <small class="text-muted">Posted on:
                                        <?php echo date("F j, Y, g:i a", strtotime($comment['created_at'])); ?></small>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="add-comment mt-5">
                        <h3>Add a Comment</h3>
                        <form method="POST" action="add_comment.php">
                            <input type="hidden" name="blog_id" value="<?php echo $blogId; ?>">
                            <input type="hidden" name="club_id" value="<?php echo $blog['club_id']; ?>">
                            <div class="form-group">
                                <label for="commentText">Your Comment</label>
                                <textarea class="form-control" id="commentText" name="comment" rows="4"
                                    placeholder="Write your comment here..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Post Comment</button>
                        </form>
                    </div>
                </section>
            </div>
        </body>

        </html>

<?php
    } else {
        echo "<p>Blog not found.</p>";
    }
} else {
    echo "<p>Invalid blog ID.</p>";
}
?>