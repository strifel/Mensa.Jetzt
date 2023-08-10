<?php

function getCurrentDayConfiguration() {
  if (date("N") == 6) {
    $date = date("Y-m-d", time() + 60 * 60 * 24 * 2);
    $humanReadableDay = "Montag";
  } else if (date("N") == 7) {
    $date = date("Y-m-d", time() + 60 * 60 * 24);
    $humanReadableDay = "Montag";
  } else if (date("N") == 5 && date("H") >= 15) {
    $date = date("Y-m-d", time() + 60 * 60 * 24 * 3);
    $humanReadableDay = "Montag";
  } else if (date("H") >= 15) {
    $date = date("Y-m-d", time() + 60 * 60 * 24);
    $humanReadableDay = "Morgen";
  } else {
    $date = date("Y-m-d");
    $humanReadableDay = "Heute";
  }
  $filename = "../data/" . $date . ".db";
  return [
    'filename' => $filename,
    'date' => $date,
    'humanReadableDay' => $humanReadableDay
  ];
}

function checkForDBEntryOfSession($filename) {
  $fh = fopen($filename,'r');
  while ($line = fgets($fh)) {
    if (str_replace("\n", "", explode(",", $line)[2]) == $_SESSION['user_id']) return $line;
  }
  fclose($fh);
  return FALSE;
}

function replaceLine($filename, $line1, $line2) {
    $content = file_get_contents($filename);
    $content = str_replace($line1, $line2, $content);
    file_put_contents($filename, $content);
}

function appendLine($filename, $line) {
    $fh = fopen($filename, "a") or die("Unable to open database!");
	fwrite($fh, $line."\n");
	fclose($fh);
}

function getAttendance($filename, $get_confidential=FALSE) {
  $attendance = [];

  $fh = fopen($filename, 'r');
  while ($line = fgets($fh)) {
    $data = explode(",", str_replace("\n", "", $line));

    $attendanceData = array(
      "name" => $data[0],
      "time" => $data[1],
      "canteen" => $data[3]
    );

    if ($get_confidential) {
      $attendanceData["user_id"] = $data[2];
    }

    $attendance[] = $attendanceData;
  }

  return $attendance;
}