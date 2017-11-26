<?php
  error_reporting(-1);
  ini_set('display_errors', 'On');

  include_once("secret.php");

  function getPhotos($query, $page = 1) {
    global $flickr_key, $flickr_secret;
 
    $perPage = 100;
    $url = 'https://api.flickr.com/services/rest/?method=flickr.photos.search';
    $url .= '&api_key=' . $flickr_key;
    $url .= '&text=' . urlencode(strtolower($query));
    $url .= '&per_page=' . $perPage;
    $url .= '&format=json';
    $url .= '&dimension_search_mode=min';
    $url .= '&height=1024';
    $url .= '&width=1024';
    $url .= '&nojsoncallback=1';
    $url .= "&page=" . $page;

    $response = json_decode(file_get_contents($url), true);

    $size = "z"; // m - medium, z - bigger, b - biggest

    $photos = [];
    foreach ($response["photos"]["photo"] as $photo) {
      $photos[] = [
        "small" => "https://farm{$photo["farm"]}.staticflickr.com/{$photo["server"]}/{$photo["id"]}_{$photo["secret"]}_{$size}.jpg",
        "big" => "https://farm{$photo["farm"]}.staticflickr.com/{$photo["server"]}/{$photo["id"]}_{$photo["secret"]}_b.jpg",
        "query" => $query
      ];
    }
    return $photos;
  }

  if (isset($_POST["query"])) {
    echo json_encode(getPhotos($_POST["query"], $_POST["page"]));
  }
?>