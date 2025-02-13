<?php
session_start(); // Needed if storing email in session

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "hopebridge_database"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : $_SESSION['email']; // Use session if email is not in POST
    $user_code = trim($_POST["verify"]);

    $sql = "SELECT verification_code FROM user_table WHERE email = ? AND verified = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($correct_code);
    
    if (!$stmt->fetch()) {
        echo "Email not found or already verified.";
        exit;
    }
    
    $stmt->close();

    if ($user_code == $correct_code) {
        $update_sql = "UPDATE user_table SET verified = 1 WHERE email = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("s", $email);
        if ($update_stmt->execute()) {
            $update_stmt->close();
            header("Location: login.html");
            exit; 
        } else {
            echo "Error verifying email.";
        }
    } else {
        echo "Invalid verification code.";
    }
}
$conn->close();
?>
