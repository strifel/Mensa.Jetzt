<?php
require_once 'db.php';
require_once 'config.php';
require_once 'auth.php';

if (isset($_COOKIE['auth'])) {
  $login = get_token_value($_COOKIE['auth']); 
} else {
  $login = FALSE;
}
$dateConfig = getCurrentDayConfiguration();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if (
   preg_match("/^[a-zA-Z0-9äöüß ]{1,21}$/s", $_POST['name']) != 1 ||
   array_search($_POST['time'], $times) === FALSE ||
   array_search($_POST['canteen'], $canteen_types) === FALSE
  ) {
    http_response_code(400);
    die("Bad data");
  }

  if (isset($_POST['saveName']) && $_POST['saveName'] == 'on') {
		setcookie('save-name', $_POST['name'], time()+60*60*24*31*12);
	}

  // Logged out users shall have id -1
  if ($login != FALSE) {
    $user_id = $login['user_id'];
  } else {
    $user_id = "-1";
  }

  $name_modifiers = "";

  if (array_key_exists($user_id, $verified_symbols)) {
    $name_modifiers = $verified_symbols[$user_id];
  }

  if (isset($_POST['verifiedName']) && $_POST['verifiedName'] == 'on' && isset($login['username'])) {
		$name_modifiers = $name_modifiers." (@".$login['username'].")";
	}

  $txt = $_POST['name'].','.$_POST['time'].','.$user_id.','.$_POST['canteen'].','.$name_modifiers;

  if (!isset($login['user_id']) || ($old = checkForDBEntryOfUser($dateConfig['filename'], $login['user_id'])) == FALSE) {
    appendLine($dateConfig['filename'], $txt);
  } else {
    replaceLine($dateConfig['filename'], $old, $txt."\n");
  }

  header("Location: /", true, 303);
	exit();
}
?>
<html>
  <head>
    <title><?php echo $pageTitle; ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name='robots' content='noindex,follow' />
    <link href="/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="/bootstrap/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        window.onblur = function() { window.onfocus = function () { location.reload(true) }};
    </script>
  </head>
  <body>
    <nav class="navbar navbar-light bg-light justify-content-between" style="padding: 0">
      <span class="navbar-brand mb-0 h1" style="padding-left: 1rem"><?php echo $pageTitle; ?></span>
      <form class="form-inline">
        <?php if ($login == FALSE) { ?>
          <a href="/oauth.php" class="btn btn-outline-success my-2 my-sm-0" style="margin-right: 1rem;margin-top: 10px !important">Login</a>
        <?php } else { ?>
          <button onclick='document.cookie.split(";").forEach(function(c) { document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); });location.reload()' class="btn btn-outline-success my-2 my-sm-0" style="margin-right: 1rem;margin-top: 10px !important">Logout</button>
        <?php } ?>
      </form>
    </nav>
  <div id="content" style="padding: 2rem;">
    <h2><?php echo $dateConfig['humanReadableDay']." ".$lang_overview_sentence; ?></h2><br>
    <table class="table">
      <thead>
        <tr>
          <th>Wer?</td>
          <?php if (sizeof($times) > 1) echo '<td>Wann?</td>'; ?>
          <?php if (sizeof($canteen_types) > 1) echo '<td>Wo?</td>'; ?>
	</tr>
      </thead>
      <?php       
       $attendance = getAttendance($dateConfig['filename'], TRUE);
       $readMyself = FALSE;
       foreach ($attendance as $data) {
         if (
          isset($login['user_id']) && 
          $data['user_id'] == $login['user_id']
         ) $readMyself = $data;
      ?>
      <tr>
        <td style="color: <?php echo $data['color']; ?>;"><?php echo $data['name'].$data['name_modifiers']; ?></td>
        <?php if (sizeof($times) > 1) echo '<td>'.$data['time'].'</td>'; ?>
        <?php if (sizeof($canteen_types) > 1) echo '<td>'.$data['canteen'].'</td>'; ?>
      </tr>
      <?php	
        }
      ?>
    </table><br>
    <div class="card" style="max-width: 30rem;">
      <div class="card-body">
        <h5 class="card-title">
          <?php if ($readMyself == FALSE) {
            echo str_replace("%word", $dateConfig['humanReadableDay'], $lang_also_there_sentence);
          } else {
            echo str_replace("%word", $dateConfig['humanReadableDay'], $lang_change_time_sentence);
          } ?>
        </h5>
      <form method="POST">
      <input class="form-control" type="text" name="name" pattern="^[a-zA-Z0-9äöüß ]{1,21}$" placeholder="Gib hier deinen Namen ein" value="<?php if ($readMyself != FALSE) echo $readMyself["name"]; else if (isset($_COOKIE['save-name'])) echo $_COOKIE['save-name']; else if (isset($login['name'])) echo $login['name']; ?>" /><br>
      <select class="form-control" name="time" <?php if (sizeof($times) <= 1) echo 'style="display:none"'; ?>>
      <?php foreach ($times as $time) {
          if (
            ($readMyself != FALSE && $readMyself['time'] == $time) ||
            ($readMyself == FALSE && $time == $default_time)
          ) {
            echo('<option value="'.$time.'" selected="selected">'.$time.'</option>');
          } else {
            echo('<option value="'.$time.'">'.$time.'</option>');
          }
        } ?>
      </select><br>
      <select class="form-control" name="canteen" <?php if (sizeof($canteen_types) <= 1) echo 'style="display:none"'; ?>>
        <?php foreach ($canteen_types as $canteen) {
          if ($readMyself != FALSE && $readMyself['canteen'] == $canteen) {
            echo('<option value="'.$canteen.'" selected="selected">'.$canteen.'</option>');
          } else {
            echo('<option value="'.$canteen.'">'.$canteen.'</option>');
          }
        } ?>
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
      <?php
        if (isset($login['username'])) {
      ?>
        <div class="form-check">
          <input type="checkbox" name="verifiedName" checked class="form-check-input" id="verifiedNameCheck">
          <label class="form-check-label" for="verifiedNameCheck">Verifizierten Username zeigen</label>
        </div><br>
      <?php
       }
      ?>
      <button class="btn btn-primary" type="submit">Speichern</button>
    </form></div></div>
  </div>
  </body>
</html>
