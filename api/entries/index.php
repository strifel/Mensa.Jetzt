<?php
include_once '../../db.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

$dateConfig = getCurrentDayConfiguration();

// Handle relative paths correctly
if (substr($dateConfig['filename'], 0, 1) != "/") {
        $dateConfig['filename'] = "../../" . $dateConfig['filename'];
}

$attendance = getAttendance($dateConfig['filename']);

echo(json_encode(array(
    "day" => $dateConfig['humanReadableDay'],
    "date" => $dateConfig['date'],
    "attendance" => $attendance
)));