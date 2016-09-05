<?php
	// Turn on debugging for admin side
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

	include_once("../php/secret.php");
	include_once("../php/compareImages.php");

	$foundMatch = false;

	if (isset($_POST["url"]) && strlen($_POST["url"]) > 0) {
		$PDO = createConnection();

		$image = new compareImages($_POST["url"]);

		// If it's not being forced
		if (!(isset($_POST["force"]) && $_POST["force"] == "on")) {
			// Check to see if a similar image exists
			$image = new compareImages($_POST["url"]);
			$stmt = $PDO->query("SELECT * FROM images WHERE hash IS NOT NULL");
			$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

			foreach ($images as $possible) {
				$matchIndex = $image->compareHash($possible["hash"]);
				if ($matchIndex < 11) {
					echo "Possible match ($matchIndex): ";
					echo "<img src='{$possible['url']}' style='display: block; max-width: 400px; max-height: 400px;'>";
					$foundMatch = true;
				}
			}
		}

		if (!$foundMatch || (isset($_POST["force"]) && $_POST["force"] == "on")) {
			// Set the timezone
			date_default_timezone_set("America/Los_Angeles");

			$stmt = $PDO->prepare("SELECT * FROM images ORDER BY date DESC LIMIT 1");
			$stmt->execute();

			$last_date = $stmt->fetch(PDO::FETCH_ASSOC)["date"];

			// Insert into the database
			$stmt = $PDO->prepare("INSERT INTO images (url,date,hash) VALUES (:url,:date,:hash)");
			$stmt->bindValue(":url", $_POST["url"], PDO::PARAM_STR);
			$stmt->bindValue(":date", strtotime(date("F j, Y", $last_date) . " +1 day"), PDO::PARAM_STR); // increment the day once
			$stmt->bindValue(":hash", $image->getHasString(), PDO::PARAM_STR);
			$stmt->execute();

			echo "Inserted url '{$_POST['url']}'.";
			echo "<img src='{$_POST['url']}' style='display: block; max-width: 400px; max-height: 400px;'>";
		}
	}
?>

<form action="" method="POST">
	<input type="text" name="url">
	<?php if ($foundMatch) { ?>
	<input type="checkbox" name="force">
	<?php } ?>
	<input type="submit" value="Add Image">
</form>