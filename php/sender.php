<?php
	include_once("secret.php");

	// Ensure can't be called without secret key
	if (!((isset($_GET["key"]) && $_GET["key"] == "$key") || (isset($argv[1]) && $argv[1] == "$key"))) {
		die("Access Denied.");
	}

	// Include to send emails
	include("mailchimp.php");

	// Include to send Instagrams
	include("instagram.php");

	// Open up a database connection
	$PDO = createConnection();

	// Necessary to match with database
	date_default_timezone_set("America/Los_Angeles");

	// Get the proper URL to send out
	$stmt = $PDO->prepare("SELECT * FROM images WHERE date = :date");
	$stmt->bindValue(":date", strtotime(date("F j, Y")), PDO::PARAM_STR);
	$stmt->execute();

	$image = $stmt->fetch(PDO::FETCH_ASSOC);

	// List of all possible greetings
	$greetings = [
		"Hope your day is great!",
		"Have a fantastic day!",
		"Think of puppies!",
		"So cute!",
		"Hope the rest of your day is superb!",
		"A great way to wake up.",
		"Such a great dog.",
		"A good start to the day!",
	];

	// Only send an email/attempt to post if there's an image
	if ($image) {
		// Pull the greeting, or generate one
		if ($image["email"] == "") {
			$greeting = $greetings[rand(0,count($greetings)-1)];
		} else {
			$greeting = $image["email"];
		}
		// Create and send a campaign using MailChimp
		createCampaign($image["url"], $greeting);

		// Post photo to Instagram (or at least try) (use email caption if there's no IG specific one)
		$caption = $image["instagram"] == "" ? "#dogaday #dog #dogsofinsta" : trim($image["instagram"]);
		$chunks = explode(" ", $caption);
		$start = 0;
		for ($i = 0; $i < count($chunks); $i++) {
		  if ($chunks[$i][0] == '#') {
		    $start = $i;
		    break;
		  }
		}
		$suffix = implode(" ", array_slice($chunks, $start));
		if ($caption == $suffix && $image["email"] == "") {
		  $caption = "The dog for " . date("F j") . "! $suffix";
		} else if ($caption == $suffix) {
		  $caption = "The dog for " . date("F j") . "! " . $image["email"] . " $suffix";
		} else {
		  $caption = "The dog for " . date("F j") . "! " . $image["instagram"];
		}
	  postPhoto($image["url"], $caption, false);
	}
?>