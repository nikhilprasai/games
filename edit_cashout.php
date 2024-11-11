<?php
include 'db.php'; // Include the database connection file

// Check if the ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the current cashout details
    $sql = "SELECT fb_name, cashout_amount FROM cashouts WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $cashout = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if cashout exists
    if (!$cashout) {
        die("Cashout not found.");
    }
} else {
    die("Invalid request.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fb_name = $_POST['fb-name'];
    $cashout_amount = $_POST['cashout-amount'];

    // Update the cashout details in the database
    $sql = "UPDATE cashouts SET fb_name = :fb_name, cashout_amount = :cashout_amount WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':fb_name', $fb_name);
    $stmt->bindParam(':cashout_amount', $cashout_amount);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Redirect back to cashout history with a success message
        header("Location: cashout_history.php?message=Cashout updated successfully.");
        exit();
    } else {
        // Redirect back with an error message
        header("Location: cashout_history.php?error=Failed to update cashout.");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Cashout</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="index.html" class="link">Home</a></li>
                <li><a href="dashboard.html" class="link">Dashboard</a></li>
                <li><a href="deposit.html" class="link">Deposit</a></li>
                <li><a href="recent_deposit.php" class="link">Recent Deposits</a></li>
                <li><a href="freeplay_deposit.php" class="link">FreePlay Deposits</a></li>
                <li><a href="freeplay_history.php" class="link">FreePlay History</a></li>
                <li><a href="cashout.php" class="link">Cashout</a></li>
                <li><a href="cashout_history.php" class="link">Cashout History</a></li>
            </ul>
        </nav>
        <div class="main-content">
            <h2>Edit Cashout</h2>
            <form action="edit_cashout.php?id=<?= htmlspecialchars($id); ?>" method="POST">
                <label for="fb-name">FB Name:</label>
                <input type="text" id="fb-name" name="fb-name" value="<?= htmlspecialchars($cashout['fb_name']); ?>" required>

                <label for="cashout-amount">Cashout Amount:</label>
                <input type="number" id="cashout-amount" name="cashout-amount" value="<?= htmlspecialchars($cashout['cashout_amount']); ?>" required>

                <button type="submit">Update Cashout</button>
            </form>
        </div>
    </div>
</body>
</html>