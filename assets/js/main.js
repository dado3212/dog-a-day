$(document).ready(function() {
  $('.header button.prev:not(.hidden)').on('click', function() {
    $(this).parent().parent().removeClass('active');
    $(this).parent().parent().prev().addClass('active');
  });

  $('.header button.next:not(.hidden)').on('click', function() {
    $(this).parent().parent().removeClass('active');
    $(this).parent().parent().next().addClass('active');
  });

  $('.fancybox').fancybox({
      openEffect  : 'elastic',
      closeEffect  : 'elastic',

      helpers : {
        title : {
          type : 'inside'
        }
      }
    });
})