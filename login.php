<<<<<<< Updated upstream
<?php
session_start();
//echo "<pre>";
//print_r($_SESSION); // Displays all session variables
//echo "</pre>";
$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if needed
$dbname = "hopebridge_database"; // Change to your database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    exit("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user1 = isset($_POST["username1"]) ? trim($_POST["username1"]) : "";
    $user2 = isset($_POST["username2"]) ? trim($_POST["username2"]) : "";
    $pass = isset($_POST["password1"]) ? trim($_POST["password1"]) : "";
    $confirm_pass = isset($_POST["password2"]) ? trim($_POST["password2"]) : "";


    if (empty($user1) || empty($pass) || empty($user2) || empty($confirm_pass)) {
        $_SESSION["toast"] = ["type" => "error", "message" => "All fields required"];
        header("Location: home.php");
        exit();
    }

    if ($user1 !== $user2) {
        $_SESSION["toast"] = ["type" => "error", "message" => "Username does not match"];
        header("Location: home.php");
        exit();
    }

    if ($pass !== $confirm_pass) {
        $_SESSION["toast"] = ["type" => "error", "message" => "Password does not match"];
        exit();
    }

    $sql = "SELECT id, password FROM user_table WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user1);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $_SESSION["toast"] = ["type" => "error", "message" => "Username does not exist"];
        header("Location: home.php?error=InvalidCredentials");
        exit();
    }

    // Fetch hashed password
    $stmt->bind_result($user_id, $hashed_password);
    $stmt->fetch();
    $stmt->close();

    // Verify password
    if (password_verify($pass, $hashed_password)) {
        session_start();
        $_SESSION["user_id"] = $user_id;
        $_SESSION["username"] = $user1;
        $_SESSION["token"] = bin2hex(random_bytes(32));

        setcookie("session_token", $_SESSION["token"], [
            "expires" => time() + 3600, // 1-hour expiration
            "path" => "/",
            "secure" => true, // Only for HTTPS
            "httponly" => true, // Prevent JavaScript access
            "samesite" => "Strict"
        ]);

        $_SESSION["toast"] = ["type" => "success", "message" => "Log in successful"];
        header("Location: homepage.html?login=success"); // Redirect after login
        exit();
    } else {
        $_SESSION["toast"] = ["type" => "error", "message" => "Invalid Credentials"];
        header("Location: home.php?error=InvalidCredentials");
        exit();
    }
}

$conn->close();
?>
=======
<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
session_start();
$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if needed
$dbname = "hopebridge_database"; // Change to your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = isset($_POST["username1"]) ? trim($_POST["username1"]) : "";
    $pass = isset($_POST["password1"]) ? trim($_POST["password1"]) : "";

    if (empty($user) || empty($pass)) {
        header("Location: home.php?error=Username and Password are required&modal=loginModal");
        exit();
    }

    $stmt = $conn->prepare("SELECT password, role, status FROM user_table WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->bind_result($hashed_password, $role, $status);
    $stmt->fetch();
    $stmt->close();

    if (!$hashed_password || !password_verify($pass, $hashed_password)) {
        header("Location: home.php?error=Invalid username or password&modal=loginModal");
        exit();
    }

    if ($status === 'Blocked') {
        header("Location: home.php?error=Your account has been blocked by the admin.&modal=loginModal");
        exit();
    }

    $_SESSION["username"] = $user;
    $_SESSION["role"] = $role;

    // Redirect to Admin Login based on role
    if ($role === 'User') {
        header("Location: adminlogin.php");
        exit();
    }else{
        // Successful login (Redirect to dashboard)
        header("Location: homepage.html");
        exit();
    }

}
?>
>>>>>>> Stashed changes
