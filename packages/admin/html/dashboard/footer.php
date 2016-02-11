<script>
  System.goToHomeApp = function () {
    System.abortAllRequests();
    $("#action-bar-items").children().animate({
      opacity: 0
    },
      300);

    System.UI.components.appTitle.text("Home");
    //System.UI.components.homeButton.stop().comeOut(500);


    /*$("#main-content").stop().animate({
     //transform: "scale(1.14)",
     top: "-=94px",
     opacity: 0
     }, 500, "Power2.easeInOut", function () {
     this.remove();
     });*/

    /*$("#app-bar-nav").stop().animate({opacity: 0}, 300, function () {
     this.remove();
     
     });*/

    System.UI.components.appBar.animate({
      className: "app-bar"
    },
      500, "Power2.easeInOut");
    System.UI.components.homePane.animate({
      className: "home-pane in"
    },
      500, "Power2.easeInOut");
  };

  System.onLoadApp = function (app) {

    System.UI.components.appTitle.text(app.title);
    if (EW.selectedApp) {
      UIUtil.removeCSSClass(EW.selectedApp, "selected");
    }

    EW.selectedApp = $(".apps-menu-link[data-app='" + app.id + "']").addClass("selected")[0];

    System.UI.components.sectionsMenuTitle.addClass("inline-loader");
    if (EW.selectedSection)
      UIUtil.addCSSClass(EW.selectedSection, "inline-loader");

    $("#action-bar-items").empty();
    $("#main-content").remove();
    //return true;
  };

  System.onAppLoaded = function (app, response) {
    //setTimeout(function () {
    // if user immidietly returned to home, then stop here
    /*if (System.getHashNav("app")[0] === "Home") {
     return;
     }*/

    $("#app-content").append(response);
    System.UI.components.mainContent = $("#main-content");

    //EW.initSideBar();

    if (app.type === "app"/* && app.id === "system/" + System.getHashParam("app")*/) {
      EW.currentAppSections = System.modules[app.id].data.sections;
      EW.hoverApp = app.id;

      System.UI.components.sectionsMenuList[0].setAttribute("data", EW.currentAppSections);
      app.start();
    }
    //}, 100);
  };



  var anim = false;
  EverythingWidgets.prototype.loadSection = function (sectionId) {
    
    //console.log(System.UI.components.sectionsMenuList[0].xtag);
    var element = System.UI.components.sectionsMenuList[0].links[EW.oldApp + "/" + sectionId];
    
    System.UI.components.sectionsMenuList[0].value = element.dataset.index;
    //alert(EW.oldApp + "/" + sectionId)
    if (element) {
      //alert(EW.oldApp+"/"+sectionId)
      var sectionData = System.UI.components.sectionsMenuList[0].data[element.dataset.index];
      if (!sectionData/* || sectionData.id === EW.oldSectionId*/)
        return;
      EW.oldSectionId = sectionData.id;
      System.UI.components.sectionsMenuTitle.text(sectionData.title);
      System.UI.components.sectionsMenuTitle.addClass("inline-loader");
      UIUtil.addCSSClass(element, "inline-loader");

      $("#action-bar-items").find("button,div").remove();
      System.UI.components.mainFloatMenu[0].clean();
      System.UI.components.mainFloatMenu[0].contract();
      
      

      System.UI.components.mainContent.empty();
      System.abortAllRequests();

      System.loadModule(sectionData, function (mod, data) {
        $("#action-bar-items").find("button,div").remove();

        if (!System.getHashNav("app")[0]) {
          return;
        }

        System.UI.components.mainContent.css("opacity", 0);
        System.UI.components.mainContent.html(data);
        mod.start();
        if (anim) {
          anim.pause();
        }
        //alert("section loaded: " + mod.id);
        //System.startLastLoadedModule();
        
        if (System.UI.components.mainFloatMenu.children().length > 0) {
          System.UI.components.mainFloatMenu[0].on();
        } else {
          System.UI.components.mainFloatMenu[0].off();
        }

        System.UI.components.sectionsMenuTitle.removeClass("inline-loader");
        UIUtil.removeCSSClass(element, "inline-loader");

        anim = TweenLite.fromTo(System.UI.components.mainContent[0], .5, {
          opacity: 0,
          ease: "Power2.easeInOut",
          top: "-=94px"
        },
          {
            top: "+=94px",
            opacity: 1,
            onComplete: function () {
            }
          });
      });
    }

  };

  EverythingWidgets.prototype.readApps = function () {
    var _this = this;
    this.apps = {/*Home: {id: "Home"}*/
    };
    
    $.get('~admin/api/EWCore/read_apps_sections', {
      appDir: "admin"
    },
      function (data) {

        var items = [
          '<ul class="apps-menu-list">'
        ];
        $.each(data, function (key, val) {
          /*items.push('<li class=""><a class="app-link z-index-0" data-app="'
           + val['id'] + '"><label>'
           + val['title'] + '</label><p>'
           + val['description'] + '</p></a></li>');*/

          items.push('<li class=""><a class="apps-menu-link" data-app="'
            + val['id'] + '"><span class="">'
            + val['title'] + '</span></a></li>');
          //val.package = "~admin";
          val.file = "index.php";
          val.id = val['id'];
          _this.apps[val['id']] = val;

        });

        items.push('</ul>');

        $(items.join('')).appendTo("#apps-menu");

        System.start();

        $.each(_this.apps, function (e, v) {
          if (v.id !== EW.oldApp) {
            //alert(System.getHashParam("app") + " >>> " + v.id + " @ " + EW.oldApp);
            System.loadModule(v, function () {
              //alert("sdfsdfsdfsdf")
            });
          }
        });


        var $oldAppLink = $();
        /*$("#navigation-menu .apps-menu-link").click(function (event) {
         event.preventDefault();
         $oldAppLink.removeClass("selected");
         $oldAppLink = $(this);
         $oldAppLink.addClass("selected");
         
         System.setHashParameters({
         app: $oldAppLink.attr("data-app")
         }, null);
         
         });*/

      }, "json");
  };

  /*EverythingWidgets.prototype.loadApp = function (data) {
   
   if (data.app !== this.oldApp) {
   this.oldApp = data.app;
   
   if (!data.app) {
   System.UI.components.appBar.animate({className: "app-bar"}, 500, "Power2.easeOut");
   System.UI.components.homePane.animate({className: "home-pane in"}, 500, "Power2.easeOut");
   return;
   }
   
   $("#action-bar-items").empty();
   System.UI.components.appBar.animate({className: "app-bar in"}, 500, "Power2.easeOut");
   System.UI.components.homePane.animate({className: "home-pane"}, 500, "Power2.easeOut");
   
   setTimeout(function () {
   $.post("~admin/api/" + data.app + "/index.php", {}, function (response) {
   $("#app-content").append(response);
   EW.initSideBar();
   });
   }, 500);
   }
   };*/

  $.fn.textWidth = function () {
    var html_org = $(this).html();
    var html_calc = '<span style="white-space:nowrap">' + html_org + '</span>';
    $(this).html(html_calc);
    var width = $(this).find('span:first').width();
    $(this).html(html_org);
    return width;
  };

  $.fn.comeIn = function (dur) {

    if (!this.is(":visible") || this.css("visibility") !== "visible") {
      var orgClass = "";
      this.stop(true, true);

      if (this.prop("class")) {
        orgClass = this.prop("class").replace('btn-hide', '');
      }

      this.addClass("btn-hide").css({
        display: ""
      });

      this.animate({
        className: orgClass
      },
        dur || 300, "Power2.easeInOut");
    }

    return this;
  };

  $.fn.comeOut = function (dur) {

    if (!this.hasClass("btn-hide")) {
      this.stop(true, true).animate({
        className: this.prop("class") + " btn-hide"
      },
        dur || 300, "Power2.easeInOut", function () {
          this.hide();
        });
    }

    return this;
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
      //var base = this;
      var $element = $(element);
      defaults.callback = function (link) {
        $element.val(link).change();
        linkChooserDialog.trigger("close");
      };
      //this.$element = $(element);
      var settings = $.extend({
      }, defaults, options);
      //$element.EW().putInWrapper();
      //var wrapper = this.$element.parent();
      if (linkChooserDialog)
        linkChooserDialog.remove();
      $element.EW().inputButton({
        title: '<i class="link-icon"></i>',
        label: 'tr{Link Chooser}',
        class: "btn-default",
        onClick: function (e) {
          linkChooserDialog = EW.createModal({
            class: "center slim"
          });
          $.post("<?php echo EW_DIR ?>~admin/html/content-management/file-chooser.php", {
            callback: settings.callbackName,
            data: $element.val(),
            contentType: $element.data("content-type") || "all"
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
        $element.wrap('<div class="element-wrapper" style="position:relative;padding-bottom:30px;" data-element-wrapper="true"><div style="padding:5px 0;border:2px dashed #aaa;background-color:#fff;display:block;overflow:hidden;" data-element-wrapper="true"></div></div>');
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
          maxWidth: "720px",
          display: "block",
          float: "",
          margin: "2px auto 2px auto"
        });

        imageChooserBtn = $("<button type='button' class='btn btn-xs btn-link btn-link-chooser' >Choose Image</button>");
        imageChooserBtn.css({
          position: "absolute",
          right: "2px",
          bottom: "2px"
        });
        wrapper.append(imageChooserBtn);
        $element.attr(ACTIVE_PLUGIN_ATTR, true);
      }

      imageChooserBtn.click(function () {
        imageChooserDialog = EW.createModal({
          autoOpen: false,
          class: "center"
        });
        imageChooserDialog.append("<div class='form-content'></div><div class='footer-pane row actions-bar action-bar-items' ></div>");
        $.post("<?php echo EW_DIR ?>~admin/html/content-management/Media.php", {
          callback: settings.callbackName
        },
          function (data) {
            imageChooserDialog.find(".form-content:first").append(data);
            imageChooserDialog.prepend("<h1>Media</h1>");
            var bSelectPhoto = EW.addAction("Select Photo", function () {
              EW.setHashParameter("select-photo", true, "media");
            }, {
              display: "none"
            }).addClass("btn-success");
            // create handler to track selected
            var EWhandler = function () {
              var url = EW.getHashParameter("absUrl", "media");
              if (url) {
                bSelectPhoto.comeIn(300);
              } else {
                bSelectPhoto.comeOut(200);
              }

              if (EW.getHashParameter("select-photo", "media")) {
                EW.setHashParameter("select-photo", null, "media");
                imageChooserDialog.dispose();
                //if (EW.getHashParameter("url", "Media"))
                $element.val(EW.getHashParameter("absUrl", "media")).change();
                $element.attr("data-filename", EW.getHashParameter("filename", "media"));
                $element.attr("data-file-extension", EW.getHashParameter("fileExtension", "media"));
                $element.attr("data-url", EW.getHashParameter("url", "media"));
              }
            };
            EW.addURLHandler(EWhandler, "media.ImageChooser");
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

    if (!element.innerHTML && element.nodeName.toLowerCase() !== 'input' && element.nodeName.toLowerCase() !== 'textarea') {
      return;
    }

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



  // Plugins which initilize when document is ready
  //var EW = null;
  $(document).ready(function () {
    System.UI.body = $("body")[0];
    System.UI.components = {
      homeButton: $("#apps"),
      appTitle: $("#app-title"),
      appBar: $("#app-bar"),
      homePane: $("#home-pane"),
      mainContent: $("#main-content"),
      body: $("body"),
      document: $(document),
      navigationMenu: $("#navigation-menu"),
      appsMenu: $("#apps-menu"),
      sectionsMenu: $("#sections-menu"),
      sectionsMenuList: $("#sections-menu-list"),
      sectionsMenuTitle: $("#sections-menu-title"),
      mainFloatMenu: $("#main-float-menu")
    };

    var mouseInNavMenu = false,
      enterOnLink = false,
      currentSectionIndex = null;


    System.UI.components.sectionsMenuList[0].onSetData = function (data) {
      if (data.length) {
        if (mouseInNavMenu) {
          TweenLite.to(System.UI.components.sectionsMenu[0], .3, {
            className: "sections-menu in",
            ease: "Power2.easeInOut"
          });
        }
      } else {
        //alert(System.UI.components.sectionsMenu.height());
        System.UI.components.sectionsMenu.css("height", System.UI.components.sectionsMenu.height());
        TweenLite.to(System.UI.components.sectionsMenu[0], .2, {
          className: "sections-menu out",
          height: "94px",
          ease: "Power2.easeInOut",
          onComplete: function () {
            System.UI.components.sectionsMenu.css("height", "");
          }
        });
      }
    };


    System.UI.components.sectionsMenuList[0].onItemSelected = function (item, index, element) {
      currentSectionIndex = index;

      if (EW.selectedSection) {
        UIUtil.removeCSSClass(EW.selectedSection, "selected");
      }

      EW.selectedSection = element;
      UIUtil.addCSSClass(EW.selectedSection, "selected");
      System.setHashParameters({
        app: item.id
      },
        true);

    };

    System.UI.components.navigationMenu.on("mouseenter", function (e) {
      if (mouseInNavMenu)
        return;

      mouseInNavMenu = true;
      System.UI.components.navigationMenu.addClass("expand");

      if (System.UI.components.sectionsMenuList[0].data.length) {
        if (!enterOnLink)
          System.UI.components.sectionsMenu[0].style.top = System.UI.components.appsMenu.find(".apps-menu-link.selected")[0].getBoundingClientRect().top + "px";

        TweenLite.to(System.UI.components.sectionsMenu[0], .3, {
          className: "sections-menu in",
          ease: "Power2.easeInOut"
        });
      }
    });

    var moveAnim = null;

    System.UI.components.appsMenu.on("mouseenter", "a", function (e) {
      EW.hoverApp = "system/" + e.target.dataset.app;

      var sections = System.modules["system/" + e.target.dataset.app] ? System.modules["system/" + e.target.dataset.app].data.sections : [
      ];
      System.UI.components.sectionsMenuList[0].setAttribute("data", sections);

      if (EW.oldApp === e.target.dataset.app) {
        System.UI.components.sectionsMenuList[0].value = currentSectionIndex;
      }

      if (!mouseInNavMenu) {
        System.UI.components.sectionsMenu[0].style.top = e.target.getBoundingClientRect().top + "px";
        enterOnLink = true;
        return;
      }

      moveAnim = TweenLite.to(System.UI.components.sectionsMenu[0], .2, {
        top: e.target.getBoundingClientRect().top
      });
    });

    System.UI.components.navigationMenu.on("mouseleave", function () {
      mouseInNavMenu = false;
      enterOnLink = false;

      System.UI.components.navigationMenu.removeClass("expand");

      TweenLite.to(System.UI.components.sectionsMenu[0], .2, {
        className: "sections-menu",
        marginTop: 0,
        ease: "Power2.easeInOut",
        onComplete: function () {
          if (!EW.loadingApp && currentSectionIndex !== System.UI.components.sectionsMenuList[0].value) {
            System.UI.components.sectionsMenuList[0].setAttribute("data", EW.currentAppSections);
            System.UI.components.sectionsMenuList[0].value = currentSectionIndex;
          }
        }
      });
    });



    var hashDetection = new hashHandler();
    EW.activities = <?php echo EWCore::read_activities(); ?>;
    EW.oldApp = null;
    EW.apps = {};

    // Init EW plugins
    initPlugins(document);

    var installModules = <?= EWCore::read_apps_sections(); ?>;
    installModules.forEach(function (e) {
      EW.apps[e.id] = e;
    });

    var items = [
      '<ul class="apps-menu-list">'
    ];
    $.each(installModules, function (key, val) {

      items.push('<li class=""><a class="apps-menu-link" data-app="'
        + val['id'] + '"><span class="">'
        + val['title'] + '</span></a></li>');

      val.file = "index.php";
      val.id = val['id'];
      EW.apps[val['id']] = val;

    });
    items.push('</ul>');
    $(items.join('')).appendTo("#apps-menu");

    System.init(installModules);

    System.app.hashHandler = function (nav, params) {
      if ((!nav["app"] || nav["app"][0] === "Home") && "content-management" !== EW.oldApp) {
        //EW.oldApp = "content-management";
        System.setHashParameters({
          app: "content-management"
        },
          true);
      }
    };

    System.on('app', function (path, app) {
      /*if (!app || app === "Home") {
       System.goToHomeApp();
       return;
       }*/

      //alert(app + " @ " + EW.oldApp)
      if (/*EW.apps[app] && */app !== EW.oldApp) {
        EW.oldApp = app;
        //alert(app)
        //System.UI.components.appTitle.text(EW.apps[app].title);
        //System.openApp(EW.apps[app]);

        // before load
        EW.loadingApp = true;
        System.onLoadApp(EW.apps[app]);

        System.loadModule(EW.apps[app], function (mod) {
          // after load

          //mod.start();
          System.onAppLoaded(mod, mod.html);
          //alert("aha -> " + mod.id);
          EW.loadingApp = false;
        });
        return;
      }

    });
    //alert("system start");
    System.start();
    //EW.readApps();

    $(document).ajaxStart(function (event, data) {
      if (event.target.activeElement) {
      }
    });

    $(document).ajaxComplete(function (event, data) {
    });
    // Notify error if an ajax request fail
    $(document).ajaxError(function (event, data, status) {
      // Added to ignore aborted request and don't show them as a error
      if (data && data.statusText === "abort")
        return;
      if (EW.customAjaxErrorHandler) {
        EW.customAjaxErrorHandler = false;
        return;
      }

      try
      {
        var errorsList = '<ul>';
        $.each(data.responseJSON.reason, function (current, i) {
          errorsList += '<li><h4>' + current + '</h4><p>' + i.join() + '</p></li>';
        });
        errorsList += '</ul>';
      } catch (e) {
        console.log("ajaxError:");
        console.log(e, status);
        console.log(data);
      }

      $("body").EW().notify({
        "message": {
          html: (!data.responseJSON) ? "---ERROR---" : data.responseJSON.message + errorsList
        },
        status: "error",
        position: "n",
        delay: "stay"
      }).show();
    });

    $('select').selectpicker({
      container: "body"
    });

    document.addEventListener("DOMNodeInserted", function (event) {
      if (event.target) {
        initPlugins(event.target);
      }

      $(".nav.xs-nav-tabs").data("xs-nav-bar-active", function (e) {
        if ($(e).hasClass("xs-nav-tabs-active") || $(e).data("nav-xs-btn")) {
          return;
        }

        var nav = $(e);
        // Show default nav style when the window is wide enough
        $(window).one("ew.screen.sm ew.screen.md ew.screen.lg", function () {
          if (nav && nav.hasClass("xs-nav-tabs-active")) {
            nav.unbind('mouseenter mouseleave');
            nav.data("button").after(nav.data("menu"));
            nav.data("menu").show();
            nav.data("button").remove();
            nav.attr("class", nav.data("oldClass"));
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
        nav.prop("class", "nav nav-pills xs-nav-tabs-active nav-stacked dropdown col-xs-10");
        nav.data("element-id", nav.attr("id"));
        var xsNavbar = $("<ul class='nav nav-pills'><li class='dropdown'><a id='tabs-btn' data-toggle='tab' href='#'></a></li></ul>");
        xsNavbar.data("nav-xs-btn", true);
        nav.before(xsNavbar);
        nav.data("button", xsNavbar);
        var dropdownNavBtn = $("<li class='dropdown'><a id='tabs-btn' data-toggle='tab' href='#'></a></li>");
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

        nav.hover(function (e) {
          nav.stop().animate({
            className: "nav nav-pills xs-nav-tabs-active nav-stacked dropdown in"
          },
            300, "Power3.easeOut");
          e.preventDefault();
        }, function () {
          nav.stop().animate({
            className: "nav nav-pills xs-nav-tabs-active nav-stacked dropdown"
          },
            300, "Power3.easeOut", function () {
              nav = nav.detach();
            });
        });
      });

      if ($(window).width() < 768) {
        $(window).trigger("ew.screen.xs");
      }
    });
  });

  $(window).on("ew.screen.xs", function () {
    $(".nav.xs-nav-tabs:not(.xs-nav-tabs-active)").each(function (i) {
      $(this).data("xs-nav-bar-active")(this);
    });
  });
</script>