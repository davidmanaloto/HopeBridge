<?php
session_start(); 
echo"<pre>";
print_r($_SESSION);
echo"</pre>";

if (isset($_SESSION["user_id"])) {
    echo "User is logged in as: " . $_SESSION["username"];
} else {
    echo "No active session found. Redirecting to login...";
    header("Location: home.html?error=NoActiveSessionFound"); // Redirect if session is missing
    exit();
}   
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// User is logged in
echo "Welcome, " . htmlspecialchars($_SESSION['username']);
?>
<a href="logout.php">Logout</a>