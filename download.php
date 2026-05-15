<?php
require 'vendor/autoload.php'; // Ensure you have PHPSpreadsheet installed via Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Function to apply professional styling
function applyProfessionalStyling($sheet) {
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

    // Apply header styling
    $headerRange = 'A1:' . $highestColumn . '1';
    $sheet->getStyle($headerRange)->applyFromArray([
        'font' => [
            'bold' => true,
            'color' => ['argb' => Color::COLOR_WHITE],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FF007BFF'],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_LEFT,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ]);

    // Apply body styling
    for ($row = 2; $row <= $highestRow; $row++) {
        $color = ($row % 2 == 0) ? 'FFF2F2F2' : 'FFFFFFFF'; // Alternating row colors
        $sheet->getStyle("A$row:$highestColumn$row")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => $color],
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFDDDDDD'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
    }

    // Manually set column widths
    $maxWidth = 30; // Set your desired maximum width here
    foreach (range('A', $highestColumn) as $columnID) {
        $sheet->getColumnDimension($columnID)->setWidth($maxWidth);
    }

    // Increase row height
    for ($row = 1; $row <= $highestRow; $row++) {
        $sheet->getRowDimension($row)->setRowHeight(20); // Set your desired row height here
    }
}

header('Content-Type: text/plain');
// Get raw POST data
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Check if the detailHtml, userTotalsHtml, projectTotalsHtml and projectGroupedHtml are set
if (isset($data['detailHtml']) && isset($data['userTotalsHtml']) && isset($data['projectTotalsHtml']) && isset($data['projectGroupedHtml'])) {
    // Get the HTML inputs
    $detailHtml = $data['detailHtml'];
    $userTotalsHtml = $data['userTotalsHtml'];
    $projectTotalsHtml = $data['projectTotalsHtml'];
    $projectGroupedHtml = $data['projectGroupedHtml'];

    // Create a new Spreadsheet
    $spreadsheet = new Spreadsheet();

    // Load HTML reader
    $htmlReader = new Html();
    $spreadsheet->removeSheetByIndex(0); // Remove the default sheet
    $sheetIndex = 0;

    // Load detailHtml if provided
    if (!empty($detailHtml)) {
        $detailSheet = $htmlReader->loadFromString($detailHtml);
        $spreadsheet->addExternalSheet($detailSheet->getSheet(0), $sheetIndex);
        $spreadsheet->getSheet($sheetIndex)->setTitle('Detail');
        applyProfessionalStyling($spreadsheet->getSheet($sheetIndex));
        $sheetIndex++;
    }

    // Load projectGroupedHtml if provided
    if (!empty($projectGroupedHtml)) {
        $projectGroupedSheet = $htmlReader->loadFromString($projectGroupedHtml);
        $spreadsheet->addExternalSheet($projectGroupedSheet->getSheet(0), $sheetIndex);
        $spreadsheet->getSheet($sheetIndex)->setTitle('Project Grouped');
        applyProfessionalStyling($spreadsheet->getSheet($sheetIndex));
        $sheetIndex++;
    }

    // Load userTotalsHtml if provided
    if (!empty($userTotalsHtml)) {
        $userTotalsSheet = $htmlReader->loadFromString($userTotalsHtml);
        $spreadsheet->addExternalSheet($userTotalsSheet->getSheet(0), $sheetIndex);
        $spreadsheet->getSheet($sheetIndex)->setTitle('User Totals');
        applyProfessionalStyling($spreadsheet->getSheet($sheetIndex));
        $sheetIndex++;
    }

    // Load projectTotalsHtml if provided
    if (!empty($projectTotalsHtml)) {
        $projectTotalsSheet = $htmlReader->loadFromString($projectTotalsHtml);
        $spreadsheet->addExternalSheet($projectTotalsSheet->getSheet(0), $sheetIndex);
        $spreadsheet->getSheet($sheetIndex)->setTitle('Project Totals');
        applyProfessionalStyling($spreadsheet->getSheet($sheetIndex));
        $sheetIndex++;
    }

    // Set headers to force download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="report.xlsx"');
    header('Cache-Control: max-age=0');

    // Write the file to the output
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit; // Ensure no additional output is sent
} elseif (isset($_POST['masterHtml'])) {
    // Get the HTML inputs
    $masterHtml = $_POST['masterHtml'];

    // Create a new Spreadsheet
    $spreadsheet = new Spreadsheet();

    // Load detailHtml to the first sheet
    $htmlReader = new Html();
    $spreadsheet->removeSheetByIndex(0); // Remove the default sheet
    $masterSheet = $htmlReader->loadFromString($masterHtml);
    $spreadsheet->addExternalSheet($masterSheet->getSheet(0), 0);
    $spreadsheet->getSheet(0)->setTitle('Master Sheet');
    applyProfessionalStyling($spreadsheet->getSheet(0));

    // Set headers to force download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="report.xlsx"');
    header('Cache-Control: max-age=0');
    
    // Write the file to the output
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit; // Ensure no additional output is sent
} else {
    echo "No report data received.";
}
?>
