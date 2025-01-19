<?php
session_start();

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
        $userFirstName = explode(' ', $user_data['name'])[0];
    }
}

// Get the event ID from the URL parameter
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $event_id = $_GET['id'];

    // Fetch the event details
    $query = "SELECT e.*, c.club_name 
              FROM events e 
              JOIN clubs c ON e.club_id = c.id 
              WHERE e.id = ? AND e.status = 'Approved'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
    } else {
        echo "<p>Event not found.</p>";
        exit;
    }
} else {
    echo "<p>Invalid event ID.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo htmlspecialchars($event['name']); ?> - Event Details</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="shortcut icon" href="photos/icon/Blue & White Modern Swimming Club Logo.png" type="image/x-icon" />
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            padding-top: 60px;
        }

        .event-details {
            padding: 3rem 0;
        }

        .event-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .event-title {
            color: #007bff;
            font-weight: bold;
            margin-bottom: 1.5rem;
        }

        .event-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .event-info i {
            color: #007bff;
            margin-right: 0.5rem;
        }

        .back-button {
            margin-bottom: 2rem;
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

    <!-- Event Details Section -->
    <section class="event-details">
        <div class="container">
            <div class="back-button">
                <a href="javascript:history.back()" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="event-card">
                <h1 class="event-title"><?php echo htmlspecialchars($event['name']); ?></h1>
                
                <div class="event-info">
                    <div class="row">
                        <div class="col-md-6">
                            <p><i class="fas fa-calendar"></i> Date: <?php echo date("F j, Y", strtotime($event['date'])); ?></p>
                            <p><i class="fas fa-map-marker-alt"></i> Location: <?php echo htmlspecialchars($event['location']); ?></p>
                            <p><i class="fas fa-users"></i> Organized by: <?php echo htmlspecialchars($event['club_name']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="event-description">
                    <h4 class="mb-3">Event Description</h4>
                    <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                </div>
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