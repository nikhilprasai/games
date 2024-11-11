<?php
session_start();
require 'db.php'; // Database connection

// Fetch messages from the database
$stmt = $pdo->query("SELECT username, message, file FROM chat_messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($messages);
?>