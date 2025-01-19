<?php
session_start();

include 'db_connection.php';
$conn = connect_to_db();

$userId = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT id, name, student_id, department, session, email, phone_number, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($id, $name, $studentId, $department, $session, $email, $phone, $createdAt);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>My Account - University Club Management</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1,shrink-to-fit=no" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="my_account.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet" />
    <link rel="shortcut icon" href="photos/icon/Blue & White Modern Swimming Club Logo.png" type="image/x-icon" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
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
                            <?php echo htmlspecialchars($name); ?> <i class="fas fa-user"></i>
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

    <div class="container py-5 mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">My Personal Information</h1>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <table class="table table-hover mb-0">
                            <tbody>
                                <tr>
                                    <td width="200"><strong>ID</strong></td>
                                    <td><?php echo htmlspecialchars($id); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Name</strong></td>
                                    <td><?php echo htmlspecialchars($name); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Student ID</strong></td>
                                    <td><?php echo htmlspecialchars($studentId); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Department</strong></td>
                                    <td><?php echo htmlspecialchars($department); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Session</strong></td>
                                    <td><?php echo htmlspecialchars($session); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td><?php echo htmlspecialchars($email); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Phone Number</strong></td>
                                    <td><?php echo htmlspecialchars($phone); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Account Created</strong></td>
                                    <td><?php echo htmlspecialchars($createdAt); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button onclick="window.location.href='update_profile.php';" class="btn btn-warning">
                        <i class="fas fa-edit mr-2"></i>Update Profile
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
</body>

</html>