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
    $user = trim($_POST["username"]);
    $pass = password_hash(trim($_POST["password"]), PASSWORD_BCRYPT);

    if (strlen($pass) <= 8) {
        die("Error: Password must be at least 8 characters long."); // FIX THIS 11/02/2025 //FIXED IN: 3mins 11/02/2025
    }

    // Check if email already exists
    $check_sql = "SELECT username FROM user_table WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $user);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        die("Error: Email already exists.");
    }
    $check_stmt->close();

    // Insert
    $sql = "INSERT INTO user_table (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user, $pass);

    if ($stmt->execute()) {
        echo "Sign-up successful! <a href='login.html'>Login here</a>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
