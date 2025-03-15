<?php
session_start();
require 'db_connection.php';

// Ensure only admins can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: admin_login.php");
    exit();
}

// Handle form submissions for approval or rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    
    if ($_POST['action'] === 'approve') {
        $update_sql = "UPDATE user_table SET status = 'Verified' WHERE id = ?";
    } elseif ($_POST['action'] === 'reject') {
        $update_sql = "UPDATE user_table SET status = 'Rejected' WHERE id = ?";
    }
    
    if (isset($update_sql)) {
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch pending verification requests
$sql = "SELECT u.id, u.username, v.document_path FROM user_table u 
        JOIN verification_requests v ON u.id = v.user_id 
        WHERE u.status = 'Pending'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Verification Requests</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Verification Requests</h2>
    <table border="1">
        <tr>
            <th>Username</th>
            <th>Document</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td>
                <a href="<?php echo htmlspecialchars($row['document_path']); ?>" target="_blank">View Document</a>
            </td>
            <td>
                <form method="POST">
                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="action" value="approve">Approve</button>
                    <button type="submit" name="action" value="reject">Reject</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
