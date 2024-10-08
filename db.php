<?php

include_once 'config.php';

function getCurrentDayConfiguration() {
  global $dataDirectory;
  global $endHour;

  if (date("N") == 6) {
    $date = date("Y-m-d", time() + 60 * 60 * 24 * 2);
    $humanReadableDay = "Montag";
  } else if (date("N") == 7) {
    $date = date("Y-m-d", time() + 60 * 60 * 24);
    $humanReadableDay = "Montag";
  } else if (date("N") == 5 && date("H") >= $endHour) {
    $date = date("Y-m-d", time() + 60 * 60 * 24 * 3);
    $humanReadableDay = "Montag";
  } else if (date("H") >= $endHour) {
    $date = date("Y-m-d", time() + 60 * 60 * 24);
    $humanReadableDay = "Morgen";
  } else {
    $date = date("Y-m-d");
    $humanReadableDay = "Heute";
  }

  $filename = $dataDirectory . "/" . $date . ".db";
  return [
    'filename' => $filename,
    'date' => $date,
    'humanReadableDay' => $humanReadableDay
  ];
}

function openFile($filename, $mode) {
	if (!file_exists($filename)) {
		touch($filename);
	}
	return fopen($filename, $mode);
}

function checkForDBEntryOfUser($filename, $user_id) {
  $fh = openFile($filename,'r');
  while ($line = fgets($fh)) {
    if (str_replace("\n", "", explode(",", $line)[2]) == $user_id) return $line;
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
    $fh = openFile($filename, "a") or die("Unable to open database!");
	fwrite($fh, $line."\n");
	fclose($fh);
}

function getAttendance($filename, $get_confidential=FALSE) {
  global $verified_colors;

  $attendance = [];

  $fh = openFile($filename, 'r');
  while ($line = fgets($fh)) {
    $data = explode(",", str_replace("\n", "", $line));

    $attendanceData = array(
      "name" => $data[0],
      "name_modifiers" => $data[4],
      "time" => $data[1],
      "canteen" => $data[3],
      "color" => ($data[2] != "" && array_key_exists($data[2], $verified_colors)) ? $verified_colors[$data[2]] : "#000000"
    );

    if ($get_confidential) {
      $attendanceData["user_id"] = $data[2];
    }
    $attendance[] = $attendanceData;
  }

  usort($attendance, 'sortByDate');
  return $attendance;
}

function sortByDate($a1, $a2) {
	global $times;

	$ai1 = array_search($a1["time"], $times);
	$ai2 = array_search($a2["time"], $times);

	if ($ai1 == $ai2) {
		return 0;
	} else if ($ai1 < $ai2) {
		return -1;
	} else {
		return 1;
	}
}
