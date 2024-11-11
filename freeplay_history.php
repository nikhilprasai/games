<?php
include 'db.php'; // Include the database connection file

// Query to fetch the FreePlay deposit history
$sql = "SELECT id, fb_name, freeplay_amount, created_at FROM freeplay_deposits ORDER BY created_at DESC"; // Added 'id' to the query
$stmt = $pdo->prepare($sql);
$stmt->execute();
$freeplayDeposits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreePlay History</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
    <style>
        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
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
                <li><a href="freeplay_deposit.php" class="link">FreePlay Deposit</a></li>
                <li><a href="freeplay_history.php" class="link active">FreePlay History</a></li>
                <li><a href="cashout.php" class="link active">Cashout</a></li>
                <li><a href="cashout_history.php" class="link">Cashout History</a></li>
                <li><a href="reports.php" class="link">Reports</a></li>
                <li><a href="settings.html" class="link">Settings</a></li>


            </ul>
        </nav>

        <div class="main-content">
            <h2>FreePlay History</h2>

            <!-- Search bar for filtering history -->
            <input type="text" id="search-bar" placeholder="Search by FB Name..." onkeyup="filterDeposits()">

            <table id="freeplay-history" class="recent-deposits">
                <thead>
                    <tr>
                        <th>FB Name</th>
                        <th>FreePlay Amount</th>
                        <th>Date</th>
                        <th>Action</th> <!-- Added Action column -->
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($freeplayDeposits)): ?>
                        <?php foreach ($freeplayDeposits as $deposit): ?>
                            <tr>
                                <td><?= htmlspecialchars($deposit['fb_name']); ?></td>
                                <td><?= htmlspecialchars($deposit['freeplay_amount']); ?></td>
                                <td><?= htmlspecialchars($deposit['created_at']); ?></td>
                                <td>
                                    <button onclick="openModal(<?= htmlspecialchars($deposit['id']); ?>, '<?= htmlspecialchars($deposit['fb_name']); ?>', <?= htmlspecialchars($deposit['freeplay_amount']); ?>)">Edit</button>
                                    <form action="delete_freeplay.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($deposit['id']); ?>">
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this entry?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No FreePlay deposits found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for editing FreePlay deposit -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Edit FreePlay Deposit</h2>
            <form id="editForm" action="edit_freeplay.php" method="POST">
                <input type="hidden" id="edit-id" name="id">
                <label for="edit-fb-name">FB Name:</label>
                <input type="text" id="edit-fb-name" name="fb-name" required>

                <label for="edit-freeplay-amount">FreePlay Amount:</label>
                <input type="number" id="edit-freeplay-amount" name="freeplay-amount" required>

                <button type="submit">Update Deposit</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(id, fbName, freeplayAmount) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-fb-name').value = fbName;
            document.getElementById('edit-freeplay-amount').value = freeplayAmount;
            document.getElementById('editModal').style.display = "block";
        }

        function closeModal() {
            document.getElementById('editModal').style.display = "none";
        }

        // Close the modal if the user clicks outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // Function to filter FreePlay deposits based on search input
        function filterDeposits() {
            const searchInput = document.getElementById('search-bar').value.toLowerCase();
            const depositRows = document.querySelectorAll('#freeplay-history tbody tr');

            depositRows.forEach(row => {
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
