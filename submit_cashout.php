<?php
include 'db.php'; // Include the database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form values
    $fbName = $_POST['fb-name'];
    $cashoutAmount = $_POST['cashout-amount'];
    $selectedDepositId = $_POST['selected-deposit-id']; // Get the selected deposit ID

    // Insert cashout into the database
    $sql = "INSERT INTO cashouts (fb_name, cashout_amount, created_at, deposit_id) VALUES (?, ?, NOW(), ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$fbName, $cashoutAmount, $selectedDepositId])) {
        // Redirect to cashout history page upon success
        header("Location: cashout_history.php");
        exit;
    } else {
        echo "Error processing cashout.";
    }
}
?>
