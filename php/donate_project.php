<?php
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['project_name']) || empty($_POST['project_name'])) {
        echo json_encode(["status" => "error", "message" => "Project name is required"]);
        exit;
    }
    
    if (!isset($_POST['amount']) || empty($_POST['amount']) || !is_numeric($_POST['amount']) || $_POST['amount'] <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid donation amount"]);
        exit;
    }

    $project_name = trim($_POST['project_name']);
    $amount = floatval($_POST['amount']); 

    // Fetch user_id, project_id, and organization_id from projects table
    $check_query = "SELECT user_id, project_id, organization_id FROM projects WHERE project_name = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $project_name);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows == 0) {
        echo json_encode(["status" => "error", "message" => "Project does not exist"]);
        $check_stmt->close();
        $conn->close();
        exit;
    }

    $check_stmt->bind_result($user_id, $project_id, $organization_id);
    $check_stmt->fetch();
    $check_stmt->close();

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update funds_raised in projects table
        $update_query = "UPDATE projects SET funds_raised = funds_raised + ? WHERE project_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("di", $amount, $project_id);
        $update_stmt->execute();
        $update_stmt->close();

        // Insert into donations table
        $insert_query = "INSERT INTO donations (user_id, organization_id, amount, status) VALUES (?, ?, ?, 'Pending')";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("iid", $user_id, $organization_id, $amount);
        $insert_stmt->execute();
        $insert_stmt->close();

        // Commit transaction
        $conn->commit();
        
        echo json_encode(["status" => "success", "message" => "Donation added successfully"]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => "Transaction failed: " . $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
