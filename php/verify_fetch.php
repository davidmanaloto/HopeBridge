<?php
require 'db_connection.php';

$query = "
    SELECT vr.id, u.username, u.email, vr.status, vr.document_path, vr.reason 
    FROM verification_requests vr
    JOIN user_table u ON vr.user_id = u.id
    WHERE vr.status = 'Pending'
";
$result = $conn->query($query);

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);
?>
