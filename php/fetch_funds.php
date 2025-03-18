<?php
require 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // If project_name is missing, return all projects with their funds_raised
    if (!isset($_GET['project_name']) || empty($_GET['project_name'])) {
        $query = "SELECT project_name, funds_raised FROM projects";  // Fetch project_name and funds_raised
        $result = $conn->query($query);

        $projects = [];
        while ($row = $result->fetch_assoc()) {
            $projects[] = [
                "project_name" => $row['project_name'],
                "funds_raised" => $row['funds_raised']
            ]; // Store project_name and funds_raised values
        }

        echo json_encode($projects); // Return all projects with funds_raised
        exit;
    }

    $project_name = trim($_GET['project_name']);

    error_log("Received project_name: " . $project_name);

    // Prepare the SQL query to fetch specific project funds
    $query = "SELECT project_name, funds_raised FROM projects WHERE project_name = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $project_name);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($project_name, $funds_raised);
            $stmt->fetch();
            echo json_encode([
                "project_name" => $project_name,
                "funds_raised" => $funds_raised
            ]);
        } else {
            echo json_encode(["error" => "Project not found"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => "Database query failed: " . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>
