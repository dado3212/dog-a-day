var page = 1;

$(document).ready(function() {
  function chooseBreed($breed, $page = 1) {
    $.post("load.php", { query: $breed, page: $page })
    .done(function(data) {
      console.log(data);
      var photos = JSON.parse(data);

      var photoString = "";
      for (photo of photos) {
        photoString += "<div class='image' data-big='" + photo.big + "' data-small='" + photo.small + "'><img src='" + photo.small + "'></span></div>";
      }

      var elements = $(photoString);

      if ($page == 1) {
        $('#current').html(elements);
      } else {
        $('#current').append(elements);
      }

      // Add listener
      elements.on('click', function() {
        var a = $("<div class='chosen-image' data-href='" + $(this).data('big') + "'><img src='" + $(this).data('small') + "'></div>");
        $('#selected').append(a);
      
        a.on('click', function(event) {
          if (event.metaKey) { // command click -> remove it
            $(this).remove();
          } else {
            window.open($(this).data('href'), "_blank");
          }
        });
      });
    });
  }

  // Add listener to switch breeds
  $('#breeds .breed').on('click', function() {
    page = 1;
    chooseBreed($(this).data('breed'));
    $('.breed.active').removeClass('active');
    $(this).addClass('active');
  });

  // Add listener to load more
  $('button').on('click', function() {
    page += 1;
    chooseBreed($('#breeds .breed.active').data('breed'), page);
  });

  // Initially load the first breed
  chooseBreed($($('#breeds .breed')[0]).data('breed'));

  // Listen to navigation for dumb scrolling stuff
  window.onbeforeunload = function() {
    return true;
  };
})