/**
 * ScrollIt
 * ScrollIt.js(scroll•it•dot•js) makes it easy to make long, vertically scrolling pages.
 *
 * Latest version: https://github.com/cmpolis/scrollIt.js
 *
 * License <https://github.com/cmpolis/scrollIt.js/blob/master/LICENSE.txt>
 */
(function ($) {
  'use strict';

  var pluginName = 'ScrollIt',
    pluginVersion = '1.0.3';

  /*
   * OPTIONS
   */
  var defaults = {
    upKey: 38,
    downKey: 40,
    easing: 'Power3.easeInOut',
    scrollTime: 750,
    activeClass: 'active',
    onPageChange: null,
    topOffset: 0
  };

  $.fn.scrollIt = function (options) {
    /*
     * DECLARATIONS
     */
    var $this = this;
    var settings = $.extend(defaults, options),
      active = 0,
      lastIndex = $('[data-scroll-index]:last').attr('data-scroll-index'), watcher = true;

    /*
     * METHODS
     */
    /*var indic = document.createElement('div');
     indic.style.position = 'absolute';
     indic.style.width = '100%';
     indic.style.height = '10px';
     indic.style.backgroundColor = 'red';
     $('body').append(indic);*/
    /**
     * navigate
     *
     * sets up navigation animation
     */
    var navigate = function (ndx) {
      if (ndx < 0 || ndx > lastIndex)
        return;

      $('[data-scroll-nav]').removeClass(settings.activeClass);
      $('[data-scroll-nav=' + ndx + ']').addClass(settings.activeClass);

      watcher = false;
      var targetTop = $('[data-scroll-index=' + ndx + ']').offset().top + settings.topOffset;

      $('body').stop().animate({
        scrollTop: targetTop,
        easing: settings.easing
      },
        settings.scrollTime, function () {
          active = ndx;

          setTimeout(function () {
            watcher = true;
          }, 1);

        });
    };

    /**
     * doScroll
     *
     * runs navigation() when criteria are met
     */
    var doScroll = function (e) {
      var target = $(e.target).closest("[data-scroll-nav]").attr('data-scroll-nav') ||
        $(e.target).closest("[data-scroll-goto]").attr('data-scroll-goto');
      navigate(parseInt(target));

    };

    /**
     * keyNavigation
     *
     * sets up keyboard navigation behavior
     */
    var keyNavigation = function (e) {
      var key = e.which;
      if ($('body').is(':animated') && (key == settings.upKey || key == settings.downKey)) {
        return false;
      }

      if (key == settings.upKey && active > 0) {
        navigate(parseInt(active) - 1);
        return false;
      } else if (key == settings.downKey && active < lastIndex) {
        navigate(parseInt(active) + 1);
        return false;
      }
      return true;
    };

    /**
     * updateActive
     *
     * sets the currently active item
     */
    var updateActive = function (ndx) {
      if (settings.onPageChange && ndx && (active != ndx))
        settings.onPageChange(ndx);

      active = ndx;
      $('[data-scroll-nav]').removeClass(settings.activeClass);
      $('[data-scroll-nav=' + ndx + ']').addClass(settings.activeClass);
    };

    /**
     * watchActive
     *
     * watches currently active item and updates accordingly
     */
    var watchActive = function () {
      if (!watcher)
        return;

      var winTop = $(window).scrollTop(),
        scrollHeight = window.document.body.scrollHeight,
        innerHeight = window.innerHeight;

      var top = (winTop * scrollHeight) / (scrollHeight - innerHeight);
      //indic.style.top = top+'px';

      var visible = sections.filter(function (ndx, div) {
        return top >= $(div).offset().top + settings.topOffset; /*&&
         winTop < $(div).offset().top + (settings.topOffset) + $(div).outerHeight()*/

        winTop >= $(div).offset().top;
      });
      var newActive = visible.last().attr('data-scroll-index');
      updateActive(newActive);
    };

    /*
     * runs methods
     */

    var sections = $('[data-scroll-index]');
    $(window).on('scroll', watchActive).scroll();

    $(window).on('keydown', keyNavigation);

    $(this).on('click', '[data-scroll-nav], [data-scroll-goto]', function (e) {
      e.preventDefault();
      doScroll(e);
    });

    /*$('body').on('click','[data-scroll-nav], [data-scroll-goto]', function(e){
     e.preventDefault();
     doScroll(e);
     });*/

  };
}(jQuery));