<?php
session_start();
require 'db_connection.php';

// Check if organization is logged in
if (!isset($_SESSION['organization_id'])) {
    die(json_encode(["error" => "Organization not logged in."]));
}

$org_id = $_SESSION['organization_id']; // Get org ID from session

// Fetch donations for the logged-in organization
$sql = "SELECT d.project_name, d.username, d.date_created, d.amount 
        FROM donations d
        INNER JOIN projects p ON d.project_id = p.project_id
        WHERE d.status = 'Completed' AND d.organization_id = ?
        ORDER BY d.date_created DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $org_id);
$stmt->execute();
$result = $stmt->get_result();

$donations = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $donations[] = [
            'username' => $row['username'],
            'projectName' => $row['project_name'],
            'amount' => $row['amount'],
            'dateCreated' => $row['date_created']
        ];
    }
}

// Send JSON response
echo json_encode($donations);

$stmt->close();
$conn->close();
?>
