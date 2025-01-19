<?php
// register.php
include 'db_connection.php';

$conn = connect_to_db();

// ... rest of your register.php code
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['c_name'];
    $student_id = $_POST['student_id'];
    $department = $_POST['c_department'];
    $session = $_POST['session'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check for duplicate email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Error: The Email is already exists. Please try again with different one.'); window.location.href='sign_up.html';</script>";
    } else {
        // Prepare and execute SQL statement
        $sql = "INSERT INTO users (name, student_id, department, session, email, phone_number, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $name, $student_id, $department, $session, $email, $phone_number, $password);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location.href='log_in.html';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
}

// Close the connection when done
$conn->close();
