<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if (strpos($_POST['name'].$_POST['time'], '"') != false) die("Bad data");
	if (strpos($_POST['name'].$_POST['time'], ',') != false) die("Bad data");
	$fh = fopen("../data/".date("Y-m-d").".db", "a") or die("Unable to open database!");	
	$txt = $_POST['name'].','.$_POST['time'];
	fwrite($fh, $txt."\n");
	fclose($fh);
	if ($_POST['saveName'] == 'on') {
		setcookie('save-name', $_POST['name'], time()+60*60*24*31*12);
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
    <nav class="navbar navbar-light bg-light">
      <span class="navbar-brand mb-0 h1" style="padding-left: 1rem">Mensa.JETZT</span>
    </nav>
  <div id="content" style="padding: 2rem;">
    <h2>Heute in der Mensa</h2><br>
    <table class="table">
      <thead>
        <tr>
          <th>Wer?</td>
          <th>Wann?</td>
	</tr>
      </thead>
      <?php       
       $fh = fopen('../data/'.date("Y-m-d").'.db','r');
       while ($line = fgets($fh)) {
         $data = explode(",", $line);
      ?>
      <tr>
        <td><?php echo $data[0]; ?></td>
	<td><?php echo $data[1]; ?></td>
      </tr>
      <?php	
        }
        fclose($fh);
      ?>
    </table><br>
    <div class="card" style="max-width: 30rem;">
      <div class="card-body">
        <h5 class="card-title">Du bist heute auch in der Mensa?</h5>
      <form method="POST">
      <input class="form-control" type="text" name="name" placeholder="Gebe hier deinen Namen ein" <?php if (isset($_COOKIE['save-name'])) { ?> value="<?php echo $_COOKIE['save-name'] ?>" readonly <?php } ?> /><br>
      <select class="form-control" name="time" value="12:00 Uhr">
        <option value="11:30 Uhr">11:30 Uhr</option>
        <option value="11:45 Uhr">11:45 Uhr</option>
        <option value="12:00 Uhr">12:00 Uhr</option>
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
