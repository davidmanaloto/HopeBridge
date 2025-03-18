<?php
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['user_id']) || empty($_POST['user_id'])) {
        echo json_encode(["status" => "error", "message" => "User ID is required"]);
        exit;
    }

    if (!isset($_POST['project_name']) || empty($_POST['project_name'])) {
        echo json_encode(["status" => "error", "message" => "Project name is required"]);
        exit;
    }
    
    if (!isset($_POST['amount']) || empty($_POST['amount']) || !is_numeric($_POST['amount']) || $_POST['amount'] <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid donation amount"]);
        exit;
    }

    $user_id = intval($_POST['user_id']);
    $project_name = trim($_POST['project_name']);
    $amount = floatval($_POST['amount']); 

    // Get user name
    $user_query = "SELECT username FROM user_table WHERE id = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_stmt->store_result();
    
    if ($user_stmt->num_rows == 0) {
        echo json_encode(["status" => "error", "message" => "User does not exist"]);
        $user_stmt->close();
        $conn->close();
        exit;
    }

    $user_stmt->bind_result($username);
    $user_stmt->fetch();
    $user_stmt->close();

    // Get project details
    $check_query = "SELECT project_id, organization_id FROM projects WHERE project_name = ?";
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

    $check_stmt->bind_result($project_id, $organization_id);
    $check_stmt->fetch();
    $check_stmt->close();

    $conn->begin_transaction();

    try {
        // Update the project's funds_raised
        $update_query = "UPDATE projects SET funds_raised = funds_raised + ? WHERE project_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("di", $amount, $project_id);
        $update_stmt->execute();
        $update_stmt->close();

        // Insert the donation with the username
        $insert_query = "INSERT INTO donations (user_id, username, organization_id, amount, status) VALUES (?, ?, ?, ?, 'Pending')";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("isid", $user_id, $username, $organization_id, $amount);
        $insert_stmt->execute();
        $insert_stmt->close();

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
