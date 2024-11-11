<?php
include 'db.php'; // Include the database connection file

// Check if the request is valid
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reportType = $_POST['report_type'];
    $downloadFormat = $_POST['download-format'];
    $fbName = $_POST['fb-name'];
    $startDate = $_POST['start-date'];
    $endDate = $_POST['end-date'];

    // Fetch the data based on the report type
    if ($reportType === 'deposits') {
        $sql = "SELECT d.fb_name, d.deposit_amount, d.bonus_amount, d.created_at AS deposit_date 
                FROM deposits d
                WHERE d.fb_name LIKE :fb_name AND d.created_at BETWEEN :start_date AND :end_date
                ORDER BY d.created_at";
    } elseif ($reportType === 'freeplay') {
        $sql = "SELECT fb_name, freeplay_amount, created_at AS freeplay_date
                FROM freeplay_deposits
                WHERE fb_name LIKE :fb_name AND created_at BETWEEN :start_date AND :end_date
                ORDER BY created_at";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':fb_name', '%' . $fbName . '%');
    $stmt->bindValue(':start_date', $startDate ?: '1970-01-01');
    $stmt->bindValue(':end_date', $endDate ?: date('Y-m-d'));
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate the report based on the selected format
    if ($downloadFormat === 'pdf') {
        // Include TCPDF library
        require 'vendor/autoload.php'; // Use Composer autoload

        // Create new PDF document
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);

        // Add content to PDF
        $html = '<h1>Report</h1>';
        foreach ($data as $row) {
            $html .= '<p>' . implode(', ', $row) . '</p>'; // Customize as needed
        }
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF
        $pdf->Output('report.pdf', 'D'); // Download the PDF
    } elseif ($downloadFormat === 'excel') {
        // Include PhpSpreadsheet library
        require 'vendor/autoload.php'; // Use Composer autoload

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'FB Name');
        $sheet->setCellValue('B1', 'Deposit Amount');
        $sheet->setCellValue('C1', 'Bonus Amount');
        $sheet->setCellValue('D1', 'Deposit Date');

        // Add data to the sheet
        $row = 2; // Start from the second row
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['fb_name']);
            $sheet->setCellValue('B' . $row, $item['deposit_amount']);
            $sheet->setCellValue('C' . $row, $item['bonus_amount']);
            $sheet->setCellValue('D' . $row, $item['deposit_date']);
            $row++;
        }

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="report.xlsx"');

        // Write the file to output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}
?>
