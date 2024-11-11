<?php
include 'db.php'; // Include the database connection file

// Check if the ID is provided
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $fbName = $_POST['fb-name'];
    $depositAmount = $_POST['deposit-amount'];
    $bonusAmount = $_POST['bonus-amount'];
    $game = $_POST['game'];

    // Prepare the SQL statement to update the entry
    $updateSql = "UPDATE deposits SET fb_name = :fb_name, deposit_amount = :deposit_amount, bonus_amount = :bonus_amount, game = :game WHERE id = :id";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->bindParam(':fb_name', $fbName);
    $updateStmt->bindParam(':deposit_amount', $depositAmount);
    $updateStmt->bindParam(':bonus_amount', $bonusAmount);
    $updateStmt->bindParam(':game', $game);
    $updateStmt->bindParam(':id', $id);

    if ($updateStmt->execute()) {
        // Redirect back to the recent deposits page after updating
        header("Location: recent_deposit.php");
        exit;
    } else {
        echo "Error: Could not update the entry.";
    }
} else {
    echo "No ID provided.";
}
?>