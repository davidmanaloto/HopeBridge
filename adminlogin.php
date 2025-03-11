<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = isset($_POST["username"]) ? trim($_POST["username"]) : "";
    $pass = isset($_POST["password"]) ? trim($_POST["password"]) : "";

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "hopebridge_database";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $errors = [];

    if (empty($user)) {
        $errors['username'] = "Username is required.";
    }
    if (empty($pass)) {
        $errors['password'] = "Password is required.";
    }

    if (empty($errors)) {
        if ($stmt = $conn->prepare("SELECT password, role FROM user_table WHERE username = ?")) {
            $stmt->bind_param("s", $user);
            $stmt->execute();
            $stmt->bind_result($hashed_password, $role);
            $stmt->fetch();

            $_SESSION['username'] = $user;
            $_SESSION['role'] = $role;

            if ($hashed_password && password_verify($pass, $hashed_password)) {
                if ($role === 'Admin') {
                    header("Location: admindashboard.php");
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
    $conn->close();
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
            <form action="adminlogin.php" method="POST">
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
                <a href="home.php" class ="forgot-password">Return Home</a>
                <!--<a href="forgotpass.html" class="forgot-password">Forgot password?</a>-->

                <!--<div class="signup-container">
                    <p>Don't have an account? <a href="adminsignup.php">Sign Up</a></p>
                </div>-->
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