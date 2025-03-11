<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hopebridge_database";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['action']) && $_GET['action'] === 'delete_org' && isset($_GET['id'])) {
    $orgId = intval($_GET['id']);
    $deleteSql = "DELETE FROM organizations_table WHERE org_id = ?";
    
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $orgId);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to delete organization."]);
    }
    exit; // Stop further execution
}

$sql = "SELECT o.org_id, o.org_name AS org_name, o.website_link AS website_link, o.donation_link, o.description, 
        GROUP_CONCAT(t.tag SEPARATOR ', ') AS tags 
        FROM organizations_table o 
        LEFT JOIN organization_tags t ON o.org_id = t.org_id 
        GROUP BY o.org_id 
        ORDER BY o.org_name ASC";
$result = $conn->query($sql);
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
    <script nomodule="" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
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
            <a href="donations.php" class="nav-link user-management"><ion-icon name="people-outline"></ion-icon>Donations</a>
            <a href="verifyuser.php" class="nav-link user-management"><ion-icon name="people-outline"></ion-icon> Verify</a>
            <a href="user-management.php" class="nav-link user-management"><ion-icon name="people-outline"></ion-icon> User Management</a>
            <!--<a href="reports.html" class="nav-link reports"><ion-icon name="document-text-outline"></ion-icon> Reports</a>-->
            <a href="addorgs.php" class="nav-link reports"><ion-icon name="document-text-outline"></ion-icon> Add Orgs</a><!-- Added -->
            <a href="organizations.php" class="nav-link reports"><ion-icon name="document-text-outline"></ion-icon> Organizations</a>
            <!--<a href="settings.html" class="nav-link settings"><ion-icon name="settings-outline"></ion-icon> Settings</a>-->
            <a href="logout.php" class="nav-link logout"><ion-icon name="log-out-outline"></ion-icon> Log Out</a>
            </div>
        </nav>
        
        <main class="admin-content">
        <h2>Manage Organizations</h2>
        
        <div class="search-filter-container">
            <div class="search-bar">
                <ion-icon name="search-outline"></ion-icon>
                <input type="text" id="searchOrg" placeholder="Search organizations...">
            </div>
            <select id="filterType">
                <option value="">Filter by Type</option>
                <option value="NGO">NGO</option>
                <option value="Healthcare">Healthcare</option>
                <option value="Education">Education</option>
            </select>
            <select id="filterDonation">
                <option value="">Filter by Donation Type</option>
                <option value="Monetary">Monetary</option>
                <option value="Food">Food</option>
                <option value="Clothes">Clothes</option>
            </select>
            <button id="addOrgBtn">Add Organization</button>
        </div>

        <table class="org-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Website</th>
                    <th>Donation Link</th>
                    <th>Tags</th>
                    <th>Description</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="orgList">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="org-name"><?php echo htmlspecialchars($row['org_name']); ?></td>
                        <td><a href="<?php echo htmlspecialchars($row['website_link']); ?>" target="_blank">Visit</a></td>
                        <td>
                            <?php if (!empty($row['donation_link'])): ?>
                                <a href="<?php echo htmlspecialchars($row['donation_link']); ?>" target="_blank">Donate</a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td class="org-tags"><?php echo htmlspecialchars($row['tags']); ?></td>
                        <td class="org-description"><?php echo nl2br(htmlspecialchars($row['description'])); ?></td>
                        <td>
                        <button class="edit-btn">Edit</button>
                        <button class="delete-btn" data-id="<?php echo $row['org_id']; ?>">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
</body>
<script src="main.js"></script>
<script src="org_management.js"></script>
</html>

<?php $conn->close(); ?>