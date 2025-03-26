<?php
session_start(); // Start a session at the beginning of the script
require 'db_connection.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT id, username, email FROM user_table WHERE email='$email' AND password='$password'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    // Store user ID in session
    $_SESSION['user_id'] = $user['id'];
    echo json_encode([
        'status' => 'success',
        'id' => $user['id'],
        'username' => $user['username']
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid login credentials'
    ]);
}
?>
    