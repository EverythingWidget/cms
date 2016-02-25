(function () {
  var slider = $('.widget.slider');
  $(window).on('resize',function () {
    slider.css('height',window.innerHeight);
  });
  
  slider.css('height',window.innerHeight);
})();