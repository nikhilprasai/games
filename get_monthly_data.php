<?php
include 'db.php'; // Include the database connection file

// Get the month and year from the query parameters
$month = $_GET['month'];
$year = $_GET['year'];

// Prepare the SQL query to fetch monthly data
$sql = "SELECT DATE_FORMAT(date_column, '%Y-%m-%d') AS date, 
               SUM(deposit_amount) AS total_deposits, 
               SUM(cashout_amount) AS total_wins 
        FROM transactions 
        WHERE DATE_FORMAT(date_column, '%Y-%m') = :month_year 
        GROUP BY DATE_FORMAT(date_column, '%Y-%m-%d')";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':month_year', $year . '-' . $month);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for the chart
$labels = [];
$deposits = [];
$wins = [];

// Check if results are empty
if (empty($results)) {
    // Return empty data structure
    echo json_encode([
        'labels' => [],
        'deposits' => [],
        'wins' => [],
        'total_deposits' => 0,
        'total_wins' => 0,
        'total_freeplay' => 0, // Assuming you want to return this as well
        'total_bonus' => 0 // Assuming you want to return this as well
    ]);
    exit;
}

foreach ($results as $row) {
    $labels[] = $row['date'];
    $deposits[] = (float)$row['total_deposits'];
    $wins[] = (float)$row['total_wins'];
}

// Calculate totals
$totalDeposits = array_sum($deposits);
$totalWins = array_sum($wins);

// Return the data as JSON
echo json_encode([
    'labels' => $labels,
    'deposits' => $deposits,
    'wins' => $wins,
    'total_deposits' => $totalDeposits,
    'total_wins' => $totalWins,
    'total_freeplay' => 0, // Replace with actual logic if needed
    'total_bonus' => 0 // Replace with actual logic if needed
]);
?>
