<?php
require 'db_connection.php';

$action = $_GET['action'] ?? '';

if ($action == 'get_organizations') {
    $query = "SELECT id, name, website, donation_link, tags, description FROM organizations ORDER BY id ASC";
    $result = $conn->query($query);

    $organizations = [];
    while ($row = $result->fetch_assoc()) {
        $organizations[] = $row;
    }
    echo json_encode($organizations);
    exit;
}

if ($action == 'add_organization') {
    $name = $_POST['name'] ?? '';
    $website = $_POST['website'] ?? '';
    $donation_link = $_POST['donation_link'] ?? '';
    $tags = $_POST['tags'] ?? '';
    $description = $_POST['description'] ?? '';

    if ($name && $website && $donation_link) {
        $stmt = $conn->prepare("INSERT INTO organizations (name, website, donation_link, tags, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $website, $donation_link, $tags, $description);
        echo json_encode(['success' => $stmt->execute()]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    }
    exit;
}

if ($action == 'edit_organization') {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $website = $_POST['website'] ?? '';
    $donation_link = $_POST['donation_link'] ?? '';
    $tags = $_POST['tags'] ?? '';
    $description = $_POST['description'] ?? '';

    if ($id && $name && $website && $donation_link) {
        $stmt = $conn->prepare("UPDATE organizations SET name = ?, website = ?, donation_link = ?, tags = ?, description = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $website, $donation_link, $tags, $description, $id);
        echo json_encode(['success' => $stmt->execute()]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    }
    exit;
}

if ($action == 'delete_organization') {
    $id = $_GET['id'] ?? '';
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM organizations WHERE id = ?");
        $stmt->bind_param("i", $id);
        echo json_encode(['success' => $stmt->execute()]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid ID']);
    }
    exit;
}
?>
