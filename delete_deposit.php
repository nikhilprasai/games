<?php
include 'db.php'; // Include the database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the ID of the deposit to delete
    $id = $_POST['id'];

    // Prepare the SQL statement to delete the entry
    $sql = "DELETE FROM deposits WHERE id = :id"; // Assuming 'id' is the primary key
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        // Redirect back to the recent deposits page after deletion
        header("Location: recent_deposit.php");
        exit;
    } else {
        echo "Error: Could not delete the entry.";
    }
}
?>
