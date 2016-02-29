(function () {
  var slider = $('.widget.slider');
  $(window).on('resize', function () {
    slider.css('height', window.innerHeight);
  });

  slider.css('height', window.innerHeight);

  $('.widget.nav-menu li').each(function (i, e) {
    e.dataset.scrollNav = i;
  });
  
  $('.scroll-index').each(function (i, e) {
    e.dataset.scrollIndex = i;
  });
  
  $('.widget.nav-menu:not(.no-scroll-it)').first().scrollIt();
})();