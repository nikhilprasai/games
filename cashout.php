<?php
include 'db.php'; // Include the database connection file

// Fetch all recent deposits for the dropdown selection
$sql = "SELECT id, fb_name, deposit_amount, bonus_amount, game, created_at FROM deposits ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$deposits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashout</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Styles for the suggestion list */
        .suggestions {
            border: 1px solid #ccc;
            max-height: 150px;
            overflow-y: auto;
            position: absolute;
            background-color: white;
            z-index: 1000;
            width: calc(100% - 20px); /* Adjust width to match input */
        }
        .suggestion-item {
            padding: 10px;
            cursor: pointer;
        }
        .suggestion-item:hover {
            background-color: #f0f0f0; /* Highlight on hover */
        }
        /* Styles for the results table */
        #results-table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }
        #results-table th, #results-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        #results-table th {
            background-color: #3498db; /* Header background color */
            color: white; /* Header text color */
        }
        .select-button {
            background-color: #3498db; /* Button color */
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px; /* Rounded corners */
            cursor: pointer;
        }
        .select-button:hover {
            background-color: #1abc9c; /* Change color on hover */
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
                <li><a href="cashout.php" class="link active">Cashout</a></li>
                <li><a href="cashout_history.php" class="link">Cashout History</a></li>
                <li><a href="reports.php" class="link">Reports</a></li>
                <li><a href="settings.html" class="link">Settings</a></li>


            </ul>
        </nav>
        <div class="main-content">
            <h2>Submit Cashout</h2>
            <form action="submit_cashout.php" method="POST">
                <label for="fb-name">FB Name:</label>
                <input type="text" id="fb-name" name="fb-name" onkeyup="filterNames()" required autocomplete="off">
                <div id="suggestions" class="suggestions" style="display: none;"></div>

                <label for="cashout-amount">Cashout Amount:</label>
                <input type="number" id="cashout-amount" name="cashout-amount" required>

                <label for="selected-deposit">Selected Deposit:</label>
                <input type="text" id="selected-deposit" name="selected-deposit" readonly>
                <input type="hidden" id="selected-deposit-id" name="selected-deposit-id"> <!-- Hidden field for deposit ID -->

                <button type="submit">Submit Cashout</button>
            </form>

            <!-- Table to display filtered results -->
            <table id="results-table" style="display: none;">
                <thead>
                    <tr>
                        <th>FB Name</th>
                        <th>Deposit Amount</th>
                        <th>Bonus Amount</th>
                        <th>Game</th>
                        <th>Date</th>
                        <th>Select</th>
                    </tr>
                </thead>
                <tbody id="results-body">
                    <!-- Filtered results will be inserted here -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const deposits = <?= json_encode($deposits); ?>; // Convert PHP array to JavaScript array

        function filterNames() {
            const input = document.getElementById('fb-name');
            const filter = input.value.toLowerCase();
            const suggestions = document.getElementById('suggestions');
            const resultsTable = document.getElementById('results-table');
            const resultsBody = document.getElementById('results-body');

            suggestions.innerHTML = ''; // Clear previous suggestions
            resultsBody.innerHTML = ''; // Clear previous results

            if (filter) {
                const filteredDeposits = deposits.filter(deposit => deposit.fb_name.toLowerCase().includes(filter));
                
                // Show suggestions
                filteredDeposits.forEach(deposit => {
                    const div = document.createElement('div');
                    div.classList.add('suggestion-item');
                    div.textContent = deposit.fb_name;
                    div.onclick = function() {
                        input.value = deposit.fb_name; // Set input value to the selected name
                        suggestions.style.display = 'none'; // Hide suggestions
                        displayResults(filteredDeposits); // Display results in the table
                    };
                    suggestions.appendChild(div);
                });
                suggestions.style.display = filteredDeposits.length ? 'block' : 'none'; // Show suggestions if there are any

                // Display results in the table in real-time
                displayResults(filteredDeposits);
            } else {
                suggestions.style.display = 'none'; // Hide suggestions if input is empty
                resultsTable.style.display = 'none'; // Hide results table
            }
        }

        function displayResults(filteredDeposits) {
            const resultsTable = document.getElementById('results-table');
            const resultsBody = document.getElementById('results-body');
            resultsBody.innerHTML = ''; // Clear previous results

            filteredDeposits.forEach(deposit => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${deposit.fb_name}</td>
                    <td>${deposit.deposit_amount}</td>
                    <td>${deposit.bonus_amount}</td>
                    <td>${deposit.game}</td>
                    <td>${deposit.created_at}</td>
                    <td><button class="select-button" onclick="selectDeposit(${deposit.id}, '${deposit.fb_name}', ${deposit.deposit_amount}, ${deposit.bonus_amount}, '${deposit.game}')">Select</button></td>
                `;
                resultsBody.appendChild(row);
            });

            resultsTable.style.display = filteredDeposits.length ? 'table' : 'none'; // Show table if there are results
        }

        function selectDeposit(id, fbName, depositAmount, bonusAmount, game) {
            // Set the selected deposit details in the form
            document.getElementById('fb-name').value = fbName; // Set FB Name
            document.getElementById('cashout-amount').value = ''; // Clear Cashout Amount for user input
            document.getElementById('selected-deposit').value = `FB Name: ${fbName}, Deposit: ${depositAmount}, Bonus: ${bonusAmount}, Game: ${game}`; // Show selected deposit details
            document.getElementById('selected-deposit-id').value = id; // Set hidden field for deposit ID
            console.log(`Selected Deposit ID: ${id}`); // For debugging
        }

        // Close suggestions when clicking outside
        window.onclick = function(event) {
            const suggestions = document.getElementById('suggestions');
            if (!event.target.matches('#fb-name')) {
                suggestions.style.display = 'none';
            }
        };
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
