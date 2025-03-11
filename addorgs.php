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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $org_name = $_POST['org_name'];
    $org_type = $_POST['org_type'];
    $website_link = $_POST['website_link'];
    $donation_link = $_POST['donation_link'];
    $donation_type = $_POST['donation_type'];
    $tags = $_POST['tags']; // Expecting comma-separated values
    $description = $_POST['description'];

    $sql = "INSERT INTO organizations_table (org_name, org_type, website_link, donation_link, donation_type, description) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $org_name, $org_type, $website_link, $donation_link, $donation_type, $description);

    if ($stmt->execute()) {
        $org_id = $stmt->insert_id; // Get inserted organization's ID
        $stmt->close();
    
        foreach ($tags as $tag) {
            $sql = "INSERT INTO organization_tags (org_id, tag) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $org_id, $tag);
            $stmt->execute();
            $stmt->close();
        }

        echo "<script>alert('Organization added successfully!'); window.location.href='addorgs.php';</script>";
    } else {
        echo "<script>alert('Error adding organization.');</script>";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="adminpage.css"> <!-- Your external CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule="" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
    <body>
        <nav class="nav-menu">
            <div class="logo-container">
                <a href="admindashoard.php">
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
        <h2>Add Organization</h2>
        <div class="user-management-container"> <!-- Temporary Design-->
        <form method="POST" action="addorgs.php">
            <label for="org_name">Organization Name:</label>
            <input type="text" id="org_name" name="org_name" required><br>

            <label for="org_type">Organization Type:</label>
            <input type="text" id="org_type" name="org_type" required><br>
            
            <label for="website_link">Website Link:</label>
            <input type="url" id="website_link" name="website_link" required><br> <!-- change back to url after checking -->
            
            <label for="donation_link">Donation Link:</label>
            <input type="url" id="donation_link" name="donation_link" required><br>

            <label for="donation_type">Donation Type:</label>
            <input type="text" id="donation_type" name="donation_type" required><br>
            
            <label for="tags">Tags (Select multiple):</label>
            <select id="tags" name="tags[]" multiple required>
                <option value="fire">Fire</option>
                <option value="health">Health</option>
                <option value="flood">Flood Mitigation</option>
                <option value="rescue">Rescue</option>
                <option value="charity">Charity</option>
            </select>
            <div id="selectedTags"></div><br>
            <input type="hidden" id="tagsInput" name="tags_list">

            
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required></textarea><br>
            
            <button type="submit">Add Organization</button>
        </form> 
</div> 
</body>
<script src="main.js"></script>
<script>
    document.getElementById("tags").addEventListener("change", function () {
        let selectedTags = Array.from(this.selectedOptions).map(option => option.value);
        document.getElementById("tagsInput").value = selectedTags.join(","); // Store in hidden input
        displaySelectedTags(selectedTags);
    });

    function displaySelectedTags(tags) {
        let container = document.getElementById("selectedTags");
        container.innerHTML = "";
        tags.forEach(tag => {
            let tagElement = document.createElement("span");
            tagElement.textContent = tag;
            tagElement.classList.add("tag-item");
            container.appendChild(tagElement);
        });
    }
</script>

<style>
    #selectedTags {
        margin-top: 5px;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    .tag-item {
        background-color: #007bff;
        color: white;
        padding: 5px 10px;
        border-radius: 3px;
    }
</style>

</html>