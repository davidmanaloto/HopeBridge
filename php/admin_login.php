<?php
session_start();
require 'db_connection.php';

// Get username & password from form
$user = isset($_POST["username"]) ? trim($_POST["username"]) : "";
$pass = isset($_POST["password"]) ? trim($_POST["password"]) : "";

// Check if admin exists
$sql = "SELECT id FROM user_table WHERE role = 'Admin' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    // No admin found, create one
    $admin_username = "admin";
    $admin_email = "admin@example.com";
    $admin_password = password_hash("admin123", PASSWORD_DEFAULT); // Hash password
    $admin_role = "Admin";
    $admin_status = "Verified";

    $insert_sql = "INSERT INTO user_table (username, email, password, role, status) 
                   VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("sssss", $admin_username, $admin_email, $admin_password, $admin_role, $admin_status);
    $stmt->execute();
    $stmt->close();
}

// Error messages
$errors = [];

// Validate fields
if (empty($user)) {
    $errors['username'] = "Username is required.";
}
if (empty($pass)) {
    $errors['password'] = "Password is required.";
}

// Fetch data from database
if (empty($errors)) {
    if ($stmt = $conn->prepare("SELECT password, role FROM user_table WHERE username = ?")) {
        $stmt->bind_param("s", $user);
        $stmt->execute();
        mysqli_stmt_store_result($stmt); // Store result before fetching
        $stmt->bind_result($hashed_password, $role);
        $stmt->fetch();

        // Verify password
        if ($hashed_password && password_verify($pass, $hashed_password)) {
            if ($role === 'Admin') {
                $_SESSION['username'] = $user;
                $_SESSION['role'] = $role;
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $errors['general'] = "Access denied. Only admins can log in.";
            }
        } else {
            if ($hashed_password) {
                $errors['password'] = "Incorrect password.";
            } else {
                $errors['username'] = "Username not found.";
            }
        }
        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HopeBridge Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <form action="admin_login.php" method="POST">
                <h2>Admin</h2>
                <p>Sign in to your account</p>

                <?php if (!empty($errors['general'])): ?>
                    <p class="error-message"> <?php echo $errors['general']; ?> </p>
                <?php endif; ?>

                <div class="input-group">
                    <input type="text" id="username" name="username" placeholder="Username" value="<?php echo isset($user) ? htmlspecialchars($user) : ''; ?>">
                    <?php if (!empty($errors['username'])): ?>
                        <p class="error-message"> <?php echo $errors['username']; ?> </p>
                    <?php endif; ?>
                </div>

                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder="Password">
                    <?php if (!empty($errors['password'])): ?>
                        <p class="error-message"> <?php echo $errors['password']; ?> </p>
                    <?php endif; ?>
                </div>

                <button type="submit">Login</button>
            </form>
        </div>

        <div class="brand-container">
            <img src="hopebridge.jpg" alt="Company Logo" class="logo">
            <h2 class="HopeBridge">HopeBridge</h2>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            document.querySelectorAll('.error-message').forEach(msg => msg.remove());
        }, 2000);
    });
    </script>
</body>
</html>

<style>
    .error-message {
    color: red;
    font-size: 12px;
    margin-top: 5px;
}
</style>