<<<<<<< Updated upstream
<?php
session_start();
$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if needed
$dbname = "hopebridge_database"; // Change to your database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    header("Location: home.php?signup=error=connection=database");
    exit("Error". $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user1 = isset($_POST["username1"]) ? trim($_POST["username1"]) : "";
    $user2 = isset($_POST["username2"]) ? trim($_POST["username2"]) : "";
    $email1 = isset($_POST["email1"]) ? trim($_POST["email1"]) : "";
    $email2 = isset($_POST["email2"]) ? trim($_POST["email2"]) : "";
    $pass = isset($_POST["password1"]) ? trim($_POST["password1"]) : "";
    $confirm_pass = isset($_POST["password2"]) ? trim($_POST["password2"]) : "";

    // Check for empty fields
    if (empty($user1) || empty($user2) || empty($email1) || empty($email2) || empty($pass) || empty($confirm_pass)) {
        $_SESSION["toast"] = ["type" => "error", "message" => "All fields required"];
        exit();
    }

    // Validate username, email, and password matching
    if ($user1 !== $user2) {
        $_SESSION["toast"] = ["type" => "error", "message" => "User does not match"];
        header("Location: home.php?signup=error=username=match");
        exit();
    }
    if ($email1 !== $email2) {
        $_SESSION["toast"] = ["type" => "error", "message" => "Email does not match"];
        header("Location: home.php?signup=error=email=match");
        exit();
    }
    if (strlen($pass) < 8) {
        $_SESSION["toast"] = ["type" => "error", "message" => "Password must be 8 characters long"];
        header("Location: home.php?signup=password=lenght");
        exit();
    }
    if ($pass !== $confirm_pass) {
        $_SESSION["toast"] = ["type" => "error", "message" => "Password does not match"];
        header("Location: home.php?signup=error=password=match");
        exit();
    }

    // Hash password
    $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);

    // Check if username already exists
    $check_sql = "SELECT username FROM user_table WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $user1);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $check_stmt->close();
        $_SESSION["toast"] = ["type" => "error", "message" => "Username already exist"];
        header("Location: home.html?signup=error=username=database");
        exit();
    }

    // Check if email already exists
    $check_sql = "SELECT email FROM user_table WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email1);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $check_stmt->close();
        $_SESSION["toast"] = ["type" => "error", "message" => "Email already exist"];
        header("Location: home.html?signup=error=email=database");
        exit();
    }
    $check_stmt->close();

    // Insert new user
    $sql = "INSERT INTO user_table (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $user1, $email1, $hashed_pass);

    if ($stmt->execute()) {
        $_SESSION["toast"] = ["type" => "success", "message" => "Sign up successful"];
        header("Location: home.html?signup=success");
        exit();
    } else {
        $_SESSION["toast"] = ["type" => "error", "message" => "Inserting User to Database Error"] .$stmt->error;
        header("Location: home.html?signup=error=database=error");
        exit();
    }

    $stmt->close();
}
$conn->close();
?>
=======
<?php
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
    $username = isset($_POST["username1"]) ? trim($_POST["username1"]) : "";
    $email = isset($_POST["email1"]) ? trim($_POST["email1"]) : "";
    $password = isset($_POST["password1"]) ? trim($_POST["password1"]) : "";

    if (empty($username) || empty($email) || empty($password)) {
        header("Location: home.php?error=All fields are required&modal=signupModal");
        exit();
    }

    $stmt = $conn->prepare("SELECT id FROM user_table WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        header("Location: home.php?error=Username or Email already exists&modal=signupModal");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO user_table (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    $stmt->execute();

    // Successful signup (Redirect to login)
    header("Location: home.php?success=Account created successfully&modal=loginModal");
    exit();
}
?>
>>>>>>> Stashed changes
