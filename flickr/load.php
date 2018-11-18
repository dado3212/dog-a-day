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
    $url .= '&extras=' . urlencode("url_h,url_k,url_o,url_l");
    $url .= "&page=" . $page;

    $response = json_decode(file_get_contents($url), true);

    $response_photos = array_filter($response["photos"]["photo"], function($photo) {
      return !in_array($photo['owner'], [
        '10937887@N00',
        '167230489@N03',
      ]);
    });

    $size = "z"; // m - medium, z - bigger, b - biggest

    $photos = [];
    foreach ($response_photos as $photo) {
      // echo "<pre>" . var_export($photo, true) . "</pre>";
      if (isset($photo['url_k'])) {
        $big = $photo['url_k'];
      } elseif (isset($photo['url_o']) && max($photo['height_o'], $photo['width_o']) < 3000) {
        $big = $photo['url_o'];
      } else {
        $big = $photo['url_l'];
      }
      $photos[] = [
        "small" => "https://farm{$photo["farm"]}.staticflickr.com/{$photo["server"]}/{$photo["id"]}_{$photo["secret"]}_{$size}.jpg",
        "big" => $big,
        "query" => $query,
        "owner" => $photo["owner"],
      ];
    }
    return $photos;
  }

  if (isset($_POST["query"])) {
    echo json_encode(getPhotos($_POST["query"], $_POST["page"]));
  }
?>