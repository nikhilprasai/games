<?php
session_start();
require 'db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $username = $data->username;

    // Ensure the admin is logged in
    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['message' => 'Unauthorized']);
        exit;
    }

    // Prevent the admin user from deleting themselves
    if ($username === $_SESSION['username']) {
        echo json_encode(['success' => false, 'message' => 'Admin user cannot be deleted.', 'logout' => false]);
        exit;
    }

    // Log the username being deleted for debugging
    error_log("Attempting to delete user: " . $username);

    // Update the user to mark them as deleted instead of removing from the database
    $stmt = $pdo->prepare("UPDATE users SET is_deleted = 1 WHERE username = ?");

    // Execute the statement and check for errors
    if ($stmt->execute([$username])) {
        // Log successful deletion
        error_log("User deleted successfully: " . $username);

        // Check if the deleted user is the logged-in user
        $logout = ($username === $_SESSION['username']);
        if ($logout) {
            // Destroy the session and log out the user
            session_destroy();
            error_log("Logged out user: " . $username);
        }

        echo json_encode(['success' => true, 'message' => 'User deleted successfully.', 'logout' => $logout]);
    } else {
        // Log the error for debugging
        error_log("Error deleting user: " . implode(", ", $stmt->errorInfo())); // Log the error message
        echo json_encode(['success' => false, 'message' => 'Error deleting user.', 'logout' => false]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
