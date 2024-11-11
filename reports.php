<?php
include 'db.php'; // Include the database connection file

// Initialize variables for filtering
$fbName = '';
$startDate = '';
$endDate = '';
$freeplayReports = []; // Initialize FreePlay reports

// Check if the form is submitted for deposits
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['report_type'])) {
    $fbName = $_POST['fb-name'];
    $startDate = $_POST['start-date'];
    $endDate = $_POST['end-date'];

    // Check which report type is selected
    if ($_POST['report_type'] === 'deposits') {
        // Query to fetch individual deposits and corresponding cashouts based on filters
        $sql = "SELECT d.fb_name, d.deposit_amount, d.bonus_amount, d.created_at AS deposit_date, 
                       COALESCE(c.cashout_amount, 0) AS cashout_amount, c.created_at AS cashout_date
                FROM deposits d
                LEFT JOIN cashouts c ON d.id = c.deposit_id
                WHERE d.fb_name LIKE :fb_name AND d.created_at BETWEEN :start_date AND :end_date
                ORDER BY d.created_at";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':fb_name', '%' . $fbName . '%');
        $stmt->bindValue(':start_date', $startDate ?: '1970-01-01'); // Default to a very early date if not set
        $stmt->bindValue(':end_date', $endDate ?: date('Y-m-d')); // Default to today if not set
        $stmt->execute();
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate totals
        $totalDeposit = 0;
        $totalBonus = 0;
        $totalCashout = 0;

        foreach ($reports as $report) {
            $totalDeposit += $report['deposit_amount'];
            $totalBonus += $report['bonus_amount'];
            $totalCashout += $report['cashout_amount'];
        }
    } elseif ($_POST['report_type'] === 'freeplay') {
        // Query to fetch FreePlay deposits based on filters
        $sql = "SELECT fb_name, freeplay_amount, created_at AS freeplay_date
                FROM freeplay_deposits
                WHERE fb_name LIKE :fb_name AND created_at BETWEEN :start_date AND :end_date
                ORDER BY created_at";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':fb_name', '%' . $fbName . '%');
        $stmt->bindValue(':start_date', $startDate ?: '1970-01-01'); // Default to a very early date if not set
        $stmt->bindValue(':end_date', $endDate ?: date('Y-m-d')); // Default to today if not set
        $stmt->execute();
        $freeplayReports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} else {
    $reports = []; // Initialize reports as an empty array if not submitted
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Reports</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
    <style>
        /* Additional styles for the report table */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .report-table th, .report-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        .report-table th {
            background-color: #3498db; /* Header background color */
            color: white; /* Header text color */
        }
        .report-table tr:nth-child(even) {
            background-color: #f2f2f2; /* Zebra striping for rows */
        }

        /* Styles for the form elements */
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        select, input[type="text"], input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .dropdown {
            position: relative;
            display: inline-block;
            width: 100%;
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
        .clear-button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        .clear-button:hover {
            background-color: #c0392b;
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
                <li><a href="cashout_history.php" class="link">Cashout History</a></li>
                <li><a href="reports.php" class="link active">Reports</a></li> <!-- New Reports link -->
                <li><a href="settings.html" class="link">Settings</a></li>

            </ul>
        </nav>
        <div class="main-content">
            <h2>Player Reports</h2>
            <form action="reports.php" method="POST" onsubmit="return toggleTables();">
                <div class="form-group">
                    <label for="report_type">Select Report Type:</label>
                    <select id="report_type" name="report_type" required onchange="toggleTables()">
                        <option value="deposits">Deposits and Cashouts</option>
                        <option value="freeplay">FreePlay Deposits</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fb-name">FB Name:</label>
                    <div class="dropdown">
                        <input type="text" id="fb-name" name="fb-name" value="<?= htmlspecialchars($fbName); ?>" required onkeyup="fetchFBNames()">
                        <div id="fb-name-dropdown" class="dropdown-content"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="start-date">Start Date:</label>
                    <input type="date" id="start-date" name="start-date" value="<?= htmlspecialchars($startDate); ?>">
                </div>
                <div class="form-group">
                    <label for="end-date">End Date:</label>
                    <input type="date" id="end-date" name="end-date" value="<?= htmlspecialchars($endDate); ?>">
                </div>
                <button type="submit">Generate Report</button>
            </form>

            <!-- Deposits and Cashouts Report Table -->
            <h3 id="deposits-title" style="<?= isset($_POST['report_type']) && $_POST['report_type'] === 'deposits' ? '' : 'display:none;' ?>">Deposits and Cashouts Report</h3>
            <table id="reports-table" class="report-table" style="<?= isset($_POST['report_type']) && $_POST['report_type'] === 'deposits' ? '' : 'display:none;' ?>">
                <thead>
                    <tr>
                        <th>FB Name</th>
                        <th>Deposit Amount</th>
                        <th>Bonus Amount</th>
                        <th>Cashout Amount</th>
                        <th>Deposit Date</th>
                        <th>Cashout Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reports)): ?>
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><?= htmlspecialchars($report['fb_name']); ?></td>
                                <td><?= htmlspecialchars($report['deposit_amount']); ?></td>
                                <td><?= htmlspecialchars($report['bonus_amount']); ?></td>
                                <td><?= htmlspecialchars($report['cashout_amount']); ?></td>
                                <td><?= htmlspecialchars($report['deposit_date']); ?></td>
                                <td><?= htmlspecialchars($report['cashout_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td><strong>Total</strong></td>
                            <td><strong><?= htmlspecialchars($totalDeposit); ?></strong></td>
                            <td><strong><?= htmlspecialchars($totalBonus); ?></strong></td>
                            <td><strong><?= htmlspecialchars($totalCashout); ?></strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No reports found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- FreePlay Report Table -->
            <h3 id="freeplay-title" style="<?= isset($_POST['report_type']) && $_POST['report_type'] === 'freeplay' ? '' : 'display:none;' ?>">FreePlay Deposits Report</h3>
            <table id="freeplay-reports-table" class="report-table" style="<?= isset($_POST['report_type']) && $_POST['report_type'] === 'freeplay' ? '' : 'display:none;' ?>">
                <thead>
                    <tr>
                        <th>FB Name</th>
                        <th>FreePlay Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($freeplayReports)): ?>
                        <?php foreach ($freeplayReports as $freeplay): ?>
                            <tr>
                                <td><?= htmlspecialchars($freeplay['fb_name']); ?></td>
                                <td><?= htmlspecialchars($freeplay['freeplay_amount']); ?></td>
                                <td><?= htmlspecialchars($freeplay['freeplay_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No FreePlay reports found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Add this section after the form -->
            <div class="form-group">
                <label for="download-format">Download Report:</label>
                <select id="download-format" name="download-format">
                    <option value="pdf">PDF</option>
                    <option value="excel">Excel</option>
                </select>
                <button type="button" onclick="downloadReport()">Download</button>
            </div>
        </div>
    </div>
    <script>
        // Function to fetch FB names dynamically
        function fetchFBNames() {
            const input = document.getElementById('fb-name').value;
            const dropdown = document.getElementById('fb-name-dropdown');

            if (input.length === 0) {
                dropdown.innerHTML = ''; // Clear dropdown if input is empty
                dropdown.style.display = 'none';
                return;
            }

            // Fetch matching FB names from the server
            fetch(`fetch_fb_names.php?term=${input}`)
                .then(response => response.json())
                .then(data => {
                    dropdown.innerHTML = ''; // Clear previous results
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.textContent = item.fb_name;
                        div.onclick = function() {
                            document.getElementById('fb-name').value = item.fb_name; // Set input value
                            dropdown.innerHTML = ''; // Clear dropdown
                            dropdown.style.display = 'none'; // Hide dropdown
                        };
                        dropdown.appendChild(div);
                    });
                    dropdown.style.display = data.length > 0 ? 'block' : 'none'; // Show dropdown if results exist
                });
        }

        // Function to toggle visibility of report tables based on selected report type
        function toggleTables() {
            const reportType = document.getElementById('report_type').value;
            const depositsTable = document.getElementById('reports-table');
            const freeplayTable = document.getElementById('freeplay-reports-table');
            const depositsTitle = document.getElementById('deposits-title');
            const freeplayTitle = document.getElementById('freeplay-title');

            if (reportType === 'deposits') {
                depositsTable.style.display = '';
                freeplayTable.style.display = 'none';
                depositsTitle.style.display = '';
                freeplayTitle.style.display = 'none';
            } else {
                depositsTable.style.display = 'none';
                freeplayTable.style.display = '';
                depositsTitle.style.display = 'none';
                freeplayTitle.style.display = '';
            }
        }

        function downloadReport() {
            const reportType = document.getElementById('report_type').value;
            const downloadFormat = document.getElementById('download-format').value;
            const fbName = document.getElementById('fb-name').value;
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;

            // Create a form to submit the download request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'download_report.php'; // The script to handle the download

            // Add hidden inputs to the form
            const reportTypeInput = document.createElement('input');
            reportTypeInput.type = 'hidden';
            reportTypeInput.name = 'report_type';
            reportTypeInput.value = reportType;
            form.appendChild(reportTypeInput);

            const downloadFormatInput = document.createElement('input');
            downloadFormatInput.type = 'hidden';
            downloadFormatInput.name = 'download-format';
            downloadFormatInput.value = downloadFormat;
            form.appendChild(downloadFormatInput);

            const fbNameInput = document.createElement('input');
            fbNameInput.type = 'hidden';
            fbNameInput.name = 'fb-name';
            fbNameInput.value = fbName;
            form.appendChild(fbNameInput);

            const startDateInput = document.createElement('input');
            startDateInput.type = 'hidden';
            startDateInput.name = 'start-date';
            startDateInput.value = startDate;
            form.appendChild(startDateInput);

            const endDateInput = document.createElement('input');
            endDateInput.type = 'hidden';
            endDateInput.name = 'end-date';
            endDateInput.value = endDate;
            form.appendChild(endDateInput);

            document.body.appendChild(form);
            form.submit(); // Submit the form
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
