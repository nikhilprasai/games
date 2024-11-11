<?php
session_start();
require 'db.php';

// Get the posted data
$data = json_decode(file_get_contents('php://input'), true);
$oldUsername = $data['oldUsername'];
$newUsername = $data['newUsername'];
$newPassword = password_hash($data['newPassword'], PASSWORD_DEFAULT);

// Update user in the database
$sql = "UPDATE users SET username = ?, password = ? WHERE username = ?";
$stmt = $pdo->prepare($sql);
$result = $stmt->execute([$newUsername, $newPassword, $oldUsername]);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'User updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating user.']);
}
?>
