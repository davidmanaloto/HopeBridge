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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="nav-menu">
        <div class="logo-container">
            <a href="admin_dashboard.php">
                <img src="hopebridge.jpg" alt="Company Logo" class="logo">
            </a>
            <h1 class="site-title">HopeBridge</h1>
        </div>
        <div class="menu-sidebar">
            <a href="admin_dashboard.php" class="nav-link home"><ion-icon name="home-outline"></ion-icon> Home</a>
            <a href="donation_management.php" class="nav-link donation-management"><ion-icon name="people-outline"></ion-icon>Donation Management</a>
            <a href="donation_approved.php" class="nav-link donation-management"><ion-icon name="people-outline"></ion-icon>Donation Approved</a>
            <a href="org_management.php" class="nav-link donation-management"><ion-icon name="people-outline"></ion-icon>Organizations</a>
            <a href="fetch_events.php" class="nav-link donation-management"><ion-icon name="people-outline"></ion-icon>Event Management</a>
            <a href="user_management.php" class="nav-link user-management"><ion-icon name="people-outline"></ion-icon>User Management</a>
            <a href="verify_management.php" class="nav-link user-management"><ion-icon name="people-outline"></ion-icon> Verify Requests</a>
            <a href="admin_logout.php" class="nav-link logout"><ion-icon name="log-out-outline"></ion-icon> Log Out</a>
        </div>
    </nav>
    <h2>User Verification Requests</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Reason</th>
                <th>Documentation</th>  
                <th>Verification Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="verification-table">
        </tbody>
    </table>

    <script src="../js/verify_management.js"></script>
</body>
</html>
