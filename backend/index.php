<?php
	// Turn on debugging for admin side
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

	include_once("../php/secret.php");
	include_once("../php/compareImages.php");

	// Set the timezone
	date_default_timezone_set("America/Los_Angeles");

	// Get the current latest image
	$PDO = createConnection();
	
	$stmt = $PDO->prepare("SELECT * FROM images ORDER BY date DESC LIMIT 1");
	$stmt->execute();
	$last_date = $stmt->fetch(PDO::FETCH_ASSOC)["date"];

	$foundMatch = false;

	if (isset($_POST["url"]) && strlen($_POST["url"]) > 0) {
		$image = new compareImages($_POST["url"]);

		// If the set date is later than the current latest date
		if (strtotime($_POST["date"]) > $last_date) {
			// Insert into the database
			$stmt = $PDO->prepare("INSERT INTO images (url,date,hash,email,instagram) VALUES (:url,:date,:hash,:email,:instagram)");
			$stmt->bindValue(":url", $_POST["url"], PDO::PARAM_STR);
			$stmt->bindValue(":date", strtotime($_POST["date"]), PDO::PARAM_STR);
			$stmt->bindValue(":hash", $image->getHasString(), PDO::PARAM_STR);
			$stmt->bindValue(":email", $_POST["caption"], PDO::PARAM_STR);
			$stmt->bindValue(":instagram", $_POST["ig"], PDO::PARAM_STR);
			$stmt->execute();

            $last_date = strtotime($_POST["date"]);
		}
	}
?>

<html>
  <head>
    <?php
      // Respect request desktop
      if (preg_match("/(iPhone|iPod|iPad|Android|BlackBerry|Mobile)/i", $_SERVER['HTTP_USER_AGENT'])) {
        ?><meta name="viewport" content="width=500"><?php
      }
    ?>

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="57x57" href="/assets/favicon/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/assets/favicon/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/assets/favicon/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/assets/favicon/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/assets/favicon/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/assets/favicon/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/assets/favicon/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/assets/favicon/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/favicon/apple-touch-icon-180x180.png">
    <link rel="icon" type="image/png" href="/assets/favicon/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/assets/favicon/favicon-194x194.png" sizes="194x194">
    <link rel="icon" type="image/png" href="/assets/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="/assets/favicon/android-chrome-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="/assets/favicon/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="/assets/favicon/manifest.json">
    <link rel="shortcut icon" href="/assets/favicon/favicon.ico">
    <meta name="msapplication-TileColor" content="#2b5797">
    <meta name="msapplication-TileImage" content="/assets/favicon/mstile-144x144.png">
    <meta name="msapplication-config" content="/assets/favicon/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    <title>Dog-a-Day</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/backend.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

    <!-- FancyBox -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.pack.js"></script>

    <script src="../assets/js/background.js"></script>
  </head>
  <body>
    <img class="header" src="../DogADay.png" alt="Dog-a-Day">

    <div class="details">
        <div id="chosen">
    	   <div id="image" class="blank" ondrop="drop(event);" ondragover="return false;"></div>
           <span></span>
        </div>
    	<div id="text">
	    	<form action="" method="POST" onsubmit="return verify(event);">
	    		<label for="caption">Email Label</label>
	    		<textarea name="caption" rows=3 placeholder="A good start to the day!" required></textarea>

	    		<label for="ig">Instagram Caption</label>
	    		<textarea name="ig" rows=3 placeholder="#dogaday #dog #dogsofinsta" required> #dogaday #dog #dogsofinsta</textarea>

	    		<label for="date">Date</label>
	    		<input type="date" name="date" value="<?php echo date("Y-m-d", strtotime(date("F j, Y", $last_date) . " +1 day")); ?>" required />

		    	<input type="hidden" name="url" />

		    	<button type="submit">Add Image</button>
		    </form>
    	</div>
    	<div id="similar">
    		<div class="image blank"></div>
    		<div class="image blank"></div>
    		<div class="image blank"></div>
    		<div class="image blank"></div>
    		<div class="image blank"></div>
    		<div class="image blank"></div>
    	</div>
    </div>
  </body>
</html>