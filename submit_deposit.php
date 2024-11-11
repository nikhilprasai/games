<?php
include 'db.php'; // Include the database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form values
    $fbName = $_POST['fb-name'];
    $depositAmount = $_POST['deposit-amount'];
    $bonusAmount = $_POST['bonus-amount'];
    $game = $_POST['game'];

    // Basic validation (ensure fields are not empty)
    if (empty($fbName) || empty($depositAmount) || empty($bonusAmount) || empty($game)) {
        echo "All fields are required.";
        exit;
    }

    // Insert into the database
    try {
        // Prepare SQL statement
        $sql = "INSERT INTO deposits (fb_name, deposit_amount, bonus_amount, game, created_at) VALUES (:fb_name, :deposit_amount, :bonus_amount, :game, NOW())";
        
        // Prepare the statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':fb_name', $fbName);
        $stmt->bindParam(':deposit_amount', $depositAmount);
        $stmt->bindParam(':bonus_amount', $bonusAmount);
        $stmt->bindParam(':game', $game);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to recent_deposit.php after successful insertion
            header("Location: recent_deposit.php");
            exit;
        } else {
            echo "Error: Could not record the deposit.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
