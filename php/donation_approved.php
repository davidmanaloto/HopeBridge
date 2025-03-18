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

// Fetch approved donations
$query = "SELECT d.id, u.username AS donor_name, o.name AS organization_name, d.amount, d.receipt_path, d.date_created 
          FROM donations d
          JOIN user_table u ON d.user_id = u.id
          JOIN organizations o ON d.organization_id = o.id
          WHERE d.status = 'completed'
          ORDER BY d.date_created DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Donations</title>
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
            <a href="fetch_events.php" class="nav-link donation-management"><ion-icon name="people-outline"></ion-icon>Event Management</a>
            <a href="user_management.php" class="nav-link user-management"><ion-icon name="people-outline"></ion-icon>User Management</a>
            <a href="verify_management.php" class="nav-link user-management"><ion-icon name="people-outline"></ion-icon> Verify Requests</a>
            <a href="admin_logout.php" class="nav-link logout"><ion-icon name="log-out-outline"></ion-icon> Log Out</a>
        </div>
    </nav>
    <div class="user-management-container"> 
        <div class="user-management-header">
        <h2>Approved Donations</h2>
    </div>
    <table class="user-table">
        <thead>
            <tr>
                <th>Donor Name</th>
                <th>Organization</th>
                <th>Amount</th>
                <th>Receipt</th>
                <th>Date Approved</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['donor_name']) ?></td>
                    <td><?= htmlspecialchars($row['organization_name']) ?></td>
                    <td>$<?= number_format($row['amount'], 2) ?></td>
                    <td>
                        <?php if (!empty($row['receipt_path'])): ?>
                            <a href="<?= htmlspecialchars($row['receipt_path']) ?>" target="_blank">View Receipt</a>
                        <?php else: ?>
                            No Receipt
                        <?php endif; ?>
                    </td>
                    <td><?= $row['date_created'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php $conn->close(); ?>
