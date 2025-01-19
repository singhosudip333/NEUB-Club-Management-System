<?php
session_start(); // Start the session

// Include database connection
include 'db_connection.php';
$conn = connect_to_db();

// Get user's name if logged in
$userFirstName = 'Guest';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_query = "SELECT name FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    
    if ($user_result->num_rows > 0) {
        $user_data = $user_result->fetch_assoc();
        $userFirstName = explode(' ', $user_data['name'])[0]; // Get first name
    }
}

// Get the club ID from the URL parameter
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $club_id = $_GET['id'];

    // Fetch the club details from the clubs table
    $query = "SELECT club_name, description, club_category, created_at FROM clubs WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $club_id); // Bind the club ID parameter
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $club = $result->fetch_assoc();
    } else {
        echo "<p>No club found with that ID.</p>";
        exit;
    }

    // Fetch recent events related to this club
    $events_query = "SELECT id, name, date, location, description FROM events WHERE club_id = ? AND status = 'Approved' ORDER BY date DESC LIMIT 5";
    $events_stmt = $conn->prepare($events_query);
    $events_stmt->bind_param("i", $club_id);
    $events_stmt->execute();
    $events_result = $events_stmt->get_result();

    // Fetch recent blogs related to this club
    $blogs_query = "SELECT id, title, content, image_path, created_at FROM blogs WHERE club_id = ? ORDER BY created_at DESC LIMIT 5";
    $blogs_stmt = $conn->prepare($blogs_query);
    $blogs_stmt->bind_param("i", $club_id);
    $blogs_stmt->execute();
    $blogs_result = $blogs_stmt->get_result();

    // Fetch committee list (admins) with role name from the roles table
    $admins_query = "
        SELECT a.admin_name, a.admin_email, r.role_name 
        FROM admins a
        JOIN roles r ON a.role_id = r.id
        WHERE a.club_id = ?";
    $admins_stmt = $conn->prepare($admins_query);
    $admins_stmt->bind_param("i", $club_id);
    $admins_stmt->execute();
    $admins_result = $admins_stmt->get_result();
} else {
    echo "<p>Invalid club ID.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo htmlspecialchars($club['club_name']); ?> - Club Details</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="shortcut icon" href="photos/icon/Blue & White Modern Swimming Club Logo.png" type="image/x-icon" />
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            padding-top: 60px;
        }

        .club-details {
            padding: 3rem 0;
        }

        .club-details h1 {
            font-size: 2.5rem;
            color: #007bff;
            font-weight: bold;
            margin-bottom: 2rem;
        }

        .club-details h3,
        .recent-events h2,
        .recent-blogs h2,
        .committee-list h2 {
            color: #007bff;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .info-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-5px);
        }

        .committee-list table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .recent-events .list-group-item,
        .recent-blogs .list-group-item {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            border: none;
            margin-bottom: 1rem;
            padding: 1.5rem;
            transition: transform 0.3s ease;
        }

        .recent-events .list-group-item:hover,
        .recent-blogs .list-group-item:hover {
            transform: translateY(-3px);
        }

        .recent-blogs img {
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        /* Footer */
.footer_section {
    background-color: #2d3436;
    color: #fff;
    padding: 3rem 0;
}

.social-links a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
}

.social-links a:hover {
    background: var(--primary-color);
}

        .section-title {
            position: relative;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: #007bff;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
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
                            <?php echo $userFirstName ?> <i class="fas fa-user"></i>
                        </button>
                    </li>
                    <li class="nav-item ml-2">
                        <button onclick="window.location.href='clublist.php';" type="button" class="btn btn-sm btn-success">
                            Clubs <i class="fas fa-flag"></i>
                        </button>
                    </li>
                    <li class="nav-item ml-2">
                        <button onclick="window.location.href='log_in.html';" type="button" class="btn btn-sm btn-danger">
                            Log Out <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </li>
                </ul>
                </div>
        </div>
    </nav>

    <!-- Club Details Section -->
    <section class="club-details">
        <div class="container">
            <h1 class="text-center"><?php echo htmlspecialchars($club['club_name']); ?></h1>

        <div class="row">
            <div class="col-md-6">
                    <div class="info-card">
                        <h3><i class="fas fa-info-circle"></i> Description</h3>
                <p><?php echo nl2br(htmlspecialchars($club['description'])); ?></p>
            </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card">
                        <h3><i class="fas fa-tag"></i> Category</h3>
                <p><?php echo htmlspecialchars($club['club_category']); ?></p>

                        <h3><i class="fas fa-calendar-alt"></i> Created At</h3>
                <p><?php echo date("F j, Y", strtotime($club['created_at'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Committee List Section -->
    <section class="committee-list">
        <div class="container">
            <h2 class="section-title">Committee Members</h2>
        <?php if ($admins_result->num_rows > 0): ?>
                <table class="table table-hover text-center">
                <thead class="thead-dark">
                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($admin = $admins_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($admin['admin_name']); ?></td>
                            <td><?php echo htmlspecialchars($admin['role_name']); ?></td>
                            <td><?php echo htmlspecialchars($admin['admin_email']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center text-muted">No committee members found for this club.</p>
        <?php endif; ?>
        </div>
    </section>

    <!-- Recent Events Section -->
    <section class="recent-events">
        <div class="container">
            <h2 class="section-title">Recent Events</h2>
        <?php if ($events_result->num_rows > 0): ?>
            <div class="list-group">
                <?php while ($event = $events_result->fetch_assoc()): ?>
                    <a href="event_details.php?id=<?php echo $event['id']; ?>" class="list-group-item text-decoration-none">
                            <h5 class="mb-1"><i class="fas fa-calendar-check"></i> <?php echo htmlspecialchars($event['name']); ?></h5>
                        <p class="mb-1"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small><i class="fas fa-clock"></i> <?php echo date("F j, Y", strtotime($event['date'])); ?></small>
                                <small><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></small>
                            </div>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-muted">No upcoming events for this club.</p>
        <?php endif; ?>
        </div>
    </section>

    <!-- Recent Blogs Section -->
    <section class="recent-blogs">
        <div class="container">
            <h2 class="section-title">Recent Blogs</h2>
        <div class="row">
            <?php if ($blogs_result->num_rows > 0): ?>
                <?php while ($blog = $blogs_result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                            <a href="blog_details.php?id=<?php echo $blog['id']; ?>" class="list-group-item text-decoration-none">
                                <h5 class="mb-1"><i class="fas fa-blog"></i> <?php echo htmlspecialchars($blog['title']); ?></h5>
                            <?php if ($blog['image_path']): ?>
                                <img src="co_organizer/<?php echo htmlspecialchars($blog['image_path']); ?>" alt="Blog Image"
                                        class="img-fluid my-3" />
                            <?php endif; ?>
                                <p class="mb-1 text-muted"><?php echo nl2br(htmlspecialchars(substr($blog['content'], 0, 150)) . '...'); ?></p>
                                <small class="text-muted"><i class="fas fa-clock"></i> <?php echo date("F j, Y", strtotime($blog['created_at'])); ?></small>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                    <div class="col-12">
                <p class="text-center text-muted">No recent blogs for this club.</p>
                    </div>
            <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
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