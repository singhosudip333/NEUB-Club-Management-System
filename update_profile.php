<?php
session_start();

include 'db_connection.php';
$conn = connect_to_db();

$userId = $_SESSION['user_id'];

// Fetch existing user details
$stmt = $conn->prepare("SELECT name, student_id, department, session, email, phone_number FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($name, $studentId, $department, $session, $email, $phone);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $updatedName = $_POST['name'];
    $updatedStudentId = $_POST['student_id'];
    $updatedDepartment = $_POST['department'];
    $updatedSession = $_POST['session'];
    $updatedEmail = $_POST['email'];
    $updatedPhone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE users SET name = ?, student_id = ?, department = ?, session = ?, email = ?, phone_number = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $updatedName, $updatedStudentId, $updatedDepartment, $updatedSession, $updatedEmail, $updatedPhone, $userId);
    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href = 'my_account.php';</script>";
    } else {
        echo "<script>alert('Error updating profile. Please try again.');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Update Profile</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="my_account.css">
    <style>
        .update-profile-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            padding: 2rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        
        .form-control {
            border-radius: 8px;
            border: 1px solid var(--bg-secondary);
            padding: 0.75rem;
            transition: var(--transition);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
        
        .form-group label {
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #3451d1;
            border-color: #3451d1;
        }
        
        .btn-secondary {
            background-color: var(--bg-secondary);
            border-color: var(--bg-secondary);
            color: var(--text-color);
        }
        
        .btn-secondary:hover {
            background-color: #dde0e3;
            border-color: #dde0e3;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="update-profile-container">
            <h1 class="text-center mb-4">Update Profile</h1>
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="student_id">Student ID</label>
                            <input type="text" id="student_id" name="student_id" class="form-control" value="<?= htmlspecialchars($studentId) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="department">Department</label>
                            <input type="text" id="department" name="department" class="form-control" value="<?= htmlspecialchars($department) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="session">Session</label>
                            <input type="text" id="session" name="session" class="form-control" value="<?= htmlspecialchars($session) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($phone) ?>" required>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary mr-2">Save Changes</button>
                    <button type="button" onclick="window.location.href='my_account.php';" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>