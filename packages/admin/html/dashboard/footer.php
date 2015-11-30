<script>
   System.init();
   //var EW = new EverythingWidgets();
   System.onLoadApp = function (app) {
      if (app.id === "Home") {
         $("#app-title").text("Home");
         $("#apps").hide();
         //$("#action-bar-items").empty();
         //$("#main-content").remove();
         $("#main-content").stop().animate({
            transform: "scale(1.14)",
            opacity: 0
         }, 500, "Power2.easeInOut", function () {
            this.remove();
         });
         $("#app-bar-nav").stop().animate({opacity: 0}, 300, function () {
            this.remove();
         });
         //$("#app-bar").removeClass("in");

         $("#app-bar").animate({className: "app-bar"}, 500, "Power2.easeInOut");
         $("#home-pane").animate({className: "home-pane in"}, 500, "Power2.easeInOut");
         return false;
      }
      return true;
   };
   System.onAppLoaded = function (app, response) {
      $("#apps").show();
//            $("#app-title").text(app.title);
      $("#action-bar-items").empty();
      $("#app-bar").animate({className: "app-bar in"}, 500, "Power2.easeInOut");
      $("#home-pane").animate({className: "home-pane"}, 500, "Power2.easeInOut");
      //$("#main-content").remove();
      //$("#app-bar-nav").remove();
      setTimeout(function ()
      {
         $("#app-content").append(response);

         EW.initSideBar();
         app.start();
      }, 500);

   };

   System.main.hashHandler = function (nav, params) {
      if (!nav["app"] && "Home" !== EW.oldApp)
      {
         $("#action-bar-items").children().animate({
            opacity: 0
         }, 300);
         EW.oldApp = "Home";
         $("#app-title").text(EW.apps["Home"].title);
         System.openApp(EW.apps["Home"]);
      }
   };

   System.on('app', function (path, app, sec) {
      if (!app) {
         app = "Home";
      }

      if (app !== EW.oldApp) {
         EW.oldApp = app;
         $("#app-title").text(EW.apps[app].title);
         System.openApp(EW.apps[app]);
         return;
      }
      /*if (app && System.main.modules[app])
       {
       alert("call to sec: "+path);
       //EW.appNav.setCurrentTab($("a[data-ew-nav='" + sec + "']"));
       }*/

      //base.setCurrentTab($("a[data-ew-nav='" + EW.getHashParameter("nav") + "']"));
   });

   EverythingWidgets.prototype.loadSections = function () {
      var self = this;
      this.apps = {Home: {id: "Home"}};
      $.get('~admin-api/EWCore/read_apps', {
         appDir: "admin"
      },
              function (data) {

                 var items = ['<ul class="apps-list">'];
                 $.each(data, function (key, val)
                 {
                    items.push('<li class=""><a class="app-link" data-app="' + val['id'] + '"><label>' + val['title'] + '</label><p>' + val['description'] + '</p></a></li>');
                    //val.package = "~admin";
                    val.file = "index.php";
                    val.id = val['id'];
                    self.apps[val['id']] = val;
                 });
                 items.push('</ul>');
                 $(items.join('')).appendTo("#home-pane");
                 $("#home-pane .apps-list a").click(function (e)
                 {
                    e.preventDefault();
                    System.setHashParameters({app: $(this).attr("data-app")}, null);
                 });
                 /*EW.addHashHandler(function (data)
                  {
                  //EW.loadApp(data);
                  if (!data.app)
                  data.app = "Home";
                  if (data.app !== EW.oldApp)
                  {
                  EW.oldApp = data.app;
                  System.openApp(EW.apps[data.app]);
                  }
                  });*/
                 //alert($(items.join('')).html());
                 System.start();
                 /* if (!System.getHashNav("app"))
                  System.setHashParameters({app: "Home"});*/
              }, "json");
   };

   EverythingWidgets.prototype.loadApp = function (data) {
      if (data.app !== this.oldApp) {
         this.oldApp = data.app;
         if (!data.app) {
            $("#app-title").text("Home");
            $("#apps").hide();
            //$("#action-bar-items").empty();

            //$("#app-bar-nav").remove();
            //$("#app-bar").removeClass("in");

            $("#app-bar").animate({className: "app-bar"}, 500, "Power2.easeOut");
            $("#home-pane").animate({className: "home-pane in"}, 500, "Power2.easeOut");

            return;
         }
         $("#apps").show();
         $("#app-title").text(this.apps[data.app].title);
         $("#action-bar-items").empty();
         $("#app-bar").animate({className: "app-bar in"}, 500, "Power2.easeOut");
         $("#home-pane").animate({className: "home-pane"}, 500, "Power2.easeOut");
         setTimeout(function () {
            $.post("~admin-api/" + data.app + "/index.php",
                    {},
                    function (response) {
                       $("#app-content").append(response);
                       EW.initSideBar();
                    });
         }, 500);
      }
   };

   $.fn.textWidth = function () {
      var html_org = $(this).html();
      var html_calc = '<span style="white-space:nowrap">' + html_org + '</span>';
      $(this).html(html_calc);
      var width = $(this).find('span:first').width();
      $(this).html(html_org);
      return width;
   };

   $.fn.comeIn = function (t) {

      if (!this.is(":visible") || this.css("visibility") != "visible")
      {
         var orgClass = "";
         $(this).stop(true, true);
         //$(this).removeClass("btn-hide");
         if ($(this).prop("class"))
            orgClass = $(this).prop("class").replace('btn-hide', '');
         $(this).addClass("btn-hide");
         $(this).css({
            display: ""
         });
         $(this).animate({
            className: orgClass
         },
                 t || 300, "Power2.easeOut");
      }
      return this;
      // Open popup code.
   };

   $.fn.comeOut = function (t) {
      if (!this.hasClass("btn-hide"))
      {
         $(this).stop(true, true);
         $(this).animate({
            className: $(this).prop("class") + " btn-hide"
         },
                 t || 300, "Power3.easeOut", function () {
                    $(this).hide();
                 });
      }
      return this;
      // Close popup code.
   };
   $.fn.loadingText = function (t) {
      return this;
   };

   ew_plugins.linkChooser = function (options) {
      var defaults = {
         callbackName: "function-reference"
      };
      var linkChooserDialog;
      function LinkChooser(element, options) {
         var base = this;
         var $element = $(element);
         defaults.callback = function (link) {
            $element.val(link).change();
            linkChooserDialog.trigger("close");
         };
         this.$element = $(element);
         var settings = $.extend({
         }, defaults, options);
         //$element.EW().putInWrapper();
         //var wrapper = this.$element.parent();
         if (linkChooserDialog)
            linkChooserDialog.remove();
         $element.EW().inputButton({
            title: '<i class="link-icon"></i>',
            label: 'tr{Link Chooser}',
            onClick: function (e) {
               linkChooserDialog = EW.createModal();
               $.post("<?php echo EW_DIR ?>~admin-html/content-management/file-chooser.php", {
                  callback: settings.callbackName,
                  data: $element.val()
               },
                       function (data) {
                          var functionRefrence = $("<div style='display:none;' id='function-reference'></div>");
                          functionRefrence.data("callback", settings.callback);
                          e = $(data);
                          e.append(functionRefrence);
                          linkChooserDialog.html(e);
                       });
            }
         });
      }
      return this.each(function () {
         //alert($.data(this, "ew-plugin-link-chooser"));
         if (!$.data(this, "ew_plugin_link_chooser")) {
            $.data(this, "ew_plugin_link_chooser", true);
            new LinkChooser(this, options);
         }
      });
   };
   ew_plugins.imageChooser = function (options) {
      var ACTIVE_PLUGIN_ATTR = "data-active-plugin-image-chooser";
      var defaults = {
         callbackName: "function-reference"
      };
      var imageChooserDialog;
      function ImageChooser(element, options) {
         var base = this;
         var $element = $(element);
         $element.off("change.image-chooser");
         $element.on("change.image-chooser", function () {
            image.attr("src", $element.val() || "asset/images/no-image.png");
         });
         defaults.callback = function (link) {
            imageChooserDialog.dispose();
         };

         var settings = $.extend({
         }, defaults, options);
         if (!$element.parent().attr("data-element-wrapper"))
            $element.wrap('<div class="element-wrapper" style="position:relative;padding-bottom:30px;" data-element-wrapper="true"><div style="border:1px dashed #ddd;background-color:#eee;display:block;overflow:hidden;" data-element-wrapper="true"></div></div>');
         $element.attr("type", "hidden");
         var wrapper = $element.parent().parent();
         if (imageChooserDialog)
            imageChooserDialog.remove();
         var image = wrapper.find("img");
         if (image.length <= 0) {
            image = $("<img>");
            console.log(image);
            wrapper.find("div").append(image);
         }
         image.css("max-height", $element.css("max-height"));
         var imageChooserBtn;
         // if the plugin has been called later again on same element
         if ($element.attr(ACTIVE_PLUGIN_ATTR)) {
            imageChooserBtn = wrapper.find("button.btn-image-chooser");
         }
         // If the plugin has been called for the first time
         else {
            image.attr("src", $element.val() || "asset/images/no-image.png");
            image.css({
               border: "none",
               outline: "none",
               minHeght: "128px",
               display: "block",
               float: "",
               margin: "2px auto 2px auto"
            });
            //dashed = $("<div style='border:2px dashed #aaa;display:block;overflow:hidden;'></div>");

            imageChooserBtn = $("<button type='button' class='btn btn-xs btn-link btn-link-chooser' >Choose Image</button>");
            imageChooserBtn.css({
               position: "absolute",
               right: "2px",
               bottom: "2px"
            });
            wrapper.append(imageChooserBtn);
            //wrapper.append(dashed);
            $element.attr(ACTIVE_PLUGIN_ATTR, true);
         }

         imageChooserBtn.click(function () {
            //if (!imageChooserDialog)

            imageChooserDialog = EW.createModal({
               //closeAction: "hide",
               autoOpen: false,
               class: "center-big"
            });
            imageChooserDialog.append("<div class='form-content'></div><div class='footer-pane row actions-bar action-bar-items' ></div>");
            $.post("<?php echo EW_DIR ?>~admin-api/content-management/Media.php", {
               callback: settings.callbackName
            },
                    function (data) {
                       imageChooserDialog.find(".form-content:first").append(data);
                       imageChooserDialog.prepend("<h1>Media</h1>");
                       /*var functionRefrence = $("<div style='display:none;' id='function-reference'></div>");
                        functionRefrence.data("callback", settings.callback);
                        imageChooserDialog.find("#link-chooser").append(functionRefrence);*/

                       var bSelectPhoto = EW.addAction("Select Photo", function () {
                          EW.setHashParameter("select-photo", true, "Media");
                       }, {
                          display: "none"
                       }).addClass("btn-success");
                       // create handler to track selected
                       var EWhandler = function ()
                       {
                          var url = EW.getHashParameter("absUrl", "Media");
                          if (url)
                             bSelectPhoto.comeIn(300);
                          else
                             bSelectPhoto.comeOut(200);
                          if (EW.getHashParameter("select-photo", "Media"))
                          {
                             EW.setHashParameter("select-photo", null, "Media");
                             imageChooserDialog.dispose();
                             //if (EW.getHashParameter("url", "Media"))
                             $element.val(EW.getHashParameter("absUrl", "Media")).change();
                             $element.attr("data-filename", EW.getHashParameter("filename", "Media"));
                             $element.attr("data-file-extension", EW.getHashParameter("fileExtension", "Media"));
                             $element.attr("data-url", EW.getHashParameter("url", "Media"));
                          }
                       };
                       EW.addURLHandler(EWhandler, "Media.ImageChooser");
                    });

            imageChooserDialog.open();
         });
      }
      return this.each(function () {
         if (!$.data(this, ACTIVE_PLUGIN_ATTR)) {
            $.data(this, ACTIVE_PLUGIN_ATTR, new ImageChooser(this, options));
         }
      });
   };

   function initPlugins(element) {
      if (!element.innerHTML && element.nodeName.toLowerCase() != 'input' && element.nodeName.toLowerCase() != 'textarea')
         return;
      var $element = $(element);
      EW.initPlugins($element);

      $element.find("a[rel='ajax']").each(function () {
         var a = $(this);
         if (a.attr("rel") === "ajax") {
            a.click(function (event) {
               event.preventDefault();
               var params = a.attr("href").split(",");
               $.each(params, function (k, v) {
                  if (v) {
                     var kv = v.split("=");
                     EW.setHashParameter(kv[0], kv[1]);
                  }
               });
            });
         }
      });
   }

   EverythingWidgets.prototype.initSideBar = function () {
      this.appNav = this.appNav || {};
      var $sidebar = $("#app-bar-nav");
      //var sbb = $("#side-bar-btn");
      $sidebar.prepend(EW.sidebarButton);
      $sidebar.css({
         left: "-250px"
      });
      $("#app-bar").prepend($sidebar);
      $sidebar.stop().animate({
         left: 0
      }, 200, "Power2.easeOut");
      //sidebar.attr("tabindex", 1);
      $sidebar.off("mouseleave");
      $sidebar.on("mouseleave", function () {
         $sidebar.stop().css({
            overflowY: "hidden"
         });

         $("#app-bar-nav.in").stop().animate({
            className: "app-bar-nav"
         }, 360, "Power3.easeOut");
      });
      $sidebar.off("click");
      $sidebar.on("click", function (e) {
         e.stopPropagation();
      });
      EW.sidebarButton.off("click mouseenter focus");
      EW.sidebarButton.on("click mouseenter focus", function (event) {
         $sidebar.css({
            maxHeight: $(window).height() - 100
         });

         $("#app-bar-nav:not(.in)").stop().animate({
            className: "app-bar-nav in",
            width: "250px"
         },
                 360, "Power4.easeOut", function () {
                    $sidebar.stop().css({
                       overflowY: "auto"
                    });
                    if (event.type === 'focus') {
                       $sidebar.find("a:first").focus();
                    }
                 });
         event.stopPropagation();
         $(window).on("click.sidebar", function ()
         {
            $sidebar.trigger("mouseleave");
            $(window).off("click.sidebar");
         });
      });
      //sbb = $("#side-bar-btn").detach();
      //sidebar.prepend(sbb);
      this.appNav.currentTab = null;
      var oldHref = null
      var oldRequest = null
      this.appNav.setCurrentTab = function (element) {
         if (this.currentTab) {
            this.currentTab.removeClass("selected");
            oldHref = this.currentTab.attr("href")
         }
         if (element) {
            if (element !== this.currentTab) {
               EW.sidebarButton.text(element.text());
               if (element.attr("data-ew-nav") && element.attr("href") != oldHref) {
                  //alert(element.prop("href"));
                  $("#action-bar-items").find("button,div").remove();
                  $("#main-content").empty();
                  //EW.lock($("#main-content"), "");
                  if (oldRequest)
                     oldRequest.abort();
                  oldRequest = $.post(element.prop("href"), function (data) {
                     //$("#action-bar-items").find("button").remove();
                     //EW.unlock($("#main-content"));
                     $("#action-bar-items").find("button,div").remove();
                     $("#main-content").html(data);
                  });
               }
            }
         }
         element.addClass("selected");
         this.currentTab = element;
      };
      //var base = this;
      if ($("#app-bar-nav").length == 0) {
         //sbb.hide();
      }
      if ($("#components-list").length == 1) {
         //$("#component-chooser-btn").remove();
      }
      $("#app-bar-nav a, .app-bar-nav a").each(function () {
         var a = $(this);
         if (a.attr("rel") === "ajax") {
            a.click(function (event) {
               event.preventDefault();
               if (a.attr("data-ew-nav")) {
                  System.setHashParameters({
                     "app": System.getHashNav("app")[0] + '/' + a.attr("data-ew-nav")
                  }, null);
               } else {
                  var kv = a.attr("href").split("=");
                  EW.setHashParameter(kv[0], kv[1]);
               }
            });
            var currentNav = System.getHashNav("app")[1];

            if (window.location.hash.indexOf(a.attr("href")) != -1 || currentNav === a.attr("data-ew-nav")) {
               //System.hashChanged();
               //base.setCurrentTab(a);
            }
            //alert(currentNav);
            if (a.attr("data-default") && !currentNav) {
               if (System.getHashNav("app")[1] !== a.attr("data-ew-nav")) {
                  System.setHashParameters({
                     app: System.getHashNav("app")[0] + '/' + a.attr("data-ew-nav")
                  }, true);
               }
            }
            /*var defaultLink = EW.getHashParameter(kv[0]);
             if (window.location.hash.indexOf(a.attr("href")) != -1 || defaultLink === kv[1])
             {
             base.setCurrentTab(a);
             }*/
         }
      });
      // Init nav bar handler
      /*EW.addURLHandler(function ()
       {
       if (EW.getHashParameter("nav"))
       {
       base.setCurrentTab($("a[data-ew-nav='" + EW.getHashParameter("nav") + "']"));
       }
       });*/
   }

   // Plugins which initilize when document is ready
   //var EW = null;
   $(document).ready(function ()
   {
      var hashDetection = new hashHandler();
      EW.activities = <?php echo EWCore::read_activities(); ?>;
      console.log(EW.activities);
      EW.oldApp = null;

      // Init EW plugins
      initPlugins(document);
      EW.initSideBar() & EW.loadSections();
      var currentButton = null;
      var buttons = null;
      var currentBtnForm = null;
      $(document).ajaxStart(function (event, data)
      {
         if (event.target.activeElement)
         {
         }
      });
      $(document).ajaxComplete(function (event, data)
      {
      });
      // Notify error if an ajax request fail
      $(document).ajaxError(function (event, data, status)
      {
         // Added to ignore aborted request and don't show them as a error
         if (data && data.statusText === "abort")
            return;
         if (EW.customAjaxErrorHandler)
         {
            EW.customAjaxErrorHandler = false;
            return;
         }
         //console.log(data.responseJSON);
         try
         {
            //data.responseJSON = $.parseJSON(data.responseJSON);
            //alert(data.responseJSON);
            var rl = '<ul>';
            $.each(data.responseJSON.reason, function (current, i)
            {
               rl += '<li><h4>' + current + '</h4><p>' + i.join() + '</p></li>';
            });
            rl += '</ul>';
         } catch (e)
         {
            console.log("ajaxError:");
            console.log(e);
            console.log(data);
         }
         $("body").EW().notify({
            "message": {
               html: (!data.responseJSON) ? "---ERROR---" : data.responseJSON.message + rl
            },
            status: "error",
            position: "n",
            delay: "stay"
         }).show();
         // this code is buggy
         //EW.unlock($(".glass-pane-lock").parent());
      });
      $('select').selectpicker({
         container: "body"
      });
      //$("#sidebar").hide();

      //$("#sidebar").prepend(sbb);

      document.addEventListener("DOMNodeInserted", function (event)
      {
         if (event.target)
         {
            initPlugins(event.target);
         }

         $(".nav.xs-nav-tabs").data("xs-nav-bar-active", function (e) {
            if ($(e).hasClass("xs-nav-tabs-active") || $(e).data("nav-xs-btn"))
               return;
            var nav = $(e);
            // Show default nav style when the window is wide enough
            $(window).one("ew.screen.sm ew.screen.md ew.screen.lg", function ()
            {
               if (nav && nav.hasClass("xs-nav-tabs-active"))
               {
                  nav.unbind('mouseenter mouseleave')
                  nav.data("button").after(nav.data("menu"));
                  nav.data("menu").show();
                  nav.data("button").remove();
                  nav.attr("class", $(e).data("oldClass"));
                  nav.find(".dropdown").remove();
                  nav.css({
                     top: ""
                  });
                  nav.data("nav-xs-btn", null);
               }
            });
            nav.data("oldClass", nav.attr("class"));
            nav.data("nav-xs-btn", true);
            nav.data("menu", nav);
            //alert(nav.html());
            $(e).prop("class", "nav nav-pills xs-nav-tabs-active nav-stacked dropdown col-xs-10");
            //nav.hide();
            $(e).data("element-id", $(e).attr("id"));
            var xsNavbar = $("<ul class='nav nav-pills'><li class='dropdown'><a id='tabs-btn' data-toggle='tab' href='#'></a></li></ul>");
            xsNavbar.data("nav-xs-btn", true);
            nav.before(xsNavbar);
            nav.data("button", xsNavbar);
            var dropdownNavBtn = $("<li class='dropdown'><a id='tabs-btn' data-toggle='tab' href='#'></a></li>")
            nav.prepend(dropdownNavBtn);
            var xsNavBarBtn = xsNavbar.find("li");
            nav.css({
               top: xsNavBarBtn.offset().top
            });
            nav.hide();
            xsNavBarBtn.hover(function () {
               nav.show();
               nav = nav.detach();
               $("body").append(nav);
            });
            nav.hover(function (e)
            {
               nav.stop().animate({
                  className: "nav nav-pills xs-nav-tabs-active nav-stacked dropdown in"
               },
                       300, "Power3.easeOut");
               e.preventDefault();
            },
                    function ()
                    {
                       nav.stop().animate({
                          className: "nav nav-pills xs-nav-tabs-active nav-stacked dropdown"
                       },
                               300, "Power3.easeOut", function () {
                                  nav = nav.detach();
                               });
                    });
         });
         if ($(window).width() < 768)
         {
            $(window).trigger("ew.screen.xs");
         }
      });
   });
   $(window).on("ew.screen.xs", function ()
   {
      $(".nav.xs-nav-tabs:not(.xs-nav-tabs-active)").each(function (i) {
         $(this).data("xs-nav-bar-active")(this);
      });
   });
</script>

<script src="~admin-public/js/lib/bootstrap.js" ></script>
<script src="~admin-public/js/ContentStrike/content-tools.js"></script>