<?php
$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if needed
$dbname = "hopebridge_database"; // Change to your database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user1 = isset($_POST["username1"]) ? trim($_POST["username1"]) : "";
    $user2 = isset($_POST["username2"]) ? trim($_POST["username2"]) : "";
    $email1 = isset($_POST["email1"]) ? trim($_POST["email1"]) : "";
    $email2 = isset($_POST["email2"]) ? trim($_POST["email2"]) : "";
    $pass = isset($_POST["password1"]) ? trim($_POST["password1"]) : "";
    $confirm_pass = isset($_POST["password2"]) ? trim($_POST["password2"]) : "";

    if (empty($user1)||empty($user2)||empty($email1)||empty($email2)||empty($pass)||empty($confirm_pass)) {
        die("Error: All fields are required.");
    }

    if ($user1 !== $user2) {
        die("Error: Usernames do not match.");
    }

    if ($email1 !== $email2) {
        die("Error: Emails do not match.");
    }

    if (strlen($pass) < 8) {
        die("Error: Password must be at least 8 characters long."); // FIX THIS 11/02/2025 //FIXED IN: 3mins 11/02/2025
    }

    if ($pass !== $confirm_pass) {  
        die("Passwords do not match.");
        var_dump($pass, $confirm_pass);
        exit();
    }

    $pass = password_hash($pass, PASSWORD_BCRYPT);

    // Check if email already exists
    $check_sql = "SELECT email FROM user_table WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email1);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        die("Error: Email already exists.");
    }

    $check_stmt->close();

    //$verification_code = sendVerificationEmail($email);
    //if ($verification_code !== false) {
    // Store $verification_code in database
    //} else {
    //    echo "Failed to send verification email.";
    //}

    // Insert
    $sql = "INSERT INTO user_table (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $user, $email, $pass);

    if ($stmt->execute()) {
        header("Location: home.html?signup=success");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>