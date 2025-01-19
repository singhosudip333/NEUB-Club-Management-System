<?php
session_start(); // Start the session to access user data

// Include database connection
include 'db_connection.php';
$conn = connect_to_db();

// Fetch all active clubs from the database
$query = "SELECT id, club_name, description, club_category, club_status, created_at FROM clubs WHERE club_status = 'active'";
$result = $conn->query($query);

// Assuming user ID is stored in session after login
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch user data from the database
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($full_name);
    $stmt->fetch();
    $stmt->close();

    // Extract the first name
    $first_name = explode(' ', $full_name)[0]; // Get the first part of the name
} else {
    $first_name = 'Guest'; // Default if no user is logged in
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Club List - NEUB Club Management</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1,shrink-to-fit=no" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="clublist.css" />
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
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
                            <?php echo htmlspecialchars($first_name); ?> <i class="fas fa-user"></i>
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

    <br />

    <section class="clubs">
        <div class="container mt-4">
            <div class="text-center mb-5">
                <h1 class="display-4 font-weight-bold">Our Clubs</h1>
                <p class="lead text-muted">Discover and join amazing clubs at NEUB</p>
            </div>

            <div class="row g-4">
                <?php
                // Check if clubs exist
                if ($result->num_rows > 0) {
                    // Loop through each club and display
                    while ($club = $result->fetch_assoc()) {
                        echo '<div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h3 class="card-title">' . htmlspecialchars($club['club_name']) . '</h3>
                                        <span class="badge badge-primary mb-3">' . htmlspecialchars($club['club_category']) . '</span>
                                        <p class="card-text">' . htmlspecialchars($club['description']) . '</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="club_details.php?id=' . $club['id'] . '" class="btn btn-primary">
                                                <i class="fa-solid fa-circle-info"></i> Learn More
                                            </a>
                                            <small class="text-muted">Created: ' . date('M d, Y', strtotime($club['created_at'])) . '</small>
                                        </div>
                                    </div>
                                </div>
                              </div>';
                    }
                } else {
                    echo '<div class="col-12">
                            <div class="alert alert-info text-center" role="alert">
                                <i class="fa-solid fa-circle-info"></i> No active clubs found.
                            </div>
                          </div>';
                }
                ?>
            </div>
        </div>
    </section>

    <br />
    <section class="footer_section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h5 class="text-center">© Copyright 2024 | University Club Management | All Rights Reserved</h5>
                </div>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>