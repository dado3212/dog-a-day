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
		<link rel="stylesheet" type="text/css" href="assets/css/main.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

		<!-- FancyBox -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.pack.js"></script>

		<script src="assets/js/main.js"></script>
	</head>
	<body>
		<img class="header" src="DogADay.png" alt="Dog-a-Day">

		<div class="subscribe">
			<p>Add someone to Dog-a-Day!  Just submit the person's name, and email, and they'll start
			receiving a dog straight to their inbox every morning at 7am PST.</p>
			<a id="subscribe" href="http://eepurl.com/cd9UQf">SUBSCRIBE</a>
		</div>
		<h3>Previous Dogs</h3>
	<?php
		$yearIndex = 0;
		foreach ($sorted_images as $yearDate => $year) {
			$monthIndex = 0;
			foreach ($year as $monthDate => $month) {
				if (date('F') == $monthDate && date('Y') == $yearDate) {
					echo "<div class='month active'>";
				} else {
					echo "<div class='month'>";
				}
				echo "<div class='header'>";
				if ($monthIndex == 0 && $yearIndex == 0) {
					echo "<button class='prev hidden'>Previous</button>";
				} else {
					echo "<button class='prev'>Previous</button>";
				}
				echo "<span class='month'>$yearDate - $monthDate</span>";
				if ($monthIndex == count($year) - 1 && $yearIndex == count($sorted_images) - 1) {
					echo "<button class='next hidden'>Next</button>";
				} else {
					echo "<button class='next'>Next</button>";
				}
				echo "</div>";
				foreach ($month as $image) {
					$day = date("j", $image["date"]);
					$url = $image["url"];
					if (is_null($url)) {
						$url = "https://placeholdit.imgix.net/~text?txtsize=45&txt=ERROR&w=300&h=300";
						$thumbnail = $url;
					} else {
						$thumbnail = substr($url, 0, -4) . "t" . substr($url, -4);
					}
					echo "<a class='fancybox day' rel='group' href='$url' style='background-image: url($thumbnail);'>";
					echo "<span class='day'>$day</span>";
					echo "</a>";
				}
				echo "</div>";

				$monthIndex += 1;
			}
			$yearIndex += 1;
		}
	?>
	</body>
</html>