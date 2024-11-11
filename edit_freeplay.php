<?php
include 'db.php'; // Include the database connection file

// Check if the ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the current details of the FreePlay deposit
    $sql = "SELECT fb_name, freeplay_amount FROM freeplay_deposits WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $deposit = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the deposit exists
    if (!$deposit) {
        echo "Deposit not found.";
        exit;
    }
}

// Update the deposit if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fbName = $_POST['fb-name'];
    $freeplayAmount = $_POST['freeplay-amount'];
    $id = $_POST['id']; // Get the ID from the form

    // Debugging: Check if values are received correctly
    error_log("Updating deposit: ID = $id, FB Name = $fbName, FreePlay Amount = $freeplayAmount");

    // Prepare the SQL statement to update the entry
    $updateSql = "UPDATE freeplay_deposits SET fb_name = :fb_name, freeplay_amount = :freeplay_amount WHERE id = :id";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->bindParam(':fb_name', $fbName);
    $updateStmt->bindParam(':freeplay_amount', $freeplayAmount);
    $updateStmt->bindParam(':id', $id);

    if ($updateStmt->execute()) {
        // Redirect back to the FreePlay history page after updating
        header("Location: freeplay_history.php");
        exit;
    } else {
        echo "Error: Could not update the entry.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit FreePlay Deposit</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Edit FreePlay Deposit</h2>
        <form action="edit_freeplay.php?id=<?= htmlspecialchars($id); ?>" method="POST">
            <label for="fb-name">FB Name:</label>
            <input type="text" id="fb-name" name="fb-name" value="<?= htmlspecialchars($deposit['fb_name']); ?>" required>

            <label for="freeplay-amount">FreePlay Amount:</label>
            <input type="number" id="freeplay-amount" name="freeplay-amount" value="<?= htmlspecialchars($deposit['freeplay_amount']); ?>" required>

            <button type="submit">Update Deposit</button>
        </form>
    </div>
</body>
</html>
