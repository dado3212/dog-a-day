<?php
  include_once("secret.php");

  error_reporting(-1);
  ini_set('display_errors', 'On');

  // Ensure can't be called without secret key
  if (!((isset($_GET["key"]) && $_GET["key"] == "$key") || (isset($argv[1]) && $argv[1] == "$key"))) {
    die("Access Denied.");
  }

  // Include to send emails
  include("mailchimp.php");

  // Open up a database connection
  $PDO = createConnection();

  // Necessary to match with database
  date_default_timezone_set("America/Los_Angeles");

  // Get the proper URL to send out
  $stmt = $PDO->prepare("SELECT * FROM images WHERE date = :date");
  $stmt->bindValue(":date", strtotime("December 24, 2018"), PDO::PARAM_STR);
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

  var_dump($image);

  // Only send an email/attempt to post if there's an image
  if ($image) {
    // Pull the greeting, or generate one
    if ($image["email"] == "") {
      $greeting = $greetings[rand(0,count($greetings)-1)];
    } else {
      $greeting = $image["email"];
    }
    // Create and send a campaign using MailChimp
    createCampaign($image["url"], $greeting, true);
  }
?>