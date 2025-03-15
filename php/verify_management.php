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
