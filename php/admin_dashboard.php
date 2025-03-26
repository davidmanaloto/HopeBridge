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
// Fetch users from the database
//$sql = "SELECT id, username, email, status FROM user_table WHERE role != 'Admin'";
$sql = "SELECT u.id, u.username, u.email, u.status, u.verification_status, 
               COALESCE(SUM(d.amount), 0) AS amount 
        FROM user_table u
        LEFT JOIN donations d ON u.id = d.user_id
        WHERE u.role != 'Admin'
        GROUP BY u.id, u.verification_status";

// Fetch total users (excluding admins)
$totalUsersQuery = "SELECT COUNT(*) AS total FROM user_table WHERE role != 'Admin'";
$totalUsersResult = $conn->query($totalUsersQuery);
$totalUsers = ($totalUsersResult->fetch_assoc())['total'] ?? 0;

// Fetch total organizations
$totalOrgsQuery = "SELECT COUNT(*) AS total FROM organizations";
$totalOrgsResult = $conn->query($totalOrgsQuery);
$totalOrgs = ($totalOrgsResult->fetch_assoc())['total'] ?? 0;

// Fetch total projects
$totalProjectsQuery = "SELECT COUNT(*) AS total FROM projects";
$totalProjectsResult = $conn->query($totalProjectsQuery);
$totalProjects = ($totalProjectsResult->fetch_assoc())['total'] ?? 0;

// Fetch total donations amount
$totalDonationsQuery = "SELECT COALESCE(SUM(amount), 0) AS total FROM donations";
$totalDonationsResult = $conn->query($totalDonationsQuery);
$totalDonations = ($totalDonationsResult->fetch_assoc())['total'] ?? 0;

$result = $conn->query($sql);

// Check for query errors
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/management.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div id="toast" data-message="Your message here" data-type="success" style="display: none;"></div>
    
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
            <a href="verify_management.php" class="nav-link user-management"><ion-icon name="people-outline"></ion-icon> Verify Org Requests</a>
            <a href="admin_logout.php" class="nav-link logout"><ion-icon name="log-out-outline"></ion-icon> Log Out</a>
        </div>
    </nav>
    <div class="user-management-container"> 
        <div class="user-management-header">
            <h2>Admin Dashboard</h2>
    </div>

<div class="admin-dash-container">
    <div class="row-container">
        <div class="box-cont">
            <div class="card-box">
                <h5>Total Users</h5>
                <p><?php echo $totalUsers; ?></p>
            </div>
        </div>
        <div class="box-cont">
            <div class="card-box">
                <h5>Total Org</h5>
                <p><?php echo $totalOrgs; ?></p>
            </div>
        </div>
        <div class="box-cont">
            <div class="card-box">
                <h5>Total Projects</h5>
                <p><?php echo $totalProjects; ?></p>
            </div>
        </div>
        <div class="box-cont">
            <div class="card-box">
                <h5>Total Donations</h5>
                <p>$<?php echo number_format($totalDonations, 2); ?></p>
            </div>
        </div>
    </div>
</div>

    <table class="user-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Status</th>
                <th>Donations</th>
                <th>Verification</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['amount']); ?></td>
                    <td><?php echo htmlspecialchars($row['verification_status']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>