<?php
require 'db_connection.php';

$action = $_GET['action'] ?? '';

if ($action == 'get_donations') {
    // Fetch donations with the donor's name
    $query = "SELECT d.id, d.username AS donor_name, o.name AS organization_name, d.amount, d.status, d.receipt_path, d.date_created 
              FROM donations d
              JOIN organizations o ON d.organization_id = o.id
              WHERE d.status = 'Pending'
              ORDER BY d.date_created DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();

    $donations = [];
    while ($row = $result->fetch_assoc()) {
        $donations[] = $row;
    }
    echo json_encode($donations);
    exit;
}

if ($action == 'update_status') {
    $id = $_POST['id'] ?? '';
    $newStatus = $_POST['status'] ?? '';

    if (empty($id) || empty($newStatus)) {
        echo json_encode(['success' => false, 'message' => 'Missing parameters']);
        exit;
    }

    if (!in_array($newStatus, ['Pending', 'Completed'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE donations SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
    exit;
}
?>
 => $stmt->execute()]);
    }
    exit;
}
?>
