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
    $user1 = isset($_POST["username1"]) ? trim($_POST["username1"]) : "";
    $user2 = isset($_POST["username2"]) ? trim($_POST["username2"]) : "";
    $pass = isset($_POST["password1"]) ? trim($_POST["password1"]) : "";
    $confirm_pass = isset($_POST["password2"]) ? trim($_POST["password2"]) : "";

    if (empty($user1)||empty($user2)||empty($pass)||empty($confirm_pass)) {
        die("Error: All fields are required.");
    }

    if ($user1 !== $user2) {
        die("Error: Usernames do not match.");
    }

    if ($pass !== $confirm_pass) {  
        die("Passwords do not match.");
        var_dump($pass, $confirm_pass);
        exit();
    }

    $sql = "SELECT password FROM user_table WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user1);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    $hashed_password = trim($hashed_password); // Remove spaces from stored hash (just in case)

    // DB CONNECTION DEBUG
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // PASSWORD HASH DEBUG
    //echo "Entered Password: " . $pass . "<br>";
    //echo "Stored Hash: " . $hashed_password . "<br>";

    // NEW LOGIC
    if (password_verify($pass, $hashed_password)) { // FIX THIS 11/02/2025 //FIXED IN: 5hours 11/02/2025
        die("Login successful!");
    } else {
        die("Invalid username or password.");
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
