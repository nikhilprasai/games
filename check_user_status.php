<?php
session_start();
$response = ['valid' => false];

if (isset($_SESSION['username'])) {
    // Assuming you have a database connection set up
    require 'db.php'; // Include your database connection here
    $username = $_SESSION['username'];
    
    // Fetch user status from the database
    $stmt = $pdo->prepare("SELECT is_deleted FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && !$user['is_deleted']) {
        $response['valid'] = true; // User account is valid
    }
}

echo json_encode($response);
?>
