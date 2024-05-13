<?php
include_once '../../db.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

$dateConfig = getCurrentDayConfiguration();

$attendance = getAttendance('../../'.$dateConfig['filename']);

echo(json_encode(array(
    "day" => $dateConfig['humanReadableDay'],
    "date" => $dateConfig['date'],
    "attendance" => $attendance
)));

fclose($fh);
