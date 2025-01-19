<?php
require_once('../db_connection.php');
$conn = connect_to_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_name = $_POST['admin_name'];
    $admin_email = $_POST['admin_email'];
    $admin_password = password_hash($_POST['admin_password'], PASSWORD_DEFAULT); // Secure password hashing
    $club_id = $_POST['club_id'];
    $role_id = $_POST['role_id'];

    // Check if an admin already exists for the selected club
    $checkAdminSql = "SELECT COUNT(*) AS admin_count FROM admins WHERE club_id = ?";
    $checkAdminStmt = mysqli_prepare($conn, $checkAdminSql);
    mysqli_stmt_bind_param($checkAdminStmt, "i", $club_id);
    mysqli_stmt_execute($checkAdminStmt);
    $checkAdminResult = mysqli_stmt_get_result($checkAdminStmt);
    $adminCountRow = mysqli_fetch_assoc($checkAdminResult);
    $adminCount = $adminCountRow['admin_count'];

    if ($adminCount > 0) {
        // Alert if an admin is already assigned to this club
        echo "<script>alert('An admin is already assigned to this club.'); window.location.href = 'add_commiteeadmin.php';</script>";
    } else {
        // Insert the new admin into the 'admins' table
        $sql = "INSERT INTO admins (admin_name, admin_email, password, club_id, role_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssii", $admin_name, $admin_email, $admin_password, $club_id, $role_id);

        if (mysqli_stmt_execute($stmt)) {
            // Alert on successful addition of admin
            echo "<script>alert('Admin added successfully!'); window.location.href = 'add_commiteeadmin.php';</script>";
        } else {
            // Alert on error during admin addition
            echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.location.href = 'add_commiteeadmin.php';</script>";
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_stmt_close($checkAdminStmt);
    mysqli_close($conn);
}
