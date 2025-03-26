<?php
session_start(); // Start a session
require 'db_connection.php';

if (!isset($_POST['email'], $_POST['password'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing email or password'
    ]);
    exit();
}

$email = trim($_POST['email']);
$password = trim($_POST['password']);

$stmt = $conn->prepare("SELECT id, organization_name, email, password FROM org_user_table WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        // ✅ Store email in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['org_email'] = $user['email']; // Store email in session

        echo json_encode([
            'status' => 'success',
            'id' => $user['id'],
            'email' => $user['email']
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid login credentials'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid login credentials'
    ]);
}

$stmt->close();
$conn->close();
?>
