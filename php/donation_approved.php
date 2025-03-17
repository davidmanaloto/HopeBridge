<?php
session_start();
require 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: admin_login.php");
    exit();
}

// Fetch approved donations
$query = "SELECT d.id, u.username AS donor_name, o.name AS organization_name, d.amount, d.receipt_path, d.date_created 
          FROM donations d
          JOIN user_table u ON d.user_id = u.id
          JOIN organizations o ON d.organization_id = o.id
          WHERE d.status = 'completed'
          ORDER BY d.date_created DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Donations</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Approved Donations</h2>
    <table>
        <thead>
            <tr>
                <th>Donor Name</th>
                <th>Organization</th>
                <th>Amount</th>
                <th>Receipt</th>
                <th>Date Approved</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['donor_name']) ?></td>
                    <td><?= htmlspecialchars($row['organization_name']) ?></td>
                    <td>$<?= number_format($row['amount'], 2) ?></td>
                    <td>
                        <?php if (!empty($row['receipt_path'])): ?>
                            <a href="<?= htmlspecialchars($row['receipt_path']) ?>" target="_blank">View Receipt</a>
                        <?php else: ?>
                            No Receipt
                        <?php endif; ?>
                    </td>
                    <td><?= $row['date_created'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php $conn->close(); ?>
