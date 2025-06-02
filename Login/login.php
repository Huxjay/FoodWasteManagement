<?php
session_start();
include '../db_config.php';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        // Check user in the database
        $query = "SELECT * FROM users WHERE email = ? AND status = 'active' LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            // Check hashed password
            if (password_verify($password, $user['passhash'])) {
                // Store user info in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['location_id'] = $user['location_id'];

        if ($user['role'] === 'supplier') {
            $_SESSION['location_id'] = $user['location_id'];
        }
                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header('Location: ../Admin/dashboard/dashboard.php');
                        break;
                    case 'supplier':
                        header('Location: ../Supplier/dashboard/dashboard.php');
                        break;
                    case 'customer':
                       header('Location: ../customer/dashboard/dashboard.php');
                        break;
                    default:
                        $error = 'Invalid user role.';
                        break;
                }
                exit();
            } else {
                $error = 'Incorrect password.';
            }
        } else {
            $error = 'Email not found or account is inactive.';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FoodWasteManagementSystem</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <div class="overlay"></div>
        <div class="login-box">
            <h1>Login</h1>
            <?php if (isset($error)): ?>
                <p style="color: #ff4d4d;">⚠️ <?php echo $error; ?></p>
            <?php endif; ?>
            <form action="" method="POST">
                <input type="email" name="email" placeholder="Enter your email" required>
                <input type="password" name="password" placeholder="Enter your password" required>
                <button type="submit">Login</button>
            </form>
            <p>Don’t have an account? <a href="../register/register.html">Register</a></p>
        </div>
    </div>
    <footer>
        <p>© 2025 UFMS. All rights reserved. | Designed with ❤️ Merecyana</p>
    </footer>
    <script src="assets/js/scripts.js"></script>
</body>
</html>
