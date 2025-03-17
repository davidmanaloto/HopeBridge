<?php
session_start();
require 'db_connection.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Management</title>
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
    <h2>Organization Management</h2>
    <button onclick="showAddOrganizationModal()">Add Organization</button>

    <div id="addOrganizationModal" style="display: none;">
    <h3>Add Organization</h3>
    <label for="orgName">Name:</label>
    <input id="orgName" placeholder="Organization Name">

    <label for="orgWebsite">Website:</label>
    <input id="orgWebsite" placeholder="Website">

    <label for="orgDonationLink">Donation Link:</label>
    <input id="orgDonationLink" placeholder="Donation Page URL">

    <label for="orgTags">Tags:</label>
    <input id="orgTags" placeholder="Tags">

    <label for="orgDescription">Description:</label>
    <textarea id="orgDescription"></textarea>


    <button onclick="addOrganization()">Submit</button>
    <button onclick="closeAddOrganizationModal()">Close</button>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Website</th>
                <th>Donation</th>
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
            <input type="text" id="editOrgName">

            <label for="editOrgWebsite">Website:</label>
            <input type="text" id="editOrgWebsite">

            <label for="editOrgDonationLink">Donation Link:</label>
            <input type="text" id="editOrgDonationLink">

            <label for="editOrgTag">Tags:</label>
            <input type="text" id="editOrgTag">

            <label for="editOrgDescription">Description:</label>
            <textarea id="editOrgDescription"></textarea>

            <button onclick="saveOrganizationChanges()">Save Changes</button>
            </div>
            </div>

        </thead>
        <tbody id="organizationTableBody">
            <tr><td colspan="5" style="text-align: center;">Loading organizations...</td></tr>
        </tbody>
    </table>

    <script src="../js/org_management.js"></script>
</body>
</html>

<style>
    /* Basic modal styling */
    .modal { display: none; position: fixed; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
    .modal-content { background: white; margin: 15% auto; padding: 20px; width: 30%; border-radius: 8px; }
    .close { float: right; font-size: 20px; cursor: pointer; }
</style>
