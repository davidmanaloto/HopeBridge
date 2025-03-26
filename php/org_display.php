<?php
session_start();
require 'db_connection.php';

// Ensure admin is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: admin_login.php");
    exit();
}

// Handle JSON API requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');

    switch ($_GET['action']) {
        case 'get_verified_orgs':
            fetchVerifiedOrganizations();
            break;

        case 'delete_org':
            if (isset($_POST['id'])) {
                deleteOrganization($_POST['id']);
            }
            break;
        
        case 'edit_organization':
            if (isset($_POST['id'], $_POST['name'], $_POST['tags'], $_POST['description'])) {
                openEditOrganizationModal($_POST['id'], $_POST['name'], $_POST['tags'], $_POST['description']);
            } else {
                echo json_encode(['error' => 'Missing parameters']);
            }
            break;

        default:
            echo json_encode(['error' => 'Invalid request']);
            exit;
    }
}

// Fetch verified organizations
function fetchVerifiedOrganizations() {
    global $conn;
    $query = "SELECT id, name, email, description, tags, created_at FROM org_table WHERE is_verified = 1";
    $result = $conn->query($query);
    $orgs = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($orgs);
    exit;
}

// Delete organization
function deleteOrganization($id) {
    global $conn;
    $query = "DELETE FROM org_table WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode(['success' => $stmt->affected_rows > 0]);
    exit;
}
// Edit organization
function openEditOrganizationModal($id, $name, $tags, $description) {
    global $conn;
    
    $query = "UPDATE org_table SET name = ?, tags = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'SQL prepare error: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("sssi", $name, $tags, $description, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Execution failed: ' . $stmt->error]);
    }

    $stmt->close();
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Management</title>
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
            <a href="verify_management.php" class="nav-link user-management"><ion-icon name="people-outline"></ion-icon> Verify Org Requests</a>
            <a href="admin_logout.php" class="nav-link logout"><ion-icon name="log-out-outline"></ion-icon> Log Out</a>
        </div>
    </nav>
    <div class="user-management-container"> 
        <div class="user-management-header">
    <h2>Organization Management</h2>
    </div>
    
    <table class="user-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Tags</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>

            <div id="editOrganizationModal" class="modal">
            <div class="modal-content">
            <span class="close" onclick="closeEditOrganizationModal()">&times;</span>
            <h3>Edit Organization</h3>
            
            <input type="hidden" id="editOrgId">

            <label for="editOrgName">Name:</label>
            <input type="text" id="editOrgName"><br>

            <label for="editOrgTag">Tags:</label>
            <input type="text" id="editOrgTag"><br>

            <label for="editOrgDescription">Description:</label>
            <textarea id="editOrgDescription"></textarea><br>

            <button onclick="saveOrganizationChanges()">Save Changes</button>
            </div>
            </div>

        </thead>
        <tbody id="orgTableBody">
            <tr><td colspan="7" style="text-align: center;">Loading organizations...</td></tr>
        </tbody>
    </table>

    <script src="../js/org_display.js"></script>
</body>
</html>

<style>
    /* Basic modal styling */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;}
    .modal-content { background: white; margin: 15% auto; padding: 20px; width: 30%; border-radius: 8px; }
    .close { float: right; font-size: 20px; cursor: pointer; }
</style>
