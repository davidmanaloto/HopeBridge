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
    <title>Donation Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Donation Management</h2>

    <input type="text" id="searchInput" placeholder="Search donations...">
    <select id="filterStatus">
        <option value="all">All</option>
        <option value="Pending">Pending</option>
        <option value="Completed">Completed</option>
        <option value="Failed">Failed</option>
    </select>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Donor Name</th>
                <th>Organization Name</th>
                <th>Amount</th>
                <th>Receipt</th>
                <th>Date_created</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="donationTableBody">
            <tr><td colspan="6" style="text-align: center;">Loading donations...</td></tr>
        </tbody>
    </table>

    <script src="../js/donations.js"></script>
</body>
</html>
