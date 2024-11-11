<?php
include 'db.php'; // Include the database connection file

// Query to fetch the recent cashouts
$sql = "SELECT id, fb_name, cashout_amount, created_at FROM cashouts ORDER BY created_at DESC"; // Added 'id' to the query
$stmt = $pdo->prepare($sql);
$stmt->execute();
$cashouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashout History</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Styles for the search bar */
        #search-bar {
            margin-bottom: 20px;
            padding: 10px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        /* Styles for the cashout history table */
        .cashout-history {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .cashout-history th, .cashout-history td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        .cashout-history th {
            background-color: #3498db; /* Header background color */
            color: white; /* Header text color */
        }
        .delete-button {
            background-color: #e74c3c; /* Red background for delete button */
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px; /* Rounded corners */
            cursor: pointer;
        }
        .delete-button:hover {
            background-color: #c0392b; /* Darker red on hover */
        }
        .edit-button {
            background-color: #3498db; /* Blue background for edit button */
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px; /* Rounded corners */
            cursor: pointer;
        }
        .edit-button:hover {
            background-color: #1abc9c; /* Darker green on hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="index.html" class="link">Home</a></li>
                <li><a href="dashboard.html" class="link">Dashboard</a></li>
                <li><a href="games.html" class="link active">Games Load</a></li> <!-- Active link for current page -->
                <li><a href="deposit.html" class="link">Deposit</a></li>
                <li><a href="recent_deposit.php" class="link">Recent Deposits</a></li>
                <li><a href="freeplay_deposit.php" class="link">FreePlay Deposits</a></li>
                <li><a href="freeplay_history.php" class="link">FreePlay History</a></li>
                <li><a href="cashout.php" class="link">Cashout</a></li>
                <li><a href="cashout_history.php" class="link active">Cashout History</a></li>
                <li><a href="reports.php" class="link">Reports</a></li>
                <li><a href="settings.html" class="link">Settings</a></li>


            </ul>
        </nav>
        <div class="main-content">
            <h2>Cashout History</h2>
            <input type="text" id="search-bar" onkeyup="filterCashouts()" placeholder="Search by FB Name...">
            <table id="cashout-history" class="cashout-history">
                <thead>
                    <tr>
                        <th>FB Name</th>
                        <th>Cashout Amount</th>
                        <th>Date</th>
                        <th>Action</th> <!-- Added Action column -->
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($cashouts)): ?>
                        <?php foreach ($cashouts as $cashout): ?>
                            <tr>
                                <td><?= htmlspecialchars($cashout['fb_name']); ?></td>
                                <td><?= htmlspecialchars($cashout['cashout_amount']); ?></td>
                                <td><?= htmlspecialchars($cashout['created_at']); ?></td>
                                <td>
                                    <form action="edit_cashout.php" method="GET" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($cashout['id']); ?>">
                                        <button type="submit" class="edit-button">Edit</button>
                                    </form>
                                    <form action="delete_cashout.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($cashout['id']); ?>">
                                        <button type="submit" class="delete-button" onclick="return confirm('Are you sure you want to delete this cashout?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No cashouts found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="script.js"></script>
    <script>
        // Function to filter cashouts based on search input
        function filterCashouts() {
            const searchInput = document.getElementById('search-bar').value.toLowerCase();
            const cashoutRows = document.querySelectorAll('#cashout-history tbody tr');

            cashoutRows.forEach(row => {
                const fbName = row.cells[0].textContent.toLowerCase();
                if (fbName.includes(searchInput)) {
                    row.style.display = ''; // Show entry
                } else {
                    row.style.display = 'none'; // Hide entry
                }
            });
        }
    </script>
      <script>
        // Check if the user is logged in
        if (!sessionStorage.getItem('loggedIn')) {
            alert('You are not logged in. Please log in.');
            window.location.href = 'login.html'; // Redirect to login page
        }

        // Function to check if the user's account is still valid
        async function checkUserStatus() {
            const response = await fetch('check_user_status.php'); // Endpoint to check user status
            const data = await response.json();

            if (!data.valid) {
                alert('Your account has been terminated. You will be logged out.');
                sessionStorage.removeItem('loggedIn'); // Clear session storage
                window.location.href = 'login.html'; // Redirect to login page
            }
        }

        // Check user status immediately
        checkUserStatus();
        // Optionally, start checking user status every 5 seconds
        setInterval(checkUserStatus, 5000); // Check every 5 seconds
    </script>
</body>
</html>
