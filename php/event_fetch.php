<?php
// fetch_events.php
require 'db_connection.php';

$sql = "SELECT id, event_name, organizer, event_date FROM fundraising_events ORDER BY event_date DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['event_name']}</td>
                <td>{$row['organizer']}</td>
                <td>{$row['event_date']}</td>
                <td><button class='delete-btn' data-id='{$row['id']}'>Delete</button></td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No events found.</td></tr>";
}
$conn->close();

// delete_event.php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_id'])) {
    $event_id = intval($_POST['event_id']);
    $stmt = $conn->prepare("DELETE FROM fundraising_events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
}
$conn->close();
?>