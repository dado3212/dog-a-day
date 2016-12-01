<?php
  include_once("../php/secret.php");
  include_once("../php/compareImages.php");

  if (isset($_POST["url"])) {
    $PDO = createConnection();

    $image = new compareImages($_POST["url"]);

    // Check to see if a similar image exists
    $image = new compareImages($_POST["url"]);
    $stmt = $PDO->query("SELECT * FROM images WHERE hash IS NOT NULL");
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get all the matchings of the images to the similarity
    $data = [];

    foreach ($images as $possible) {
      $matchIndex = $image->compareHash($possible["hash"]);
      $data[$possible['url']] = $matchIndex;
    }

    asort($data);

    echo json_encode(["status" => "success", "closest" => array_slice($data, 0, 6, true)]);
  } else {
    echo json_encode(["status" => "error"]);
  }
?>