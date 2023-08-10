<?php
include_once 'db.php';

if (isset($_COOKIE['PHPSESSID'])) {
  // Only start session (would set cookie) if we have consent by
  // user by logging in
  session_start([
    'cookie_lifetime' => 60*60*24*365,
    'read_and_close' => true
  ]);
}
$dateConfig = getCurrentDayConfiguration();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if (
   preg_match("/^[a-zA-Z0-9äöüß ]{1,20}$/s", $_POST['name']) != 1 ||
   preg_match("/^1[1-4]:[0-5][0-9] Uhr$/s", $_POST['time']) != 1
  ) {
    http_response_code(400);
    die("Bad data");
  }

  if (isset($_POST['saveName']) && $_POST['saveName'] == 'on') {
		setcookie('save-name', $_POST['name'], time()+60*60*24*31*12);
	}

  // Logged out users shall have id -1
  if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
  } else {
    $user_id = "-1";
  }

  $txt = $_POST['name'].','.$_POST['time'].','.$user_id;

  if (!isset($_SESSION['user_id']) || ($old = checkForDBEntryOfSession($dateConfig['filename'])) == FALSE) {
    appendLine($dateConfig['filename'], $txt);
  } else {
    replaceLine($dateConfig['filename'], $old, $txt);
  }

  header("Location: /", true, 303);
	exit();
}
?>
<html>
  <head>
    <title>Mensa.JETZT</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="/bootstrap/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  </head>
  <body>
    <nav class="navbar navbar-light bg-light justify-content-between" style="padding: 0">
      <span class="navbar-brand mb-0 h1" style="padding-left: 1rem">Mensa.JETZT</span>
      <form class="form-inline">
        <?php if (!isset($_SESSION['user_id'])) { ?>
          <a href="/oauth.php" class="btn btn-outline-success my-2 my-sm-0" style="margin-right: 1rem;margin-top: 10px !important">Login</a>
        <?php } else { ?>
          <button onclick='document.cookie.split(";").forEach(function(c) { document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); });location.reload()' class="btn btn-outline-success my-2 my-sm-0" style="margin-right: 1rem;margin-top: 10px !important">Logout</button>
        <?php } ?>
      </form>
    </nav>
  <div id="content" style="padding: 2rem;">
    <h2><?php echo $dateConfig['humanReadableDay']; ?> in der Mensa</h2><br>
    <table class="table">
      <thead>
        <tr>
          <th>Wer?</td>
          <th>Wann?</td>
	</tr>
      </thead>
      <?php       
       $attendance = getAttendance($dateConfig['filename'], TRUE);
       $readMyself = FALSE;
       foreach ($attendance as $data) {
         if (
          isset($_SESSION['user_id']) && 
          $data['user_id'] == $_SESSION['user_id']
         ) $readMyself = $data;
      ?>
      <tr>
        <td><?php echo $data['name']; ?></td>
	<td><?php echo $data['time']; ?></td>
      </tr>
      <?php	
        }
      ?>
    </table><br>
    <div class="card" style="max-width: 30rem;">
      <div class="card-body">
        <h5 class="card-title">
          <?php if ($readMyself == FALSE) { ?>
            Du bist <?php echo $dateConfig['humanReadableDay']; ?> auch in der Mensa?
          <?php } else { ?>
            Du bist doch wann anders in der Mensa?
          <?php } ?>
        </h5>
      <form method="POST">
      <input class="form-control" type="text" name="name" pattern="^[a-zA-Z0-9äöüß ]{1,20}$" placeholder="Gebe hier deinen Namen ein" value="<?php if ($readMyself != FALSE) echo $readMyself["name"]; else if (isset($_COOKIE['save-name'])) echo $_COOKIE['save-name']; else if (isset($_SESSION['name'])) echo $_SESSION['name']; ?>" /><br>
      <select class="form-control" name="time">
        <option value="11:30 Uhr">11:30 Uhr</option>
        <option value="11:45 Uhr">11:45 Uhr</option>
        <option value="12:00 Uhr" selected="selected">12:00 Uhr</option>
        <option value="12:15 Uhr">12:15 Uhr</option>
        <option value="12:30 Uhr">12:30 Uhr</option>
        <option value="12:45 Uhr">12:45 Uhr</option>
        <option value="13:00 Uhr">13:00 Uhr</option>
        <option value="13:15 Uhr">13:15 Uhr</option>
        <option value="13:30 Uhr">13:30 Uhr</option>
        <option value="13:45 Uhr">13:45 Uhr</option>
        <option value="14:00 Uhr">14:00 Uhr</option>
	<option value="14:15 Uhr">14:15 Uhr</option>
      </select><br>
      <?php
        if (!isset($_COOKIE['save-name'])) {
      ?>
        <div class="form-check">
          <input type="checkbox" name="saveName" class="form-check-input" id="saveNameCheck">
          <label class="form-check-label" for="saveNameCheck">Namen speichern (speichert Cookie)</label>
        </div><br>
      <?php
       }
      ?>
      <button class="btn btn-primary" type="submit">Speichern</button>
    </form></div></div>
  </div>
  </body>
</html>