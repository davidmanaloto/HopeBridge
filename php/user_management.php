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
</head>
<body>
    <h2>User Management</h2>
    <select id="filterSelect">
        <option value="all">All</option>
        <option value="Verified">Verified</option>
        <option value="User">Unverified</option>
    </select>
    <input type="text" id="searchInput" placeholder="Search users...">
    
    <table>
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
