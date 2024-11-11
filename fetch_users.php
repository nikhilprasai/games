<?php
session_start();
require 'db.php'; // Database connection

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

// Fetch only users that are not marked as deleted
$stmt = $pdo->query("SELECT username FROM users WHERE is_deleted = 0");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($users);
?>
