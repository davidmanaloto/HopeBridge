<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management</title>
    <script src="../js/project_management.js" defer></script>
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
            <h2>Project Management</h2>
    </div>
    <table class="user-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Creator</th>
                <th>Project Name</th>
                <th>Organization</th>
                <th>Donation Goal</th>
                <th>Funds Raised</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="projectsTableBody">
            <!-- Projects will be loaded here -->
        </tbody>
    </table>
</body>
</html>