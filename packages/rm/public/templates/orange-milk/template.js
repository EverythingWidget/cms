(function () {
  window.addEventListener('load', function () {
    var template_config = 'json|$template_settings';
    
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

    if (template_config['menu-id']) {
      $('#' + template_config['menu-id'] + ':not(.no-scroll-it)').first().scrollIt();
    }
  });
})();