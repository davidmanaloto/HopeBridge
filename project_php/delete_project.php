<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    // Decode JSON input
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['project_name'])) {
        echo json_encode(["error" => "Project name is required"]);
        exit;
    }

    $project_name = $input['project_name'];

    $stmt = $conn->prepare("DELETE FROM projects WHERE project_name = ?");
    $stmt->bind_param("s", $project_name);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Project deleted successfully"]);
    } else {
        echo json_encode(["error" => "Error deleting project"]);
    }

    $stmt->close();
    $conn->close();
}
?>
