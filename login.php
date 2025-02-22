<?php
session_start();
//echo "<pre>";
//print_r($_SESSION); // Displays all session variables
//echo "</pre>";
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
        $_SESSION["toast"] = ["type" => "error", "message" => "All fields required"];
        header("Location: home.php");
        exit();
    }

    if ($user1 !== $user2) {
        $_SESSION["toast"] = ["type" => "error", "message" => "Username does not match"];
        header("Location: home.php");
        exit();
    }

    if ($pass !== $confirm_pass) {
        $_SESSION["toast"] = ["type" => "error", "message" => "Password does not match"];
        exit();
    }

    $sql = "SELECT id, password FROM user_table WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user1);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $_SESSION["toast"] = ["type" => "error", "message" => "Username does not exist"];
        header("Location: home.php?error=InvalidCredentials");
        exit();
    }

    // Fetch hashed password
    $stmt->bind_result($user_id, $hashed_password);
    $stmt->fetch();
    $stmt->close();

    // Verify password
    if (password_verify($pass, $hashed_password)) {
        session_start();
        $_SESSION["user_id"] = $user_id;
        $_SESSION["username"] = $user1;
        $_SESSION["token"] = bin2hex(random_bytes(32));

        setcookie("session_token", $_SESSION["token"], [
            "expires" => time() + 3600, // 1-hour expiration
            "path" => "/",
            "secure" => true, // Only for HTTPS
            "httponly" => true, // Prevent JavaScript access
            "samesite" => "Strict"
        ]);

        $_SESSION["toast"] = ["type" => "success", "message" => "Log in successful"];
        header("Location: homepage.html?login=success"); // Redirect after login
        exit();
    } else {
        $_SESSION["toast"] = ["type" => "error", "message" => "Invalid Credentials"];
        header("Location: home.php?error=InvalidCredentials");
        exit();
    }
}

$conn->close();
?>
