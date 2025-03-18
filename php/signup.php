<?php
require 'db_connection.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if required fields are provided
if (!isset($_POST['email'], $_POST['username'], $_POST['password'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Missing required fields."]);
    exit();
}

// Get and sanitize input
$email = trim($_POST['email']);
$username = trim($_POST['username']);
$password = trim($_POST['password']);

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid email format."]);
    exit();
}


if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
    http_response_code(400);
    echo json_encode(["error" => "Username can only contain letters and numbers."]);
    exit();
}


if (!preg_match('/^[a-zA-Z0-9]+$/', $password)) {
    http_response_code(400);
    echo json_encode(["error" => "Password can only contain letters and numbers."]);
    exit();
}


$check_email_qry = $conn->prepare("SELECT email FROM user_table WHERE email = ?");
$check_email_qry->bind_param("s", $email);
$check_email_qry->execute();
$check_email_qry->store_result();

if ($check_email_qry->num_rows > 0) {
    http_response_code(409); // Conflict
    echo json_encode(["error" => "Email already in use."]);
    exit();
}


$check_user_qry = $conn->prepare("SELECT username FROM user_table WHERE username = ?");
$check_user_qry->bind_param("s", $username);
$check_user_qry->execute();
$check_user_qry->store_result();

if ($check_user_qry->num_rows > 0) {
    http_response_code(409);
    echo json_encode(["error" => "Username already taken."]);
    exit();
}

// Encrypt password
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Insert user into database
$insert_stmt = $conn->prepare("INSERT INTO user_table (email, username, password) VALUES (?, ?, ?)");
$insert_stmt->bind_param("sss", $email, $username, $password);

if ($insert_stmt->execute()) {
    http_response_code(201); // Created
    echo json_encode(["message" => "Signup successful."]);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Signup failed."]);
}

// Close statements and connection
$check_email_qry->close();
$check_user_qry->close();
$insert_stmt->close();
$conn->close();
?>
