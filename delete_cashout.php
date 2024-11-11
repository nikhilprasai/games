<?php
include 'db.php'; // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the cashout ID from the POST request
    $id = $_POST['id'];

    // Prepare the SQL statement to delete the cashout
    $sql = "DELETE FROM cashouts WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    
    // Bind the ID parameter
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect back to the cashout history page with a success message
        header("Location: cashout_history.php?message=Cashout deleted successfully.");
        exit();
    } else {
        // Redirect back with an error message
        header("Location: cashout_history.php?error=Failed to delete cashout.");
        exit();
    }
} else {
    // If the request method is not POST, redirect to cashout history
    header("Location: cashout_history.php");
    exit();
}
?>