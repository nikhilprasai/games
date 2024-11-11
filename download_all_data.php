<?php
include 'db.php'; // Include the database connection file

// Fetch all player data from relevant tables
$sql = "SELECT * FROM deposits UNION SELECT * FROM bonuses UNION SELECT * FROM freeplay_deposits UNION SELECT * FROM cashouts";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate a CSV or Excel file for download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="all_player_data.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, array('FB Name', 'Deposit Amount', 'Bonus Amount', 'FreePlay Amount', 'Cashout Amount', 'Date')); // Add headers

foreach ($results as $row) {
    fputcsv($output, $row); // Output each row
}

fclose($output);
exit;
?>