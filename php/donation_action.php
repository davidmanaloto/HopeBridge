<?php
require 'db_connection.php';

$action = $_GET['action'] ?? '';

if ($action == 'get_donations') {
    $query = "SELECT d.id, u.username AS donor_name, o.name AS organization_name, d.amount, d.status, d.receipt_path, d.date_created 
              FROM donations d
              JOIN user_table u ON d.user_id = u.id
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

    if ($id && $newStatus === 'Completed') {
        $stmt = $conn->prepare("UPDATE donations SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $newStatus, $id);
        echo json_encode(['success' => $stmt->execute()]);
    }
    exit;
}
?>
