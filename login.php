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
    $pass = isset($_POST["password1"]) ? trim($_POST["password1"]) : "";
    $confirm_pass = isset($_POST["password2"]) ? trim($_POST["password2"]) : "";


    if (empty($user1) || empty($pass) || empty($user2) || empty($confirm_pass)) {
        exit("Error: Username and password are required.");
    }

    if ($user1 !== $user2) {
        exit("Error: Usernames do not match.");
    }

    if ($pass !== $confirm_pass) {
        exit("Error: Passwords do not match.");
    }

    $sql = "SELECT password FROM user_table WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user1);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows === 0) {
        $stmt->close();
        exit("Error: Invalid username or password.");
    }

    // Fetch hashed password
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    // Verify password
    if (password_verify($pass, $hashed_password)) {
        session_start();
        $_SESSION["username"] = $user1;
        header("Location: homepage.html?login=success"); // Redirect after login
        exit();
    } else {
        exit("Error: Invalid username or password.");
    }
}

$conn->close();
?>
