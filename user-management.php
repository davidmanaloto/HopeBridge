<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hopebridge_database";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'get_users':
            $sql = "SELECT id, username, email, role, status FROM user_table WHERE role != 'Admin'";
            $result = $conn->query($sql);
            $users = [];
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $users[] = $row;
                }
            }
            header('Content-Type: application/json');
            echo json_encode($users);
            break;

        case 'delete_user':
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $sql = "DELETE FROM user_table WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['error' => 'Error deleting user.']);
                }
            }
            break;

        case 'block_user':
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                // Toggle status between 'Active' and 'Blocked'
                $sql = "UPDATE user_table SET status = IF(status='Active', 'Blocked', 'Active') WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['error' => 'Error updating status.']);
                }
            }
            break;

        case 'edit_user':
            if (isset($_POST['id'], $_POST['username'], $_POST['email'], $_POST['role'])) {
                $id = $_POST['id'];
                $username = $_POST['username'];
                $email = $_POST['email'];
                $role = $_POST['role'];

                $sql = "UPDATE user_table SET username = ?, email = ?, role = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $username, $email, $role, $id);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['error' => 'Error updating user.']);
                }
            }
            break;

        default:
            echo json_encode(['error' => 'Invalid action.']);
            break;
    }
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="adminpage.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="nav-menu">
        <div class="logo-container">
            <a href="admindashboard.php">
                <img src="hopebridge.jpg" alt="Company Logo" class="logo">
            </a>
            <h1 class="site-title">HopeBridge</h1>
        </div>
        <div class="menu-sidebar">
            <a href="admindashboard.php" class="nav-link home"><ion-icon name="home-outline"></ion-icon> Home</a>
            <!--<a href="reports.html" class="nav-link reports"><ion-icon name="document-text-outline"></ion-icon> Reports</a>-->
            <a href="addorgs.php" class="nav-link reports"><ion-icon name="document-text-outline"></ion-icon> Add Orgs</a><!-- Added -->
            <a href="organizations.php" class="nav-link reports"><ion-icon name="document-text-outline"></ion-icon> Organizations</a>
            <!--<a href="settings.html" class="nav-link settings"><ion-icon name="settings-outline"></ion-icon> Settings</a>--->
            <a href="logout.php" class="nav-link logout"><ion-icon name="log-out-outline"></ion-icon> Log Out</a>
        </div>
    </nav>
    
    <div class="user-management-container">
        <div class="user-management-header">
            <h2>User Management</h2>
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search users..." onkeyup="filterUsers()">
            </div>
        </div>

        <table class="user-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="userTableBody"></tbody>
        </table>
    </div>

    <script src="main.js"></script>
    <script src="user_management.js"></script>
</body>
</html>
<?php $conn->close(); ?>
