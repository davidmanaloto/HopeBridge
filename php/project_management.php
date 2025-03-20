<?php
require_once 'db_connection.php'; // Ensure database connection

header('Content-Type: application/json');

// Fetch projects
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT p.project_id, p.project_name, p.project_summary, p.donation_goal, 
                     p.funds_raised, o.name AS organization_name, p.created_at, u.username AS creator_name
              FROM projects p
              LEFT JOIN organizations o ON p.organization_id = o.id
              LEFT JOIN user_table u ON p.user_id = u.id
              ORDER BY p.created_at DESC";

    $result = $conn->query($query);
    $projects = [];

    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }

    echo json_encode($projects);
    exit;
}

// Delete project
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['project_id'])) {
        $project_id = intval($data['project_id']);
        
        $deleteQuery = "DELETE FROM projects WHERE project_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $project_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Project deleted successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete project."]);
        }
        exit;
    }
}
?>

