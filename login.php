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
    $user = $_POST["username"];
    $pass = $_POST["password"];

    $sql = "SELECT password FROM user_table WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    $pass = trim($_POST["password"]); // Remove spaces from input
    $hashed_password = trim($hashed_password); // Remove spaces from stored hash (just in case)

    // DB CONNECTION DEBUG
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // PASSWORD HASH DEBUG
    //echo "Entered Password: " . $pass . "<br>";
    //echo "Stored Hash: " . $hashed_password . "<br>";

    if (password_verify($pass, $hashed_password)) {
        echo "Password matches!";
    } else {
        echo "Wrong password!<br>";
    }

    // NEW LOGIC
    if (password_verify($pass, $hashed_password)) { // FIX THIS 11/02/2025 //FIXED IN: 5hours 11/02/2025
        echo "Login successful!";
    } else {
        echo "Invalid username or password.";
    }

    //OLD LOGIC
    //if ($hashed_password && password_verify($_POST["password"], $hashed_password))
    
    //echo password_hash("your_actual_password", PASSWORD_BCRYPT);

    // DEBUG FOR QUERY RUNS CORRECTLY
    //    if ($stmt->execute()) {
    //        if ($stmt->fetch()) {
    //           echo "User found!";
    //        } else {
    //            echo "User not found!";
    //        }
    //    } else {
    //        echo "Query failed: " . $stmt->error;
    //    }

    $stmt->close();
}
$conn->close();
?>
