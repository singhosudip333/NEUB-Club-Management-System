<?php
session_start();

include 'db_connection.php';
$conn = connect_to_db();


$userId = $_SESSION['user_id'];

// Fetch user's first name from the database
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($firstName);
$stmt->fetch();
$stmt->close();

// Updated blog query to match existing database structure
$stmt = $conn->prepare("
    SELECT b.id, b.title, b.content as description, b.created_at, b.image_path as image_url, u.name as author_name 
    FROM blogs b 
    LEFT JOIN users u ON b.author_id = u.id 
    ORDER BY b.created_at DESC 
    LIMIT 4
");
$stmt->execute();
$result = $stmt->get_result();
$recentBlogs = $result->fetch_all(MYSQLI_ASSOC);

$userFirstName = $firstName;

// Logout Functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("location: log_in.html"); // Redirect to login page after logout
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>NEUB Club Management | Connect, Engage, Grow</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="NEUB Club Management - Your gateway to university clubs, events, and activities. Join clubs, participate in events, and make the most of your university life." />
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="log_user.css" />
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="photos/icon/Blue & White Modern Swimming Club Logo.png" type="image/x-icon" />
</head>

<body>
    <!-- Menubar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="log_user.php">
                <img src="photos/icon/Blue & White Modern Swimming Club Logo.png" alt="Logo" class="logo-img" style="width: 40px;">
                <span class="ml-2">NEUB Club Management</span>
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item">
                        <button onclick="window.location.href='my_account.php';" type="button" class="btn btn-sm btn-success">
                            <?php echo $userFirstName; ?> <i class="fas fa-user"></i>
                        </button>
                    </li>
                    <li class="nav-item ml-2">
                        <button onclick="window.location.href='clublist.php';" type="button" class="btn btn-sm btn-success">
                            Clubs <i class="fas fa-flag"></i>
                        </button>
                    </li>
                    <li class="nav-item ml-2">
                        <form method="GET" class="m-0">
                            <button type="submit" name="logout" class="btn btn-sm btn-danger">
                                Log Out <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="hero-content">
                        <h1 class="hero-title mb-4">
                            Welcome Back, <?php echo $userFirstName; ?>!
                        </h1>
                        <p class="hero-subtitle mb-4">Browse all events and details of your favourite Club. You will definitely find what you are looking for.</p>
                        <div class="hero-buttons">
                            <a href="clublist.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-compass"></i> Visit Clubs
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="hero-image">
                        <img src="./photos/slide1.jpg" class="img-fluid rounded" alt="Hero Image">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Clubs -->
    <section class="featured-clubs py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <span class="section-badge">Discover</span>
                <h2 class="section-title">Recommended Clubs for You</h2>
                <p class="section-subtitle">Join these amazing communities and start your journey</p>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="club-card">
                        <div class="club-card-badge">Most Active</div>
                        <div class="club-card-image">
                            <img src="photos/clubs/caltural.png" alt="Cultural Club">
                        </div>
                        <div class="club-card-body">
                            <h3 class="club-title">Cultural Club</h3>
                            <p class="club-description">Celebrate diversity through music, and cultural events.</p>
                            <div class="club-actions">
                                <a href="#" class="btn btn-primary btn-block">View Club</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="club-card">
                        <div class="club-card-badge">Trending</div>
                        <div class="club-card-image">
                            <img src="photos/clubs/cosmic.JPG" alt="Social Service Club">
                        </div>
                        <div class="club-card-body">
                            <h3 class="club-title">Social Service Club</h3>
                            <p class="club-description">Make a difference in the community through service.</p>
                            <div class="club-actions">
                                <a href="#" class="btn btn-primary btn-block">View Club</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="club-card">
                        <div class="club-card-badge">New</div>
                        <div class="club-card-image">
                            <img src="photos/clubs/cosmicray.jpg" alt="Cosmic Ray">
                        </div>
                        <div class="club-card-body">
                            <h3 class="club-title">Cosmic Ray</h3>
                            <p class="club-description">Explore the mysteries of space and science.</p>
                            <div class="club-actions">
                                <a href="/cosmic.html" class="btn btn-primary btn-block">View Club</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="club-card">
                        <div class="club-card-badge">Popular</div>
                        <div class="club-card-image">
                            <img src="photos/clubs/sports.jpg" alt="Sports Club">
                        </div>
                        <div class="club-card-body">
                            <h3 class="club-title">Sports Club</h3>
                            <p class="club-description">Stay active and competitive with sports activities.</p>
                            <div class="club-actions">
                                <a href="/club.html" class="btn btn-primary btn-block">View Club</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="nobel">
        <div class="container">
            <div class="row d-flex align-items-center">
                <div class="col-xl-6">
                    <img src="./photos/OIP.jpeg" alt="About Image" class="img-fluid rounded">
                </div>
                <div class="col-xl-6">
                    <div class="news_content">
                        <h3>NEUB Club Management</h3>
                        <p>Welcome to University Club Management! We strive to lead with vision, engage with passion,
                            and innovate through collaboration. Join us in creating a dynamic community where students
                            can grow, connect, and make a lasting impact. Together, we build experiences that shape our university life and
                            beyond.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-header text-center mb-5">
                <span class="section-badge">Features</span>
                <h2 class="section-title">Why Choose Us</h2>
                <p class="section-subtitle">Experience the best of university club management</p>
            </div>
            
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="feature-content">
                            <h4>Registration</h4>
                            <p>Student can get registered themself easily.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div class="feature-content">
                            <h4>Visit Clubs</h4>
                            <p>User can easily visit the club he wanted.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="feature-content">
                            <h4>Participation</h4>
                            <p>User can register himself for particuler events by the club.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <div class="feature-content">
                            <h4>Read Post</h4>
                            <p>User can read and share blogs provided by Clubs.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Blog Posts -->
    <section class="blog-section py-5 bg-light">
        <div class="container">
            <div class="section-header text-center mb-5">
                <span class="section-badge">Latest Updates</span>
                <h2 class="section-title">Recent Blog Posts</h2>
                <p class="section-subtitle">Stay informed with our latest news and updates</p>
            </div>
            
            <div class="row">
                <?php if (!empty($recentBlogs)): ?>
                    <?php foreach ($recentBlogs as $blog): ?>
                        <div class="col-lg-6 mb-4">
                            <div class="card blog-card h-100">
                                <?php if (!empty($blog['image_url'])): ?>
                                    <div class="blog-card-image">
                                        <img src="co_organizer/<?= htmlspecialchars($blog['image_url']) ?>" class="card-img-top" alt="Blog Image">
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <div class="blog-meta mb-2">
                                        <span class="text-muted">
                                            <i class="far fa-calendar-alt"></i>
                                            <?= date('F j, Y', strtotime($blog['created_at'])) ?>
                                        </span>
                                        <?php if (!empty($blog['author_name'])): ?>
                                            <span class="text-muted ml-3">
                                                <i class="far fa-user"></i>
                                                <?= htmlspecialchars($blog['author_name']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <h4 class="card-title"><?= htmlspecialchars($blog['title']) ?></h4>
                                    <?php if (!empty($blog['description'])): ?>
                                        <p class="card-text text-muted">
                                            <?= strlen($blog['description']) > 150 ? 
                                                htmlspecialchars(substr($blog['description'], 0, 150)) . '...' : 
                                                htmlspecialchars($blog['description']) ?>
                                        </p>
                                    <?php endif; ?>
                                    <div class="blog-footer mt-3">
                                        <a href="blog_details.php?id=<?= $blog['id'] ?>" class="btn btn-outline-primary btn-sm">
                                            Read More <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            No recent blogs available.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="all_blogs.php" class="btn btn-primary">
                    View All Posts <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Form -->
    <section class="contact-section">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2>Get In Touch</h2>
                <p>Have questions? We'd love to hear from you.</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="contact-form-container">
                        <form id="contactForm" class="contact-form">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Your Name" required>
                            </div>

                            <div class="form-group">
                                <input type="email" class="form-control" placeholder="Your Email" required>
                            </div>

                            <div class="form-group">
                                <textarea class="form-control" rows="5" placeholder="Your Message" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer_section">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="text-white mb-4">About NEUB Club Management</h5>
                    <p class="text-white-50">Empowering students to discover their passions, develop skills, and build lasting connections through university clubs.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="text-white mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50">Home</a></li>
                        <li><a href="#" class="text-white-50">Clubs</a></li>
                        <li><a href="#" class="text-white-50">Events</a></li>
                        <li><a href="#" class="text-white-50">Blog</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="text-white mb-4">Connect With Us</h5>
                    <div class="social-links">
                        <a href="#" class="text-white mr-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white mr-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white mr-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <hr class="border-light mt-4 mb-4">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-white-50 mb-0">© 2024 NEUB Club Management. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>