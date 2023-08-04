<?php
include_once 'db.php';

if (date("N") == 6) {
    $filename = "../data/" . date("Y-m-d", time() + 60 * 60 * 24 * 2) . ".db";
    $humanReadableDay = "Montag";
} else if (date("N") == 7) {
    $filename = "../data/" . date("Y-m-d", time() + 60 * 60 * 24) . ".db";
    $humanReadableDay = "Montag";
} else if (date("N") == 5 && date("H") >= 15) {
    $filename = "../data/" . date("Y-m-d", time() + 60 * 60 * 24 * 3) . ".db";
    $humanReadableDay = "Montag";
} else if (date("H") >= 15) {
    $filename = "../data/" . date("Y-m-d", time() + 60 * 60 * 24) . ".db";
    $humanReadableDay = "Morgen";
} else {
    $filename = "../data/" . date("Y-m-d") . ".db";
    $humanReadableDay = "Heute";
}
header("Content-Type=application/json");

$attendance = [];

$fh = fopen($filename, 'r');
while ($line = fgets($fh)) {
    $data = explode(",", $line);

    $attendance[] = array(
        "name" => $data[0],
        "time" => $data[1]
    );
}
echo(json_encode(array(
    "day" => $humanReadableDay,
    "attendance" => $attendance
)));

fclose($fh);
