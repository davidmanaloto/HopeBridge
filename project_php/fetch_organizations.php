<?php
require 'db_connection.php';

header("Content-Type: application/json");

$sql = "SELECT name, id, tags, description FROM organizations";
$result = $conn->query($sql);

$organizations = array();
while ($row = $result->fetch_assoc()) {
    $organizations[] = $row;
}

echo json_encode($organizations);
$conn->close();
?>
