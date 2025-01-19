<?php
session_start();

$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "neub_club";

// Create connection
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_email = $_POST['admin_email'];
    $admin_password = $_POST['admin_password']; // The plain text password entered by the user
    $role_id = $_POST['role_id'];
    $club_id = $_POST['club_id'];

    // Query to get the hashed password for the entered email, role, and club
    $sql = "SELECT * FROM admins WHERE admin_email = ? AND role_id = ? AND club_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $admin_email, $role_id, $club_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // Verify the entered password with the stored hashed password
        if (password_verify($admin_password, $admin['password'])) {
            // Password is correct
            $_SESSION['admin_id'] = $admin['id'];  // Store admin_id in session
            $_SESSION['admin_name'] = $admin['admin_name'];  // Store admin name for display
            $_SESSION['club_id'] = $admin['club_id'];  // Store club_id in session

            // Redirect to different dashboards based on role
            switch ($admin['role_id']) {
                case 2: // President
                    header("Location: ./committee_admin/dashboard.php");
                    break;
                case 3: // Vice President
                    header("Location: ./president/dashboard.php");
                    break;
                case 4: // Organizer
                    header("Location: ./organizer/dashboard.php");
                    break;
                case 5:
                    header("Location: ./co_organizer/dashboard.php");
                    break;
            }
            exit();
        } else {
            // Incorrect password
            echo "Invalid login credentials!";
        }
    } else {
        // No admin found with the provided email, role, and club
        echo "Invalid login credentials!";
    }

    $stmt->close();
}

$conn->close();
