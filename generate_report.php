<?php
include 'db.php'; // Include the database connection file

// Initialize variables for filtering
$fbName = '';
$startDate = '';
$endDate = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fbName = $_POST['fb-name'];
    $startDate = $_POST['start-date'];
    $endDate = $_POST['end-date'];

    // Query to fetch player reports based on filters
    $sql = "SELECT fb_name, SUM(deposit_amount) AS total_deposit, SUM(bonus_amount) AS total_bonus, 
            SUM(freeplay_amount) AS total_freeplay, SUM(cashout_amount) AS total_cashout
            FROM (
                SELECT fb_name, deposit_amount, bonus_amount, 0 AS freeplay_amount, 0 AS cashout_amount
                FROM deposits
                WHERE fb_name LIKE :fb_name AND created_at BETWEEN :start_date AND :end_date
                UNION ALL
                SELECT fb_name, 0 AS deposit_amount, 0 AS bonus_amount, freeplay_amount, 0 AS cashout_amount
                FROM freeplay_deposits
                WHERE fb_name LIKE :fb_name AND created_at BETWEEN :start_date AND :end_date
                UNION ALL
                SELECT fb_name, 0 AS deposit_amount, 0 AS bonus_amount, 0 AS freeplay_amount, cashout_amount
                FROM cashouts
                WHERE fb_name LIKE :fb_name AND created_at BETWEEN :start_date AND :end_date
            ) AS combined
            GROUP BY fb_name";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':fb_name', '%' . $fbName . '%');
    $stmt->bindValue(':start_date', $startDate ?: '1970-01-01'); // Default to a very early date if not set
    $stmt->bindValue(':end_date', $endDate ?: date('Y-m-d')); // Default to today if not set
    $stmt->execute();
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Redirect back to reports page if accessed directly
    header("Location: reports.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generated Player Reports</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="index.html" class="link">Home</a></li>
                <li><a href="dashboard.html" class="link">Dashboard</a></li>
                <li><a href="deposit.html" class="link">Deposit</a></li>
                <li><a href="recent_deposit.php" class="link">Recent Deposits</a></li>
                <li><a href="freeplay_deposit.php" class="link">FreePlay Deposits</a></li>
                <li><a href="freeplay_history.php" class="link">FreePlay History</a></li>
                <li><a href="cashout.php" class="link">Cashout</a></li>
                <li><a href="cashout_history.php" class="link">Cashout History</a></li>
                <li><a href="reports.php" class="link active">Reports</a></li> <!-- Active Reports link -->
            </ul>
        </nav>
        <div class="main-content">
            <h2>Generated Player Reports</h2>
            <table id="reports-table" class="cashout-history">
                <thead>
                    <tr>
                        <th>FB Name</th>
                        <th>Total Deposit</th>
                        <th>Total Bonus</th>
                        <th>Total FreePlay</th>
                        <th>Total Cashout</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reports)): ?>
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><?= htmlspecialchars($report['fb_name']); ?></td>
                                <td><?= htmlspecialchars($report['total_deposit']); ?></td>
                                <td><?= htmlspecialchars($report['total_bonus']); ?></td>
                                <td><?= htmlspecialchars($report['total_freeplay']); ?></td>
                                <td><?= htmlspecialchars($report['total_cashout']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No reports found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <a href="reports.php">Back to Reports</a>
        </div>
    </div>
</body>
</html>