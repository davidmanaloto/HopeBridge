<?php
require 'db_connection.php';

$query = "SELECT id, username, email, verification_status FROM user_table WHERE verification_status = 'Pending'";
$result = $conn->query($query);

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);
?>
