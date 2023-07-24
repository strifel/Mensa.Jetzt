<?php
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