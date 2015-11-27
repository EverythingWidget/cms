/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* ===========================================================
 * jquery-onepage-scroll.js v1.3.1
 * ===========================================================
 * Copyright 2013 Pete Rojwongsuriya.
 * http://www.thepetedesign.com
 *
 * Create an Apple-like website that let user scroll
 * one page at a time
 *
 * Credit: Eike Send for the awesome swipe event
 * https://github.com/peachananr/onepage-scroll
 *
 * License: GPL v3
 *
 * ========================================================== */

!function ($) {

   var defaults = {
      sections: "section",
      sectionsContainer: null,
      easing: "Circ.easeInOut",
      animationTime: 1200,
      pagination: true,
      updateURL: false,
      keyboard: true,
      beforeMove: null,
      afterMove: null,
      loop: true,
      responsiveFallback: false,
      direction: 'vertical'
   };

   /*------------------------------------------------*/
   /*  Credit: Eike Send for the awesome swipe event */
   /*------------------------------------------------*/

   $.fn.swipeEvents = function () {
      return this.each(function () {

         var startX,
                 startY,
                 $this = $(this);

         $this.bind('touchstart', touchstart);

         function touchstart(event) {
            var touches = event.originalEvent.touches;
            if (touches && touches.length) {
               startX = touches[0].pageX;
               startY = touches[0].pageY;
               $this.bind('touchmove', touchmove);
            }
         }

         function touchmove(event) {
            var touches = event.originalEvent.touches;
            if (touches && touches.length) {
               var deltaX = startX - touches[0].pageX;
               var deltaY = startY - touches[0].pageY;

               if (deltaX >= 50) {
                  $this.trigger("swipeLeft");
               }
               if (deltaX <= -50) {
                  $this.trigger("swipeRight");
               }
               if (deltaY >= 50) {
                  $this.trigger("swipeUp");
               }
               if (deltaY <= -50) {
                  $this.trigger("swipeDown");
               }
               if (Math.abs(deltaX) >= 50 || Math.abs(deltaY) >= 50) {
                  $this.unbind('touchmove', touchmove);
               }
            }
         }

      });
   };

   $.fn.onepage_scroll = function (options) {
      var settings = $.extend({}, defaults, options),
              el = $(this),
              sections = $(settings.sections),
              sectionsContainer = $(settings.sectionsContainer),
              total = sections.length,
              status = "off",
              topPos = 0,
              leftPos = 0,
              lastAnimation = 0,
              quietPeriod = 500,
              paginationList = "";
      var mainMenu;

      $.fn.transformPage = function (settings, pos, index) {
         if (typeof settings.beforeMove == 'function')
            settings.beforeMove(index);

         // Just a simple edit that makes use of modernizr to detect an IE8 browser and changes the transform method into
         // an top animate so IE8 users can also use this script.
         //if ($('html').hasClass('ie8')) {
         if (settings.direction === 'horizontal') {
            var $this = $(this);
            $this.css({
               position: "absolute",
               display: ""
            });

            $this.stop().animate({
               left: pos + '%'
            }, settings.animationTime, settings.easing, function () {
               if (!$this.hasClass("active")) {
                  $this.hide();
               } else {
                  $this.css("position", "");
               }
            });
         } else {
            var toppos = (el.height() / 100) * pos;
            $this.animate({
               top: toppos + 'px'
            }, settings.animationTime, settings.easing);
         }
         //} else {
         /*$(this).css({
          "-webkit-transform": (settings.direction == 'horizontal') ? "translate3d(" + pos + "%, 0, 0)" : "translate3d(0, " + pos + "%, 0)",
          "-webkit-transition": "all " + settings.animationTime + "ms " + settings.easing,
          "-moz-transform": (settings.direction == 'horizontal') ? "translate3d(" + pos + "%, 0, 0)" : "translate3d(0, " + pos + "%, 0)",
          "-moz-transition": "all " + settings.animationTime + "ms " + settings.easing,
          "-ms-transform": (settings.direction == 'horizontal') ? "translate3d(" + pos + "%, 0, 0)" : "translate3d(0, " + pos + "%, 0)",
          "-ms-transition": "all " + settings.animationTime + "ms " + settings.easing,
          "transform": (settings.direction == 'horizontal') ? "translate3d(" + pos + "%, 0, 0)" : "translate3d(0, " + pos + "%, 0)",
          "transition": "all " + settings.animationTime + "ms " + settings.easing
          });*/
         //}
         $this.one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function (e) {
            if (typeof settings.afterMove === 'function')
               settings.afterMove(index);
         });
      };

      var updateURL = function (index) {
         if (history.replaceState && settings.updateURL === true) {
            var href = window.location.href.substr(0, window.location.href.indexOf('#')) + "#" + index;
            history.pushState({}, document.title, href);
         }
      };

      $.fn.moveDown = function () {
         var el = $(this);
         index = $(settings.sections + ".active").data("index");
         current = $(settings.sections + "[data-index='" + index + "']");
         next = $(settings.sections + "[data-index='" + (index + 1) + "']");
         var nextIndex = next.data("index");
         if (next.length < 1) {
            if (settings.loop == true) {
               pos = 0;
               next = $(settings.sections + "[data-index='1']");
            } else {
               return
            }

         } else {
            pos = (index * 100) * -1;
         }
         if (typeof settings.beforeMove == 'function')
            settings.beforeMove(next.data("index"));
         current.removeClass("active")
         next.addClass("active");
         if (settings.pagination === true) {
            $(".onepage-pagination li a" + "[data-index='" + index + "']").removeClass("active");
            $(".onepage-pagination li a" + "[data-index='" + nextIndex + "']").addClass("active");
         }
         $("body").removeClass("viewing-page-" + current.data("index")).addClass("viewing-page-" + nextIndex);
         if (mainMenu) {
            mainMenu.find("li.active").removeClass("active");
            mainMenu.find("li a[href='#" + (next.attr("data-container-id")) + "']").parent().addClass("active");
         }

         updateURL(index + 1);

         current.transformPage(settings, -100, nextIndex);
         next.css({left: '100%'});
         next.transformPage(settings, 0, nextIndex);
      };

      $.fn.moveUp = function () {
         var el = $(this);
         index = $(settings.sections + ".active").data("index");
         current = $(settings.sections + "[data-index='" + index + "']");
         next = $(settings.sections + "[data-index='" + (index - 1) + "']");

         if (next.length < 1) {
            if (settings.loop === true) {
               pos = ((total - 1) * 100) * -1;
               next = $(settings.sections + "[data-index='" + total + "']");
            } else {
               return
            }
         } else {
            pos = ((next.data("index") - 1) * 100) * -1;
         }
         if (typeof settings.beforeMove == 'function')
            settings.beforeMove(next.data("index"));
         current.removeClass("active")
         next.addClass("active")
         if (settings.pagination == true)
         {
            $(".onepage-pagination li a" + "[data-index='" + index + "']").removeClass("active");
            $(".onepage-pagination li a" + "[data-index='" + next.data("index") + "']").addClass("active");
         }

         if (mainMenu)
         {
            mainMenu.find("li.active").removeClass("active");
            mainMenu.find("li a[href='#" + (next.attr("data-container-id")) + "']").parent().addClass("active");
         }
         $("body").removeClass("viewing-page-" + current.data("index")).addClass("viewing-page-" + next.data("index"));

         updateURL(index - 1);

         current.transformPage(settings, 100, next.data("index"));
         next.css({left: '-100%'});
         next.transformPage(settings, 0, next.data("index"));
      };

      var moveTo = function (id, inst) {
         current = $(settings.sections + ".active");
         var next = $(settings.sections + "[data-container-id='" + id + "']");
         var page_index = next.data("index");
         if (next.length > 0)
         {
            next.addClass("active");
            if (next.attr("data-container-id") === current.attr("data-container-id")) {
               return;
            }

            if (typeof settings.beforeMove === 'function') {
               settings.beforeMove(next.data("index"));
            }
            current.removeClass("active");


            if (mainMenu) {
               mainMenu.find("li.active").removeClass("active");
               mainMenu.find("li a[href*='#" + (id) + "']").parent().addClass("active");
            }
            $("body").removeClass("viewing-page-" + current.data("index")).addClass("viewing-page-" + page_index);

            pos = ((page_index - 1) * 100) * -1;

            if (history.replaceState && settings.updateURL === true)
            {
               updateURL(page_index - 1);
            }

            if (inst) {
               if (settings.direction === 'horizontal') {
                  next.css({left: 0 + 'px', display: "", position: ""});
               } else {
                  var toppos = (el.height() / 100) * pos;
                  next.css({top: toppos + 'px'});
               }
            } else {
               if (next.data("index") > current.data("index")) {
                  current.transformPage(settings, -100, next.data("index"));
                  next.css({left: '100%'});
                  next.transformPage(settings, 0, next.data("index"));
               } else {
                  current.transformPage(settings, 100, next.data("index"));
                  next.css({left: '-100%'});
                  next.transformPage(settings, 0, next.data("index"));
               }
            }
         }
      };

      function responsive() {
         //start modification
         var valForTest = false;
         var typeOfRF = typeof settings.responsiveFallback

         if (typeOfRF == "number") {
            valForTest = $(window).width() < settings.responsiveFallback;
         }
         if (typeOfRF == "boolean") {
            valForTest = settings.responsiveFallback;
         }
         if (typeOfRF == "function") {
            valFunction = settings.responsiveFallback();
            valForTest = valFunction;
            typeOFv = typeof valForTest;
            if (typeOFv == "number") {
               valForTest = $(window).width() < valFunction;
            }
         }

         //end modification
         if (valForTest) {
            $("body").addClass("disabled-onepage-scroll");
            $(document).unbind('mousewheel DOMMouseScroll MozMousePixelScroll');
            el.swipeEvents().unbind("swipeDown swipeUp");
         } else {
            if ($("body").hasClass("disabled-onepage-scroll")) {
               $("body").removeClass("disabled-onepage-scroll");
               $("html, body, .wrapper").animate({scrollTop: 0}, "fast");
            }


            el.swipeEvents().bind("swipeDown", function (event) {
               if (!$("body").hasClass("disabled-onepage-scroll"))
                  event.preventDefault();
               el.moveUp();
            }).bind("swipeUp", function (event) {
               if (!$("body").hasClass("disabled-onepage-scroll"))
                  event.preventDefault();
               el.moveDown();
            });

            /*$(document).bind('mousewheel DOMMouseScroll MozMousePixelScroll', function (event) {
             event.preventDefault();
             var delta = event.originalEvent.wheelDelta || -event.originalEvent.detail;
             //init_scroll(event, delta);
             });*/
         }
      }


      function init_scroll(event, delta)
      {
         deltaOfInterest = delta;
         var timeNow = new Date().getTime();
         // Cancel scroll if currently animating or within quiet period
         if (timeNow - lastAnimation < quietPeriod + settings.animationTime) {
            event.preventDefault();
            return;
         }

         if (deltaOfInterest < 0) {
            el.moveDown()
         } else {
            el.moveUp()
         }
         lastAnimation = timeNow;
      }

      // Prepare everything before binding wheel scroll
      if (settings.sectionsContainer)
      {
         sectionsContainer.addClass("onepage-wrapper").css("position", "relative");
      } else
      {
         el.addClass("onepage-wrapper").css("position", "relative");
         $(window).resize(function ()
         {
            el.css("min-height", $(window).height());
         });
         el.css("min-height", $(window).height());
         //el.css("height", $(document).height());
      }

      if (settings.mainMenu)
      {
         mainMenu = $(settings.mainMenu);
         var menus = mainMenu.find("li a");
      }

      $.each(sections, function (i) {
         var $this = $(this);
         $this.css({
            position: "absolute",
            top: topPos + "%"
         }).addClass("section").attr("data-index", i + 1);

         if (settings.direction == 'horizontal')
         {
            $this.css({
               left: "100%",
               display: "none"
            });
         } else if (settings.direction == 'vertical')
         {
            $this.css({
               top: topPos + "%"
            });
         }

         if (settings.direction == 'horizontal')
            leftPos = leftPos + $this.outerWidth();
         else
            topPos = topPos + 100;


         if (settings.pagination === true) {
            paginationList += "<li><a data-index='" + (i + 1) + "' href='#" + (i + 1) + "'></a></li>";
         }
      });
//el.width();
      el.swipeEvents().bind("swipeDown", function (event) {
         if (!$("body").hasClass("disabled-onepage-scroll"))
            event.preventDefault();
         el.moveUp();
      }).bind("swipeUp", function (event) {
         if (!$("body").hasClass("disabled-onepage-scroll"))
            event.preventDefault();
         el.moveDown();
      });

      // Create Pagination and Display Them
      if (settings.pagination == true) {
         if ($('ul.onepage-pagination').length < 1)
            $("<ul class='onepage-pagination'></ul>").prependTo("body");

         if (settings.direction == 'horizontal') {
            posLeft = (el.find(".onepage-pagination").width() / 2) * -1;
            el.find(".onepage-pagination").css("margin-left", posLeft);
         } else {
            posTop = (el.find(".onepage-pagination").height() / 2) * -1;
            el.find(".onepage-pagination").css("margin-top", posTop);
            mainMenu = el.find(".onepage-pagination");
         }
         $('ul.onepage-pagination').html(paginationList);
      }



      if (window.location.hash != "" && window.location.hash != "#1")
      {
         init_index = window.location.hash.replace("#", "");
         var firstLI = $(settings.sections + "[data-index='1']");
         var next = $(settings.sections + "[data-container-id='" + init_index + "']");
         if (next.length > 0) {
            moveTo(init_index, true);
         } else {
            firstLI.addClass("active").css({left: "0px", display: "", position: ""});
            $("body").addClass("viewing-page-1");
            if (settings.pagination == true)
               $(".onepage-pagination li a" + "[data-index='1']").addClass("active");
         }
      } else
      {
         firstLI.addClass("active").css({left: "0px", display: "", position: ""});
         $("body").addClass("viewing-page-1");
         if (settings.pagination == true)
            $(".onepage-pagination li a" + "[data-index='1']").addClass("active");
      }

      if (mainMenu)
      {
         mainMenu.find("li a").click(function (e)
         {
            //var page_index = $(this).data("index");
            var index = $(this).attr("href");
            index = index.substr(index.indexOf('#') + 1);
            //alert(index);
            //alert($(this).attr("href"));
            moveTo(index);
         });
      }

      if (settings.responsiveFallback != false) {
         $(window).resize(function () {
            responsive();
         });

         responsive();
      }

      if (settings.keyboard == true) {
         $(document).off("keydown.onepage-scroll");
         $(document).on("keydown.onepage-scroll", function (e) {
            var tag = e.target.tagName.toLowerCase();

            if (!$("body").hasClass("disabled-onepage-scroll")) {
               switch (e.which) {
                  case 38:
                     if (tag != 'input' && tag != 'textarea' && settings.direction == "vertical")
                        el.moveUp()
                     break;
                  case 40:
                     if (tag != 'input' && tag != 'textarea' && settings.direction == "vertical")
                        el.moveDown()
                     break;
                  case 37: // left
                     if (tag != 'input' && tag != 'textarea' && settings.direction == "horizontal")
                        el.moveUp()
                     break;
                  case 39: // right
                     if (tag != 'input' && tag != 'textarea' && settings.direction == "horizontal")
                        el.moveDown()
                     break;
                  case 32: //spacebar
                     if (tag != 'input' && tag != 'textarea')
                        el.moveDown()
                     break;
                  case 33: //pageg up
                     if (tag != 'input' && tag != 'textarea')
                        el.moveUp()
                     break;
                  case 34: //page dwn
                     if (tag != 'input' && tag != 'textarea')
                        el.moveDown()
                     break;
                  case 36: //home
                     moveTo(1);
                     break;
                  case 35: //end
                     moveTo(total);
                     break;
                  default:
                     return;
               }
            }

         });
      }
      return false;
   }


}(window.jQuery);

