<?php
session_start(); // Start session to store email for verification

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure you have PHPMailer installed via Composer

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "hopebridge_database"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST["emailadd"], FILTER_SANITIZE_EMAIL); // Sanitize input

    // Generate a random 6-digit verification code
    $verification_code = rand(100000, 999999);

    // Store the verification code in the database
    $sql = "UPDATE user_table SET verification_code = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $verification_code, $email);
    
    if ($stmt->execute()) {
        // Store email in session for later verification
        $_SESSION["email"] = $email;

        // Send the verification code via email
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'hopebridge.noreply@gmail.com';
            $mail->Password = 'ylju tvwq gmxw wrpq'; // Use App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('hopebridge.noreply@gmail.com', 'HopeBridge');
            $mail->addAddress($email);
            $mail->Subject = 'Your HopeBridge Verification Code';
            $mail->Body = "Your verification code is: $verification_code";

            $mail->send();
            echo "Verification code sent! Please check your email.";
            header("Location: verify.html");
        } catch (Exception $e) {
            echo "Error sending email: " . $mail->ErrorInfo;
        }
    } else {
        echo "Error updating database: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
