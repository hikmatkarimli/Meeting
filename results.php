<?php
require 'db.php';

// Function to merge overlapping date ranges
function mergeDateRanges($ranges)
{
    usort($ranges, function ($a, $b) {
        return $a['start_date'] <=> $b['start_date'];
    });

    $merged = [];
    foreach ($ranges as $range) {
        if (empty($merged) || $merged[count($merged) - 1]['end_date'] < $range['start_date']) {
            $merged[] = $range;
        } else {
            $merged[count($merged) - 1]['end_date'] = max($merged[count($merged) - 1]['end_date'], $range['end_date']);
        }
    }

    return $merged;
}

// Function to find the intersection of multiple date ranges
function findCommonDateRange($user_ranges)
{
    if (empty($user_ranges)) {
        return null;
    }

    $common_ranges = $user_ranges[0];
    foreach ($user_ranges as $ranges) {
        $new_common = [];
        foreach ($common_ranges as $common) {
            foreach ($ranges as $range) {
                $start = max($common['start_date'], $range['start_date']);
                $end = min($common['end_date'], $range['end_date']);
                if ($start <= $end) {
                    $new_common[] = ['start_date' => $start, 'end_date' => $end];
                }
            }
        }
        $common_ranges = $new_common;
        if (empty($common_ranges)) {
            break;
        }
    }

    return $common_ranges;
}

// Fetch submissions and date ranges from the database
$stmt = $pdo->query('
    SELECT submissions.name, date_ranges.start_date, date_ranges.end_date
    FROM submissions
    JOIN date_ranges ON submissions.id = date_ranges.submission_id
');
$rows = $stmt->fetchAll();

$submissions = [];
foreach ($rows as $row) {
    if (!isset($submissions[$row['name']])) {
        $submissions[$row['name']] = [];
    }
    $submissions[$row['name']][] = [
        'start_date' => $row['start_date'],
        'end_date' => $row['end_date']
    ];
}

$formatted_submissions = [];
$user_ranges = [];
foreach ($submissions as $name => $date_ranges) {
    $merged_ranges = mergeDateRanges($date_ranges);
    $formatted_submissions[] = ['name' => $name, 'date_ranges' => $merged_ranges];
    $user_ranges[] = $merged_ranges;
}

$common_ranges = findCommonDateRange($user_ranges);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Results</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Results</h1>
        <h2>All Submissions</h2>
        <ul>
            <?php foreach ($formatted_submissions as $submission): ?>
                <li><?php echo htmlspecialchars($submission['name']); ?>:
                    <?php foreach ($submission['date_ranges'] as $range): ?>
                        <?php if ($range['start_date'] == $range['end_date']): ?>
                            [<?php echo htmlspecialchars($range['start_date']); ?>]
                        <?php else: ?>
                            [<?php echo htmlspecialchars($range['start_date']); ?> -
                            <?php echo htmlspecialchars($range['end_date']); ?>]
                        <?php endif; ?>
                    <?php endforeach; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <h2>Common Date Range</h2>
        <?php if ($common_ranges): ?>
            <ul>
                <?php foreach ($common_ranges as $range): ?>
                    <?php if ($range['start_date'] == $range['end_date']): ?>
                        <li>[<?php echo htmlspecialchars($range['start_date']); ?>]</li>
                    <?php else: ?>
                        <li>[<?php echo htmlspecialchars($range['start_date']); ?> -
                            <?php echo htmlspecialchars($range['end_date']); ?>]
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Common Date Not Found</p>
        <?php endif; ?>
        <a href="index.php">Submit Another Date Range</a>
    </div>
</body>

</html>