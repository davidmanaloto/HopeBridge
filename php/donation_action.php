<?php
require 'db_connection.php';

$action = $_GET['action'] ?? '';

if ($action == 'get_donations') {
    $filter = $_GET['status'] ?? 'all';
    $query = "SELECT d.id, u.username AS donor_name, o.name AS organization_name, d.amount, d.status, d.receipt_path, d.date_created 
              FROM donations d
              JOIN user_table u ON d.user_id = u.id
              JOIN organizations o ON d.organization_id = o.id";
    
    if ($filter != 'all') {
        $query .= " WHERE status = ?";
    }
    $query .= " ORDER BY date_created DESC";

    $stmt = $conn->prepare($query);
    if ($filter != 'all') {
        $stmt->bind_param("s", $filter);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $donations = [];
    while ($row = $result->fetch_assoc()) {
        $donations[] = $row;
    }
    echo json_encode($donations);
    exit;
}

if ($action == 'delete_donation') {
    $id = $_GET['id'] ?? '';
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM donations WHERE id = ?");
        $stmt->bind_param("i", $id);
        echo json_encode(['success' => $stmt->execute()]);
    }
    exit;
}

if ($action == 'update_status') {
    $id = $_POST['id'] ?? '';
    $newStatus = $_POST['status'] ?? '';
    
    if ($id && in_array($newStatus, ['Pending', 'Completed', 'Failed'])) {
        $stmt = $conn->prepare("UPDATE donations SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $newStatus, $id);
        echo json_encode(['success' => $stmt->execute()]);
    }
    exit;
}
?>
