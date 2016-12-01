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

	// Pull the greeting, or generate one
	if ($image["email"] == "") {
		$greeting = $greetings[rand(0,count($greetings)-1)];
	} else {
		$greeting = $image["email"];
	}
	// Create and send a campaign using MailChimp
	createCampaign($image["url"], $greeting);

	// Post photo to Instagram (or at least try)
	if ($image["instagram"] == "") {
		$caption = "The dog for " . date("F j") . "! #dogaday #dog #dogsofinsta";
	} else {
		$caption = "The dog for " . date("F j") . "! " . $image["instagram"];
	}
  postPhoto($image["url"], $caption, false);
?>