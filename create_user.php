<?php
session_start();
require 'db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $username = $data->username;
    $password = password_hash($data->password, PASSWORD_DEFAULT); // Hash the password

    // Check if the username already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Username already exists.']);
        exit;
    }

    // Insert the new user into the database
    $stmt = $pdo->prepare("INSERT INTO users (username, password, is_deleted) VALUES (?, ?, 0)");
    if ($stmt->execute([$username, $password])) {
        echo json_encode(['success' => true, 'message' => 'User created successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating user.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
