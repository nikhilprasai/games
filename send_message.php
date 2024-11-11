<?php
session_start();
require 'db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username'];
    $message = $_POST['message'];
    $filePath = null;

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES['file']['name']);
        $targetFilePath = $targetDir . $fileName;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath)) {
            $filePath = $targetFilePath;
        }
    }

    // Insert message into the database
    $stmt = $pdo->prepare("INSERT INTO chat_messages (username, message, file) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $message, $filePath])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error sending message.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>