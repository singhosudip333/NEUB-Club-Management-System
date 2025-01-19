<?php
session_start();

$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "neub_club";

// Create connection
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_name = $_POST['admin_name'];
    $admin_email = $_POST['admin_email'];
    $admin_password = password_hash($_POST['admin_password'], PASSWORD_BCRYPT);
    $role_id = $_POST['role_id'];
    $club_id = $_POST['club_id'];

    // Check if the role is "Executive Member" (assuming role_id 3)
    if ($role_id != 7) {
        // Only check for duplicates if not an executive member
        $check_sql = "SELECT * FROM admins WHERE role_id = ? AND club_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        if ($check_stmt === false) {
            die("Error in preparing check statement: " . $conn->error);
        }
        $check_stmt->bind_param("ii", $role_id, $club_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            echo "<script>alert('An admin with this role already exists in the club.'); window.history.back();</script>";
            exit();
        }
    }

    // Insert into the admins table 
    $admin_sql = "INSERT INTO admins (admin_name, admin_email, password, role_id, club_id) 
                    VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($admin_sql);
    if ($stmt === false) {
        die("Error in preparing statement: " . $conn->error);
    }
    $stmt->bind_param("sssii", $admin_name, $admin_email, $admin_password, $role_id, $club_id);

    if ($stmt->execute() === false) {
        die("Error in execution: " . $stmt->error);
    }

    echo "<script>alert('Admin successfully added!'); window.location.href = 'add_committee_member.php';</script>";
    exit();
}

$conn->close();
