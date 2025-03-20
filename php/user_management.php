<?php
session_start();
require 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: admin_login.php");
    exit();
}
//Function to echo json to user_management.js
if (isset($_GET['action'])) {
    header('Content-Type: application/json');

    switch ($_GET['action']) {
        case 'get_users':
            $filter = $_GET['filter'] ?? 'all';
            $query = "SELECT id, username, email, role, status, is_verified FROM user_table WHERE role != 'Admin'";

            if ($filter === 'Verified') {
                $query .= " AND is_verified = 1";
            } elseif ($filter === 'User') {
                $query .= " AND is_verified = 0";
            }

            $result = $conn->query($query);
            $users = $result->fetch_all(MYSQLI_ASSOC);

            echo json_encode($users);
            exit;

        case 'toggle_status':
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
                $query = "UPDATE user_table SET status = IF(status='Active', 'Blocked', 'Active') WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                echo json_encode(['success' => $stmt->affected_rows > 0]);
                exit;
            }
            break;

        case 'delete_user':
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
                $query = "DELETE FROM user_table WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                echo json_encode(['success' => $stmt->affected_rows > 0]);
                exit;
            }
            break;
    }

    echo json_encode(['error' => 'Invalid request']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <script src="../js/user_management.js"></script>
    <link rel="stylesheet" href="../css/management.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="nav-menu">
        <div class="logo-container">
            <a href="admin_dashboard.php">
            <img src="../image/hopebridge.jpg" alt="Company Logo" class="logo">
            </a>
            <h1 class="site-title">HopeBridge</h1>
        </div>
        <div class="menu-sidebar">
            <a href="admin_dashboard.php" class="nav-link home"><ion-icon name="home-outline"></ion-icon> Home</a>
            <a href="donation_management.php" class="nav-link donation-management"><ion-icon name="people-outline"></ion-icon>Donation Management</a>
            <a href="donation_approved.php" class="nav-link donation-management"><ion-icon name="people-outline"></ion-icon>Donation Approved</a>
            <a href="org_management.php" class="nav-link donation-management"><ion-icon name="people-outline"></ion-icon>Organizations</a>
            <a href="project_view.php" class="nav-link donation-management"><ion-icon name="people-outline"></ion-icon>Project Management</a>
            <a href="user_management.php" class="nav-link user-management"><ion-icon name="people-outline"></ion-icon>User Management</a>
            <a href="verify_management.php" class="nav-link user-management"><ion-icon name="people-outline"></ion-icon> Verify Requests</a>
            <a href="admin_logout.php" class="nav-link logout"><ion-icon name="log-out-outline"></ion-icon> Log Out</a>
        </div>
    </nav>

    <div class="user-management-container"> 
        <div class="user-management-header">
    <h2>User Management</h2>
    <select id="filterSelect">
        <option value="all">All</option>
        <option value="Verified">Verified</option>
        <option value="User">Unverified</option>
    </select>
    </div>
    <div class="search-bar">
        <i class="fas fa-search"></i>
    <input type="text" id="searchInput" placeholder="Search users...">
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
</body>
</html>
