<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $start_dates = $_POST['start_date'];
    $end_dates = $_POST['end_date'];

    // Insert submission
    $stmt = $pdo->prepare('INSERT INTO submissions (name) VALUES (?)');
    $stmt->execute([$name]);
    $submission_id = $pdo->lastInsertId();

    // Insert date ranges
    $stmt = $pdo->prepare('INSERT INTO date_ranges (submission_id, start_date, end_date) VALUES (?, ?, ?)');
    for ($i = 0; $i < count($start_dates); $i++) {
        $start_date = $start_dates[$i];
        $end_date = $end_dates[$i] ?: $start_date; // Use start date as end date if end date is not provided
        $stmt->execute([$submission_id, $start_date, $end_date]);
    }

    header("Location: results.php");
    exit();
}
?>