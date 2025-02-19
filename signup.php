<?php
$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if needed
$dbname = "hopebridge_database"; // Change to your database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    exit("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user1 = isset($_POST["username1"]) ? trim($_POST["username1"]) : "";
    $user2 = isset($_POST["username2"]) ? trim($_POST["username2"]) : "";
    $email1 = isset($_POST["email1"]) ? trim($_POST["email1"]) : "";
    $email2 = isset($_POST["email2"]) ? trim($_POST["email2"]) : "";
    $pass = isset($_POST["password1"]) ? trim($_POST["password1"]) : "";
    $confirm_pass = isset($_POST["password2"]) ? trim($_POST["password2"]) : "";

    // Check for empty fields
    if (empty($user1) || empty($user2) || empty($email1) || empty($email2) || empty($pass) || empty($confirm_pass)) {
        exit("Error: All fields are required.");
    }

    // Validate username, email, and password matching
    if ($user1 !== $user2) {
        exit("Error: Usernames do not match.");
    }
    if ($email1 !== $email2) {
        exit("Error: Emails do not match.");
    }
    if (strlen($pass) < 8) {
        exit("Error: Password must be at least 8 characters long.");
    }
    if ($pass !== $confirm_pass) {
        exit("Error: Passwords do not match.");
    }

    // Hash password
    $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);

    // Check if email already exists
    $check_sql = "SELECT email FROM user_table WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email1);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $check_stmt->close();
        exit("Error: Email already exists.");
    }
    $check_stmt->close();

    // Insert new user
    $sql = "INSERT INTO user_table (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $user1, $email1, $hashed_pass);

    if ($stmt->execute()) {
        header("Location: home.html?signup=success");
        exit();
    } else {
        exit("Error: " . $stmt->error);
    }

    $stmt->close();
}
$conn->close();
?>
