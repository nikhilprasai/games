<?php
include 'db.php'; // Include the database connection file

// Initialize totals
$totalDeposits = 0;
$totalWins = 0;
$totalFreeplay = 0;
$totalBonus = 0;

// Query to get total deposits
$stmt = $pdo->query("SELECT SUM(deposit_amount) AS total FROM deposits");
$totalDeposits = $stmt->fetchColumn();

// Query to get total wins (cashouts)
$stmt = $pdo->query("SELECT SUM(cashout_amount) AS total FROM cashouts");
$totalWins = $stmt->fetchColumn();

// Query to get total freeplay
$stmt = $pdo->query("SELECT SUM(freeplay_amount) AS total FROM freeplay_deposits");
$totalFreeplay = $stmt->fetchColumn();

// Query to get total bonus
$stmt = $pdo->query("SELECT SUM(bonus_amount) AS total FROM deposits");
$totalBonus = $stmt->fetchColumn();

// Return totals as JSON
echo json_encode([
    'total_deposits' => $totalDeposits,
    'total_wins' => $totalWins,
    'total_freeplay' => $totalFreeplay,
    'total_bonus' => $totalBonus,
]);
?>

