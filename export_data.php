<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php'; // Include the database connection file

// Load PhpSpreadsheet and TCPDF for exports
require 'vendor/autoload.php'; // Make sure to include the PhpSpreadsheet and TCPDF libraries
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Get parameters
$month = $_GET['month'];
$year = $_GET['year'];
$day = $_GET['day']; // Get day parameter
$format = $_GET['format'];

// Prepare SQL query based on parameters
$sql = "SELECT d.fb_name, d.created_at, d.deposit_amount, d.bonus_amount, 
               COALESCE(c.cashout_amount, 0) AS cashout_amount, 
               COALESCE(f.freeplay_amount, 0) AS freeplay_amount 
        FROM deposits d
        LEFT JOIN cashouts c ON d.id = c.deposit_id
        LEFT JOIN freeplay_deposits f ON d.fb_name = f.fb_name
        WHERE MONTH(d.created_at) = :month AND YEAR(d.created_at) = :year";
if (!empty($day)) {
    $sql .= " AND DAY(d.created_at) = :day"; // Filter by day if provided
}

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':month', $month);
$stmt->bindValue(':year', $year);
if (!empty($day)) {
    $stmt->bindValue(':day', $day);
}
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if results are empty
if (empty($results)) {
    echo "No data found for the selected criteria.";
    exit;
}

// Create a new spreadsheet for Excel export
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header row
$sheet->setCellValue('A1', 'FB Name');
$sheet->setCellValue('B1', 'Date');
$sheet->setCellValue('C1', 'Total Deposits');
$sheet->setCellValue('D1', 'Total Cashout');
$sheet->setCellValue('E1', 'Total Bonus');
$sheet->setCellValue('F1', 'Total Freeplay');

// Fill data
$row = 2; // Start from the second row
$totalDeposits = 0;
$totalCashout = 0;
$totalBonus = 0;
$totalFreeplay = 0;

foreach ($results as $result) {
    $sheet->setCellValue('A' . $row, $result['fb_name']);
    $sheet->setCellValue('B' . $row, $result['created_at']);
    $sheet->setCellValue('C' . $row, $result['deposit_amount']);
    $sheet->setCellValue('D' . $row, $result['cashout_amount']);
    $sheet->setCellValue('E' . $row, $result['bonus_amount']);
    $sheet->setCellValue('F' . $row, $result['freeplay_amount']);
    
    // Accumulate totals
    $totalDeposits += $result['deposit_amount'];
    $totalCashout += $result['cashout_amount'];
    $totalBonus += $result['bonus_amount'];
    $totalFreeplay += $result['freeplay_amount'];
    
    $row++;
}

// Set totals in the last row
$sheet->setCellValue('A' . $row, 'Totals');
$sheet->setCellValue('C' . $row, $totalDeposits);
$sheet->setCellValue('D' . $row, $totalCashout);
$sheet->setCellValue('E' . $row, $totalBonus);
$sheet->setCellValue('F' . $row, $totalFreeplay);

// Set the filename for Excel
$filename = "data_{$year}_{$month}" . (!empty($day) ? "_{$day}" : "") . ".xlsx";

// Create the file based on the selected format
if ($format === 'excel') {
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // Write the file to the output
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit; // Ensure no further output is sent
}

// PDF export logic
if ($format === 'pdf') {
    // Create new PDF document
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    // Add content to PDF
    $html = '<h1>Data Report</h1>';
    $html .= '<table border="1" cellpadding="4"><tr><th>FB Name</th><th>Date</th><th>Total Deposits</th><th>Total Cashout</th><th>Total Bonus</th><th>Total Freeplay</th></tr>';
    foreach ($results as $result) {
        $html .= '<tr>';
        $html .= '<td>' . $result['fb_name'] . '</td>';
        $html .= '<td>' . $result['created_at'] . '</td>';
        $html .= '<td>' . $result['deposit_amount'] . '</td>';
        $html .= '<td>' . $result['cashout_amount'] . '</td>';
        $html .= '<td>' . $result['bonus_amount'] . '</td>';
        $html .= '<td>' . $result['freeplay_amount'] . '</td>';
        $html .= '</tr>';
    }
    // Add totals row
    $html .= '<tr><td>Totals</td><td></td><td>' . $totalDeposits . '</td><td>' . $totalCashout . '</td><td>' . $totalBonus . '</td><td>' . $totalFreeplay . '</td></tr>';
    $html .= '</table>';

    $pdf->writeHTML($html, true, false, true, false, '');

    // Output PDF
    $pdf->Output('data_report.pdf', 'D'); // Download the PDF
    exit; // Ensure no further output is sent
}
?>
