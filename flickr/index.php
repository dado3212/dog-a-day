<?php
  error_reporting(-1);
  ini_set('display_errors', 'On');

  include_once("secret.php");

  $breeds = [
    "snow puppies"
  ];
?>
<html>
  <head>
    <style>
      #breeds {
        height: 56px;
        overflow-x: scroll;
        white-space: nowrap;
      }

      #breeds .breed {
        height: 34px;
        background-color: #eee;
        border-radius: 5px;
        margin: 3px 5px;
        display: inline-block;
        line-height: 34px;
        text-align: center;
        padding: 0px 10px;

        cursor: pointer;
      }

      #breeds .breed.active {
        background-color: rgba(0, 128, 0, 0.39);
      }

      #current {
        max-height: calc(100% - 160px);
        overflow-x: scroll;
        white-space: nowrap;
      }

      #current .image {
        max-height: 100%;
        display: inline-block;
        margin: 0 5px;
      }

      img {
        height: 100%;
      }

      #selected {
        height: 90px;
        overflow-x: scroll;
        white-space: nowrap;
      }

      #selected .chosen-image {
        display: inline-block;
        height: 100%;
      }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="main.js"></script>
  </head>
  <body>
    <div id="breeds">
      <?php
        for ($i = 0; $i < count($breeds); $i++) {
          echo "<div class='breed" . (($i == 0) ? " active" : "") . "' data-breed='{$breeds[$i]}'><span class='name'>{$breeds[$i]}</span></div>";
        }
      ?>
    </div>
    <div id="current">
    </div>
    <button>Download More</button>
    <div id="selected">

    </div>
  </body>
</html>