<?php
include 'db.php'; // Include the database connection file

// Query to fetch the recently deposited FB Names
$sql = "SELECT fb_name FROM deposits ORDER BY created_at DESC"; // Updated to fetch from deposits
$stmt = $pdo->prepare($sql);
$stmt->execute();
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle new FreePlay deposit submission
    $fbName = $_POST['fb-name'];
    $freeplayAmount = $_POST['freeplay-amount'];

    // Insert the new deposit into the database
    $insertSql = "INSERT INTO freeplay_deposits (fb_name, freeplay_amount, created_at) VALUES (?, ?, NOW())";
    $insertStmt = $pdo->prepare($insertSql);
    $insertStmt->execute([$fbName, $freeplayAmount]);

    // Redirect to the same page to avoid form resubmission
    header("Location: freeplay_deposit.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreePlay Deposit</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
    <style>
        /* Custom styles for the search dropdown */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ccc;
            z-index: 1;
            max-height: 150px;
            overflow-y: auto;
            width: 100%;
        }
        .dropdown-content div {
            padding: 8px;
            cursor: pointer;
        }
        .dropdown-content div:hover {
            background-color: #f1f1f1;
        }
        .dropdown-content.show {
            display: block;
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
                <li><a href="freeplay_deposit.php" class="link active">FreePlay Deposit</a></li>
                <li><a href="freeplay_history.php" class="link">FreePlay History</a></li>
                <li><a href="cashout.php" class="link active">Cashout</a></li>
                <li><a href="cashout_history.php" class="link">Cashout History</a></li>
                <li><a href="reports.php" class="link">Reports</a></li>
                <li><a href="settings.html" class="link">Settings</a></li>


            </ul>
        </nav>

        <div class="main-content">
            <h2>FreePlay Deposit</h2>

            <!-- FreePlay Deposit Form -->
            <form action="freeplay_deposit.php" method="POST">
                <label for="fb-name">FB Name:</label>
                <div class="dropdown">
                    <input type="text" id="fb-name" name="fb-name" placeholder="Search FB Name..." onkeyup="filterDropdown()" required>
                    <div id="dropdown-list" class="dropdown-content">
                        <?php if (!empty($players)): ?>
                            <?php foreach ($players as $player): ?>
                                <div class="dropdown-item" onclick="selectPlayer('<?= htmlspecialchars($player['fb_name']); ?>')"><?= htmlspecialchars($player['fb_name']); ?></div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div>No recent players found.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <label for="freeplay-amount">FreePlay Amount:</label>
                <input type="number" id="freeplay-amount" name="freeplay-amount" required>

                <button type="submit">Submit FreePlay Deposit</button>
            </form>
        </div>
    </div>
    <script>
        function filterDropdown() {
            const input = document.getElementById('fb-name').value.toLowerCase();
            const dropdown = document.getElementById('dropdown-list');
            const items = dropdown.getElementsByClassName('dropdown-item');

            let hasMatches = false;
            for (let i = 0; i < items.length; i++) {
                const itemText = items[i].textContent.toLowerCase();
                if (itemText.includes(input)) {
                    items[i].style.display = 'block';
                    hasMatches = true;
                } else {
                    items[i].style.display = 'none';
                }
            }
            dropdown.classList.toggle('show', hasMatches);
        }

        function selectPlayer(name) {
            document.getElementById('fb-name').value = name; // Set the input value to the selected name
            document.getElementById('dropdown-list').classList.remove('show'); // Hide the dropdown
        }

        // Close the dropdown if the user clicks outside of it
        window.onclick = function(event) {
            if (!event.target.matches('#fb-name')) {
                const dropdown = document.getElementById('dropdown-list');
                dropdown.classList.remove('show');
            }
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
