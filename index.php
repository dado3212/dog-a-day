<?php
	include("php/secret.php");

	$PDO = createConnection();

	// Set the timezone
	date_default_timezone_set("America/Los_Angeles");

	// Select all of the images from the database that have already been sent out
	if (isset($_GET["code"]) && $_GET["code"] == "$display") { // Show all for admin purposes
		$stmt = $PDO->prepare("SELECT * FROM images");
	} else {
		$stmt = $PDO->prepare("SELECT * FROM images WHERE date <= :date");
		$stmt->bindValue(":date", time() - 25200, PDO::PARAM_STR);
	}

	$stmt->execute();

	$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$sorted_images = [];

	// Sort the images by month and year
	foreach ($images as $image) {
		$year = date("Y", $image["date"]);
		$month = date("F", $image["date"]);

		$sorted_images[$year][$month][] = $image;
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

		<!-- SEO and Semantic Markup -->

		<meta name="robots" content="index, follow, archive">
		<meta charset="utf-8" />
		<meta http-equiv="Cache-control" content="public">

		<meta name="twitter:card" content="summary">
		<meta name="twitter:creator" content="@alex_beals">

		<meta property="og:type" content="website">
		<meta property="og:title" content="Dog-a-Day">
		<meta property="og:image" content="http://alexbeals.com/projects/puppies/DogADay.png">
		<meta property="og:url" content="http://alexbeals.com/projects/puppies">
		<meta property="og:description" content="Sign up to get a dog to your inbox every day.">

		<meta name="description" content="Sign up to get a dog to your inbox every day.">

		<title>Dog-a-Day</title>
		<link rel="stylesheet" type="text/css" href="assets/main.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script> 
		<script src="assets/main.js"></script>
	</head>
	<body>
		<img class="header" src="DogADay.png" alt="Dog-a-Day">

		<div class="subscribe">
			<p> Add someone to Dog-a-Day!  Just submit the person's name, and email, and they'll start
			receiving a dog straight to their inbox every morning at 7am PST.</p>
			<a id="subscribe" href="http://eepurl.com/cd9UQf">SUBSCRIBE</a>
		</div>
	<?php
		foreach ($sorted_images as $yearDate => $year) {
			echo "<div class='year'>";
			echo "<span class='year'>Previous Dogs - $yearDate</span>";
			foreach ($year as $monthDate => $month) {
				echo "<div class='month'>";
				echo "<span class='month'>$monthDate</span>";
				foreach ($month as $image) {
					$day = date("j", $image["date"]);
					$url = $image["url"];
					if (is_null($url)) {
						$url = "https://placeholdit.imgix.net/~text?txtsize=45&txt=ERROR&w=300&h=300";
						$thumbnail = $url;
					} else {
						$thumbnail = substr($url, 0, -4) . "t" . substr($url, -4);
					}
					echo "<a class='day' href='$url' target='_blank' style='background-image: url($thumbnail);'>";
					echo "<span class='day'>$day</span>";
					echo "</a>";
				}
				echo "</div>";
			}
			echo "</div>";
		}
	?>
	</body>
</html>