<?php
require 'vendor/autoload.php'; // Ensure you have PHPSpreadsheet installed via Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Helper functions for formatting reports on server-side
function trimDatePHP($date) {
    return $date ? substr($date, 0, 10) : '';
}

function calculateTotalDurationPHP($tasks) {
    $total = 0;
    if (is_array($tasks)) {
        foreach ($tasks as $task) {
            $total += intval($task['duration'] ?? 0);
        }
    }
    return $total;
}

function displayTagsPHP($tags) {
    if (!$tags || (is_array($tags) && count($tags) === 0)) {
        return "No Tags";
    }
    
    $titles = [];
    foreach ($tags as $tag) {
        if (is_array($tag)) {
            $titles[] = $tag['title'] ?? '';
        } else {
            $titles[] = $tag;
        }
    }
    return implode(", ", array_filter($titles));
}

function convertSecondsToTimePHP($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
}

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

// Set header for fallback / response
header('Content-Type: text/plain');

// Get raw POST data
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Global status codes to labels mapping
$statusLabels = [
    1 => "New",
    2 => "Pending",
    3 => "In Progress",
    4 => "Supposedly Completed",
    5 => "Completed",
    6 => "Deferred",
    7 => "Declined",
];

// Reconstruct HTML if raw JSON data is sent to keep payload size microscopic
if (isset($data['type']) && isset($data['reportData'])) {
    $type = $data['type']; // 'detailed', 'grouped', or 'master'
    $report = $data['reportData'];
    $dateStart = $data['dateStart'] ?? '';
    $dateFinish = $data['dateFinish'] ?? '';

    $detailHtml = '';
    $userTotalsHtml = '';
    $projectTotalsHtml = '';
    $projectGroupedHtml = '';
    $masterHtml = '';

    // Reconstruct common reports: User Totals & Project Totals (needed for detailed and grouped)
    if ($type === 'detailed' || $type === 'grouped') {
        // 1. Reconstruct User Totals (Total Time Taken By Employees)
        $usernameTotals = [];
        foreach ($report as $user) {
            $totalDuration = calculateTotalDurationPHP($user['tasks'] ?? []);
            $usernameTotals[$user['name']] = ($usernameTotals[$user['name']] ?? 0) + intval($totalDuration);
        }

        $userTotalsHtml = "<h2 style='font-weight: bold; font-size: 20px; margin-top: 32px;'>Total Time Taken By Employees</h2>
        <table style=\"width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 18px; text-align: left; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); border-radius: 8px; overflow: hidden;\">
        <thead>
            <tr style=\"background-color: #007BFF; color: white;\">
                <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Username</th>
                <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Total Time</th>
            </tr>
        </thead>
        <tbody>";

        $grandTotalUserTime = 0;
        foreach ($usernameTotals as $username => $totalTime) {
            $userTotalsHtml .= "<tr style=\"background-color: #f2f2f2;\">
            <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>" . htmlspecialchars($username) . "</td>
            <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>" . htmlspecialchars(convertSecondsToTimePHP($totalTime)) . "</td>
            </tr>";
            $grandTotalUserTime += $totalTime;
        }

        $userTotalsHtml .= "<tr>
        <td style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>Grand Total Time</td>
        <td style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>" . htmlspecialchars(convertSecondsToTimePHP($grandTotalUserTime)) . "</td>
        </tr>
        </tbody>
        </table>";

        // 2. Reconstruct Project Totals (Total Time Used In Projects)
        $projectTotals = [];
        foreach ($report as $user) {
            if (isset($user['tasks']) && is_array($user['tasks'])) {
                foreach ($user['tasks'] as $task) {
                    $projectName = isset($task['group']['name']) && !empty($task['group']['name']) ? $task['group']['name'] : "Unknown Project";
                    $duration = isset($task['duration']) ? intval($task['duration']) : 0;
                    $projectTotals[$projectName] = ($projectTotals[$projectName] ?? 0) + $duration;
                }
            }
        }
        ksort($projectTotals); // Alphabetical sort

        $projectTotalsHtml = "<h2 style='font-weight: bold; font-size: 20px; margin-top: 32px;'>Total Time Used In Projects</h2>
        <table style=\"width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 18px; text-align: left; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); border-radius: 8px; overflow: hidden;\">
        <thead>
            <tr style=\"background-color: #007BFF; color: white;\">
                <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Project</th>
                <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Total Time</th>
            </tr>
        </thead>
        <tbody>";

        $grandTotalProjectTime = 0;
        foreach ($projectTotals as $projectName => $totalTime) {
            $projectTotalsHtml .= "<tr style=\"background-color: #f2f2f2;\">
            <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>" . htmlspecialchars($projectName) . "</td>
            <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>" . htmlspecialchars(convertSecondsToTimePHP($totalTime)) . "</td>
            </tr>";
            $grandTotalProjectTime += $totalTime;
        }

        $projectTotalsHtml .= "<tr>
        <td style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>Grand Total Time</td>
        <td style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>" . htmlspecialchars(convertSecondsToTimePHP($grandTotalProjectTime)) . "</td>
        </tr>
        </tbody>
        </table>";
    }

    if ($type === 'detailed') {
        // 3. Reconstruct Detailed Daily Split HTML
        $titleText = "Work Report from " . htmlspecialchars($dateStart) . " to " . htmlspecialchars($dateFinish);
        $exportTitle = "<h1 style=\"display: none;\">{$titleText}</h1>";
        $detailHtml = $exportTitle;

        foreach ($report as $user) {
            $detailHtml .= "<div>
                <h2 style='font-weight: bold; font-size: 20px; margin-bottom: 16px;'>" . htmlspecialchars($user['name'] ?? '') . "</h2>
                <table class=\"TableToExport\" style=\"width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 18px; text-align: left; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); border-radius: 8px; overflow: hidden;\">
                    <thead>
                        <tr style=\"background-color: #007BFF; color: white;\">
                            <th style='padding: 12px 15px;'>Created Date</th>
                            <th style='padding: 12px 15px;'>Group</th>
                            <th style='padding: 12px 15px;'>Task Title</th>
                            <th style='padding: 12px 15px;'>Creator</th>
                            <th style='padding: 12px 15px;'>Status</th>
                            <th style='padding: 12px 15px;'>Tags</th>
                            <th style='padding: 12px 15px;'>Duration</th>
                        </tr>
                    </thead>
                    <tbody>";

            // Group tasks by date
            $tasksByDate = [];
            if (isset($user['tasks']) && is_array($user['tasks'])) {
                foreach ($user['tasks'] as $task) {
                    $dateKey = isset($task['createdDate']) ? trimDatePHP($task['createdDate']) : 'Unknown';
                    if (!isset($tasksByDate[$dateKey])) {
                        $tasksByDate[$dateKey] = [];
                    }
                    $tasksByDate[$dateKey][] = $task;
                }
            }

            ksort($tasksByDate); // Sort dates alphabetically
            $overallTotalDuration = 0;

            foreach ($tasksByDate as $date => $dailyTasks) {
                $dailyTotalDuration = calculateTotalDurationPHP($dailyTasks);
                $overallTotalDuration += $dailyTotalDuration;

                foreach ($dailyTasks as $task) {
                    $creatorIconDomain = "";
                    if (isset($task['creator']['icon']) && preg_match('/^https:\/\/([^\/]+)/', $task['creator']['icon'], $matches)) {
                        $creatorIconDomain = $matches[1];
                    }
                    
                    $detailHtml .= "<tr style=\"background-color: #f9f9f9;\">
                        <td style='padding: 12px 15px;'>" . htmlspecialchars(trimDatePHP($task['createdDate'] ?? '')) . "</td>
                        <td style='padding: 12px 15px;'>" . htmlspecialchars($task['group']['name'] ?? '') . "</td>
                        <td style='padding: 12px 15px;'>
                            <a href=\"https://" . htmlspecialchars($creatorIconDomain) . "/company/personal/user/" . htmlspecialchars($user['id'] ?? '') . "/tasks/task/view/" . htmlspecialchars($task['taskId'] ?? '') . "/\" target=\"_blank\" style=\"color:rgb(0, 0, 0); text-decoration: none;\">" . htmlspecialchars($task['title'] ?? '') . "</a>
                        </td>
                        <td style='padding: 12px 15px;'>" . htmlspecialchars($task['creator']['name'] ?? '') . "</td>
                        <td style='padding: 12px 15px;'>" . htmlspecialchars(isset($task['status']) ? ($statusLabels[$task['status']] ?? "Unknown") : '') . "</td>
                        <td style='padding: 12px 15px;'>" . htmlspecialchars(isset($task['tags']) ? displayTagsPHP($task['tags']) : '') . "</td>
                        <td style='padding: 12px 15px;'>" . htmlspecialchars(convertSecondsToTimePHP($task['duration'] ?? 0)) . "</td>
                    </tr>";
                }

                $detailHtml .= "<tr style=\"background-color: #e6e6e6; font-weight: bold;\">
                    <td colspan=\"6\" style='padding: 12px 15px;'>Daily Total (" . htmlspecialchars($date) . ")</td>
                    <td style='padding: 12px 15px;'>" . htmlspecialchars(convertSecondsToTimePHP($dailyTotalDuration)) . "</td>
                </tr>";
            }

            $detailHtml .= "<tr style=\"background-color: #ccc; font-weight: bold;\">
                <td colspan=\"6\" style='padding: 12px 15px;'>Total Time Taken</td>
                <td style='padding: 12px 15px;'>" . htmlspecialchars(convertSecondsToTimePHP($overallTotalDuration)) . "</td>
            </tr>";

            $detailHtml .= "</tbody></table></div>";
        }

        $grandTotalDuration = 0;
        foreach ($report as $user) {
            $grandTotalDuration += calculateTotalDurationPHP($user['tasks'] ?? []);
        }
        $detailHtml .= "<h2 style='font-weight: bold; font-size: 24px; margin-top: 32px;'>Grand Total Time Taken: " . htmlspecialchars(convertSecondsToTimePHP($grandTotalDuration)) . "</h2>";
    }

    if ($type === 'grouped') {
        // 4. Reconstruct Grouped Project Report
        $projectGroupedHtml = "<h2 style='font-weight: bold; font-size: 20px; margin-top: 32px;'>Grouped Project Report</h2>";
        $grandTotalDurationGrouped = 0;

        foreach ($report as $user) {
            $projectGroupedHtml .= "<h2 style='font-weight: bold; font-size: 20px; margin-bottom: 16px;'>" . htmlspecialchars($user['name'] ?? '') . "</h2>   
            <table style=\"width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 18px; text-align: left; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); border-radius: 8px; overflow: hidden;\">   
            <thead>
                <tr style=\"background-color: #007BFF; color: white;\">
                    <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Group</th>
                    <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Task Title(s)</th>
                    <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Creator(s)</th>
                    <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Status</th>
                    <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Tags</th>
                    <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Total Duration</th>
                </tr>
            </thead>
            <tbody>";

            $groupedTasks = [];
            if (isset($user['tasks']) && is_array($user['tasks'])) {
                foreach ($user['tasks'] as $task) {
                    $groupName = isset($task['group']['name']) && !empty($task['group']['name']) ? $task['group']['name'] : "Unknown Group";
                    if (!isset($groupedTasks[$groupName])) {
                        $groupedTasks[$groupName] = [
                            'taskTitles' => [],
                            'creators' => [],
                            'statuses' => [],
                            'tags' => [],
                            'totalDuration' => 0
                        ];
                    }
                    $groupedTasks[$groupName]['taskTitles'][] = $task['title'] ?? "";
                    if (isset($task['creator']['name'])) {
                        $groupedTasks[$groupName]['creators'][] = $task['creator']['name'];
                    }
                    if (isset($task['status'])) {
                        $groupedTasks[$groupName]['statuses'][] = $statusLabels[$task['status']] ?? "Unknown";
                    }
                    if (isset($task['tags'])) {
                        if (is_array($task['tags'])) {
                            foreach ($task['tags'] as $key => $val) {
                                if (is_array($val)) {
                                    $groupedTasks[$groupName]['tags'][] = $val['title'] ?? $val;
                                } else {
                                    $groupedTasks[$groupName]['tags'][] = $val;
                                }
                            }
                        }
                    }
                    $groupedTasks[$groupName]['totalDuration'] += intval($task['duration'] ?? 0);
                }
            }

            $userTotalDuration = 0;
            foreach ($groupedTasks as $groupName => $groupData) {
                $userTotalDuration += $groupData['totalDuration'];
                
                $uniqueCreators = array_unique($groupData['creators']);
                $uniqueStatuses = array_unique($groupData['statuses']);
                $uniqueTags = array_unique($groupData['tags']);

                $projectGroupedHtml .= "<tr style=\"background-color: #f2f2f2;\">
                        <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>" . htmlspecialchars($groupName) . "</td>
                        <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>" . implode("<br>", array_map('htmlspecialchars', $groupData['taskTitles'])) . "</td>
                        <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>" . htmlspecialchars(implode(", ", $uniqueCreators)) . "</td>
                        <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>" . htmlspecialchars(implode(", ", $uniqueStatuses)) . "</td>
                        <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>" . htmlspecialchars(implode(", ", $uniqueTags)) . "</td>
                        <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>" . htmlspecialchars(convertSecondsToTimePHP($groupData['totalDuration'])) . "</td>
                    </tr>";
            }

            $projectGroupedHtml .= "<tr>
                    <td colspan='5' style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>Total Time Taken</td>
                    <td style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>" . htmlspecialchars(convertSecondsToTimePHP($userTotalDuration)) . "</td>
                </tr>";

            $grandTotalDurationGrouped += $userTotalDuration;
            $projectGroupedHtml .= "</tbody></table>";
        }

        $projectGroupedHtml .= "<h2 style='font-weight: bold; font-size: 24px; margin-top: 32px;'>Grand Total Time Taken: " . htmlspecialchars(convertSecondsToTimePHP($grandTotalDurationGrouped)) . "</h2>";
    }

    if ($type === 'master') {
        // 5. Reconstruct Master Sheet HTML
        $uniqueGroups = ["Non-Grouped Tasks"];
        foreach ($report as $user) {
            if (isset($user['tasks']) && is_array($user['tasks'])) {
                foreach ($user['tasks'] as $task) {
                    if (isset($task['group']['name']) && !empty($task['group']['name'])) {
                        if (!in_array($task['group']['name'], $uniqueGroups)) {
                            $uniqueGroups[] = $task['group']['name'];
                        }
                    }
                }
            }
        }

        $masterHtml = "<table class=\"TableToExport\" style=\"width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 18px; text-align: left; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); border-radius: 8px; overflow: hidden;\">
        <thead>
        <tr style=\"background-color: #007BFF; color: white;\">
            <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>User Name</th>";

        foreach ($uniqueGroups as $group) {
            $masterHtml .= "<th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>" . htmlspecialchars($group) . "</th>";
        }

        $masterHtml .= "<th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Total Duration</th>
        </tr>
        </thead>
        <tbody>";

        $groupTotals = array_fill_keys($uniqueGroups, 0);
        $grandTotalDuration = 0;

        foreach ($report as $user) {
            $groupDurations = [];
            if (isset($user['tasks']) && is_array($user['tasks'])) {
                foreach ($user['tasks'] as $task) {
                    $groupName = (isset($task['group']['name']) && !empty($task['group']['name'])) ? $task['group']['name'] : 'Non-Grouped Tasks';
                    $dur = isset($task['duration']) ? intval($task['duration']) : 0;
                    $groupDurations[$groupName] = ($groupDurations[$groupName] ?? 0) + $dur;
                    $groupTotals[$groupName] += $dur;
                }
            }

            $totalDuration = array_sum($groupDurations);
            $grandTotalDuration += $totalDuration;

            $masterHtml .= "<tr style=\"background-color: #f2f2f2;\">
            <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>" . htmlspecialchars($user['name'] ?? '') . "</td>";

            foreach ($uniqueGroups as $group) {
                $duration = $groupDurations[$group] ?? 0;
                $masterHtml .= "<td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>" . htmlspecialchars(convertSecondsToTimePHP($duration)) . "</td>";
            }

            $masterHtml .= "<td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>" . htmlspecialchars(convertSecondsToTimePHP($totalDuration)) . "</td>
        </tr>";
        }

        // Total row
        $masterHtml .= "<tr style=\"background-color: #007BFF; color: white;\">
        <td style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>Total</td>";

        foreach ($uniqueGroups as $group) {
            $total = $groupTotals[$group] ?? 0;
            $masterHtml .= "<td style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>" . htmlspecialchars(convertSecondsToTimePHP($total)) . "</td>";
        }

        $masterHtml .= "<td style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>" . htmlspecialchars(convertSecondsToTimePHP($grandTotalDuration)) . "</td>
        </tr>
        </tbody></table>";

        $masterHtml .= "<h2 style='font-weight: bold; font-size: 24px; margin-top: 32px;'>Grand Total Time Taken: " . htmlspecialchars(convertSecondsToTimePHP($grandTotalDuration)) . "</h2>";
    }

    // Now proceed with spreadsheet creation as standard
    $spreadsheet = new Spreadsheet();
    $htmlReader = new Html();
    $spreadsheet->removeSheetByIndex(0); // Remove default sheet
    $sheetIndex = 0;

    if ($type === 'master') {
        $masterSheet = $htmlReader->loadFromString($masterHtml);
        $spreadsheet->addExternalSheet($masterSheet->getSheet(0), 0);
        $spreadsheet->getSheet(0)->setTitle('Master Sheet');
        applyProfessionalStyling($spreadsheet->getSheet(0));
    } else {
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
    }

    // Set headers to force download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="report.xlsx"');
    header('Cache-Control: max-age=0');

    // Write the file to the output
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// Fallback to legacy HTML/form post handling if anyone uses it
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
    exit;
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
    exit;
} else {
    echo "No report data received.";
}
?>
