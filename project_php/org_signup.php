<?php
require 'db_connection.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if required fields are provided
if (!isset($_POST['email'], $_POST['name'], $_POST['password'], $_POST['contact_number'],$_POST['address'], $_POST['verification_reason'], $_POST['verification_document'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Missing required fields."]);
    exit();
}

// Get and sanitize input
$email = trim($_POST['email']);
$organization_name = trim($_POST['name']);
$password = trim($_POST['password']);
$contact_number = trim($_POST['contact_number']);
$address = trim($_POST['address']);
$verification_reason = trim($_POST['verification_reason']);
$verification_document = trim($_POST['verification_document']); // This should be a file path if handling file uploads

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid email format."]);
    exit();
}

// Validate organization name (letters and numbers only)
if (!preg_match('/^[a-zA-Z0-9\s]+$/', $organization_name)) {
    http_response_code(400);
    echo json_encode(["error" => "Organization name can only contain letters, numbers, and spaces."]);
    exit();
}

// Validate password (letters and numbers only)
if (!preg_match('/^[a-zA-Z0-9]+$/', $password)) {
    http_response_code(400);
    echo json_encode(["error" => "Password can only contain letters and numbers."]);
    exit();
}

// Validate contact number (digits only, 10-15 characters)
if (!preg_match('/^\d{10,15}$/', $contact_number)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid contact number. Must be 10-15 digits."]);
    exit();
}

// Check if email already exists
$check_email_qry = $conn->prepare("SELECT email FROM organizations WHERE email = ?");
$check_email_qry->bind_param("s", $email);
$check_email_qry->execute();
$check_email_qry->store_result();

if ($check_email_qry->num_rows > 0) {
    http_response_code(409); // Conflict
    echo json_encode(["error" => "Email already in use."]);
    exit();
}

// Check if organization name already exists
$check_user_qry = $conn->prepare("SELECT name FROM organizations WHERE name = ?");
$check_user_qry->bind_param("s", $organization_name);
$check_user_qry->execute();
$check_user_qry->store_result();

if ($check_user_qry->num_rows > 0) {
    http_response_code(409);
    echo json_encode(["error" => "Organization name already taken."]);
    exit();
}

// Encrypt password
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Insert organization into database with all fields
$insert_stmt = $conn->prepare("INSERT INTO organizations (email, name, password, contact_number, address, verification_reason, verification_document) VALUES (?, ?, ?, ?, ?, ?, ?)");
$insert_stmt->bind_param("sssssss", $email, $organization_name, $hashed_password, $contact_number, $address, $verification_reason, $verification_document);

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
