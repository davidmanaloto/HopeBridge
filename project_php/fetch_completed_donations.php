<?php

require 'db_connection.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch only completed donations
$sql = "SELECT project_name, username, date_created, amount 
        FROM donations 
        WHERE status = 'Completed' 
        ORDER BY date_created DESC";

$result = $conn->query($sql);

$donations = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $donations[] = [
            'username' => $row['username'],
            'projectName' => $row['project_name'],
            'amount' => $row['amount'],
            'dateCreated' => $row['date_created']
        ];
    }
}

// Send JSON response
echo json_encode($donations);

// Close connection
$conn->close();

?>
