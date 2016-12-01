<?php
  include_once("secret.php");

  function sendRequest($url, $post, $post_data, $user_agent, $cookies) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://i.instagram.com/api/v1/' . $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    if($post) {
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    }

    if($cookies) {
      curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/ig_cookies.txt');
    } else {
      curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/ig_cookies.txt');
    }

    $response = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return array($http, $response);
  }

  function generateGUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', 
      mt_rand(0, 65535),
      mt_rand(0, 65535),
      mt_rand(0, 65535),
      mt_rand(16384, 20479),
      mt_rand(32768, 49151),
      mt_rand(0, 65535),
      mt_rand(0, 65535),
      mt_rand(0, 65535)
    );
  }

  function generateUserAgent() {  
    $resolutions = array('720x1280', '320x480', '480x800', '1024x768', '1280x720', '768x1024', '480x320');
    $versions = array('GT-N7000', 'SM-N9000', 'GT-I9220', 'GT-I9100');
    $dpis = array('120', '160', '320', '240');

    $ver = $versions[array_rand($versions)];
    $dpi = $dpis[array_rand($dpis)];
    $res = $resolutions[array_rand($resolutions)];

    return sprintf('Instagram 4.%d.%d Android(%d/%d.%d.%d; %s; %s; samsung; %s; %s; smdkc210; en_US',
      mt_rand(1,2),
      mt_rand(0,2),
      mt_rand(10,11),
      mt_rand(1,3),
      mt_rand(3,5),
      mt_rand(0,5),
      $dpi,
      $res,
      $ver,
      $ver
    );
   }

  function sign($data) {
     return hash_hmac('sha256', $data, 'b4a23f5e39b5929e0666ac5de94c89d1618a2916');
  }

  function getPostData($filename, $time) {
    if (!$filename) {
      echo "The image doesn't exist: '{$filename}'";
    } else {
      $post_data = array('device_timestamp' => $time, 'photo' => new CURLFile($filename,'image/jpeg','test'));
      return $post_data;
    }
  }

  function postPhoto($url, $caption, $debug = false) {
    global $ig_username, $ig_password;

    // Generate random variables
    $agent = generateUserAgent();
    $guid = generateGUID();
    $device_id = "android-{$guid}";
    $time = time();
    $filename = "tmp.jpg";

    // Check to see if successful
    $success = false;

    // Download the file from the URL
    file_put_contents($filename, fopen($url, 'r'));

    $data = json_encode([
      "device_id" => $device_id,
      "guid" => $guid,
      "username" => $ig_username,
      "password" => $ig_password,
      "Content-Type" => "application/x-www-form-urlencoded; charset=UTF-8"
    ]);
    $sign = sign($data);
    $data = 'signed_body=' . $sign . '.' . urlencode($data) . '&ig_sig_key_version=4';
    $login = sendRequest('accounts/login/', true, $data, $agent, false);

    if ($debug)
      echo "<pre>" . print_r($login, true) . "</pre>";

    if (strpos($login[1], "Sorry, an error occurred while processing this request.") === false && !empty($login[1])) {
      // Decode the array that is returned
      $obj = json_decode($login[1], true);

      if (!empty($obj)) {
        // Post the picture
        $data = getPostData($filename, $time);
        $post = sendRequest('media/upload/', true, $data, $agent, true);

        if ($debug)
          echo "<pre>" . print_r($post, true) . "</pre>";

        if (!empty($post[1])) {
          // Decode the response 
          $obj = json_decode($post[1], true);

          if (!empty($obj)) {
            $status = $obj['status'];

            if ($status == 'ok') {
              // Remove and line breaks from the caption
              $caption = preg_replace("/\r|\n/", "", $caption);

              $media_id = $obj['media_id'];
              $data = json_encode([
                "device_id" => $device_id,
                "guid" => $guid,
                "media_id" => $media_id,
                "caption" => trim($caption),
                "device_timestamp" => $time,
                "source_type" => 5,
                "filter_type" => 0,
                "extra" => [],
                "Content-Type" => "application/x-www-form-urlencoded; charset=UTF-8"
              ]);
              $sign = sign($data);
              $new_data = 'signed_body=' . $sign . '.' . urlencode($data) . '&ig_sig_key_version=4';

              // Now, configure the photo
              $conf = sendRequest('media/configure/', true, $new_data, $agent, true);

              if ($debug)
                echo "<pre>" . print_r($conf, true) . "</pre>";

              if (!empty($conf[1]) && strpos($conf[1], "login_required") === false) {
                $obj = json_decode($conf[1], true);
                $status = $obj['status'];

                if($status != 'fail') {
                  $success = true;
                }
              }
            }
          }
        }
      }
    }

    unlink("tmp.jpg");
    return $success;
  }
?>