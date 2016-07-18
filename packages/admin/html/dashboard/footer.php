<script>
  window.addEventListener('load', function () {

    var states = {};
    System.services.app_service = {
      on_load: function (app) {
        if (!app)
          return;

        states.loading_app = true;
//        System.ui.components.appTitle.text(app.title);
        if (EW.selectedApp) {
          System.ui.utility.removeClass(EW.selectedApp, "selected");
        }

        EW.selectedApp = $(".apps-menu-link[data-app='" + app.id + "']").addClass("selected")[0];

        System.entity('ui/app-bar').isLoading = true;
        if (EW.selectedSection) {
          System.ui.utility.addClass(EW.selectedSection, "inline-loader");
        }

        $("#action-bar-items").empty();
        System.entity('ui/main-content').show = false;
      },
      on_loaded: function (app, html) {
        $("#app-content").append(html);

        if (app.type === "app"/* && app.id === "system/" + System.getHashParam("app")*/) {
          EW.currentAppSections = System.modules[app.id].data.sections;
          EW.hoverApp = app.id;

          System.ui.components.sectionsMenuList[0].data = EW.currentAppSections;

          app.start();
        }

        states.loading_app = false;
      },
      load: function (path, app) {
        if (!app || app === "Home") {
          app = 'content-management';
        }

        System.entity('ui/app-bar').currentState = path.join('/');
        System.entity('ui/app-bar').currentApp = path[0];
        System.entity('ui/app-bar').currentSection = path[1];
        System.entity('ui/app-bar').currentSubSection = path[2];

        if (app !== EW.oldApp) {
          EW.oldApp = app;

          System.services.app_service.on_load(EW.apps[app]);

          System.loadModule(EW.apps[app], function (module) {
            System.services.app_service.on_loaded(module, module.html);
          });
          return;
        }
      },
      load_section: function (moduleId) {
        var element = System.ui.components.sectionsMenuList[0].links[EW.oldApp + "/" + moduleId];
        System.ui.behaviors.highlightAppSection(element.dataset.index, element);
        //System.UI.components.sectionsMenuList[0].value = element.dataset.index;

        if (element) {
          var moduleConfig = System.ui.components.sectionsMenuList[0].data[element.dataset.index];
          if (!moduleConfig/* || sectionData.id === EW.oldSectionId*/) {
            return;
          }

          EW.oldSectionId = moduleConfig.id;
          System.entity('ui/app-bar').sectionsMenuTitle = moduleConfig.title;
          System.entity('ui/app-bar').isLoading = true;
          System.ui.utility.addClass(element, "inline-loader");

          System.entity('ui/app-bar').subSections = [];

          $("#action-bar-items").find("button,div").remove();
          System.ui.components.appMainActions.empty();
          

          System.ui.components.mainContent.empty();
          System.abortAllRequests();

          System.loadModule(moduleConfig, function (module, html) {
            $("#action-bar-items").find("button,div").remove();

            if (!System.getHashNav("app")[0]) {
              return;
            }
            
            System.ui.components.mainFloatMenu[0].clean();

            System.entity('ui/app-bar').subSections = module.data.subSections || [];

            System.entity('ui/main-content').show = false;
            System.ui.components.mainContent.html(html);
            module.start();

            if (anim) {
              anim.pause();
            }

            if (System.ui.components.mainFloatMenu.children().length > 0) {
              System.ui.components.mainFloatMenu[0].on();
            } else {
              System.ui.components.mainFloatMenu[0].off();
            }

            System.entity('ui/app-bar').isLoading = false;
            System.ui.utility.removeClass(element, "inline-loader");
            Vue.nextTick(function () {
              System.entity('ui/main-content').show = true;
            });
          });
        }
      },
      load_tab: function (module) {
        System.entity('ui/app-bar').isLoading = true;
        $("#action-bar-items").find("button,div").remove();
        System.ui.components.appMainActions.empty();
        System.ui.components.mainFloatMenu[0].clean();

        System.ui.components.mainContent.empty();
        System.abortAllRequests();

        System.loadModule(module, tabLoaded);

        function tabLoaded(module, html) {
          $("#action-bar-items").find("button,div").remove();

          if (!System.getHashNav('app')[0]) {
            return;
          }

          System.entity('ui/main-content').show = false;
          System.ui.components.mainContent.html(html);

          if (typeof module.start === 'function') {
            module.start();
          }

          System.entity('ui/app-bar').isLoading = false;
          Vue.nextTick(function () {
            System.entity('ui/main-content').show = true;
          });
        }
      }
    };

    System.ui.behaviors.selectElementOnly = function (element, oldElement, styleClass) {
      if ('string' !== typeof styleClass) {
        styleClass = 'selected';
      }

      if (oldElement) {
        System.ui.utility.removeClass(oldElement, "selected");
      }

      System.ui.utility.addClass(element, "selected");
      return element;
    };

    System.ui.behaviors.selectAppSection = function (component, full, section) {
      if (!section) {
        System.UI.components.sectionsMenuList[0].value = '0';
        return;
      }

      if (component.data.tab === section) {
        return;
      }

      component.data.tab = section;
      System.services.app_service.load_section(section);
    };

    System.ui.behaviors.selectSubSection = function (component, full, tab) {
      if (!tab) {
        if (component.data.activeSubSection) {
          var appBar = System.entity('ui/app-bar');
          appBar.goToState(appBar.currentApp + '/' + appBar.currentSection + '/' + component.data.activeSubSection);
        }

        return;
      }

      component.data.activeSubSection = tab;
      System.entity('ui/app-bar').currentSubSection = tab;

      var subSectionConfig = System.entity('ui/app-bar').subSections.filter(function (item) {
        return item.state === tab;
      })[0];

      System.services.app_service.load_tab(subSectionConfig);
    };

    var anim = false;

    EverythingWidgets.prototype.readApps = function () {
      var _this = this;
      this.apps = {};

      $.get('~admin/api/EWCore/read_apps_sections', {
        appDir: "admin"
      }, function (data) {

        var items = [
          '<ul class="apps-menu-list">'
        ];
        $.each(data, function (key, val) {

          items.push('<li class=""><a class="apps-menu-link" data-app="'
                  + val['id'] + '"><span class="">'
                  + val['title'] + '</span></a></li>');
          val.file = "index.php";
          val.id = val['id'];
          _this.apps[val['id']] = val;

        });

        items.push('</ul>');

        $(items.join('')).appendTo("#apps-menu");

        System.start();

        $.each(_this.apps, function (e, v) {
          if (v.id !== EW.oldApp) {
            System.loadModule(v, function () {
            });
          }
        });
      }, "json");
    };

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
        }, dur || 300, "Power2.easeInOut");
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
            $.post("<?php echo EW_DIR ?>~admin/html/content-management/link-chooser/component.php", {
              callback: settings.callbackName,
              data: $element.val(),
              contentType: $element.data("content-type") || "all"
            }, function (data) {
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
          image.attr("src", $element.val() || "html/admin/content-management/media/no-image.png");
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
          //console.log(image);
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

          System.loadModule({
            //$.post("~admin/html/content-management/link-chooser-media.php", {
            id: "forms/media-chooser",
            url: "~admin/html/content-management/link-chooser/link-chooser-media.php",
            params: {
              callback: settings.callbackName
            }
          }, function (module) {
            //imageChooserDialog.find(".form-content:first").append(data);
            //imageChooserDialog.prepend("<div class='header-pane row'><h1 class='form-title'>Media</h1></div>");
            imageChooserDialog.html(module.html);
            imageChooserDialog[0].selectMedia = function (image) {
              $element.val(image.src).change();
              imageChooserDialog.dispose();
            };
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
    //$(document).ready(function () {
    var mouseInNavMenu = false,
            enterOnLink = false,
            currentSectionIndex = null;

    System.ui.body = $("body")[0];
    System.ui.components = {
      homeButton: $("#apps"),
      appTitle: $("#app-title"),
      appBar: $("#app-bar"),
      homePane: $("#home-pane"),
      appMainActions: $("#app-main-actions"),
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

    System.ui.behaviors.highlightAppSection = function (index, section) {
      currentSectionIndex = index;

      if (EW.selectedSection) {
        System.ui.utility.removeClass(EW.selectedSection, "selected");
      }

      EW.selectedSection = section;
      System.ui.utility.addClass(EW.selectedSection, "selected");
    };

    System.ui.behaviors.selectTab = function (tabHref, tabsContainer) {
      tabsContainer.find('a[href="' + tabHref + '"]').tab('show');
    };

    System.ui.components.sectionsMenuList[0].onSetData = function (data) {
      if (data.length) {
        if (mouseInNavMenu) {
          TweenLite.to(System.ui.components.sectionsMenu[0], .3, {
            className: "sections-menu in",
            ease: "Power2.easeInOut"
          });
        }
      } else {
        //alert(System.UI.components.sectionsMenu.height());
        System.ui.components.sectionsMenu.css("height", System.ui.components.sectionsMenu.height());
        TweenLite.to(System.ui.components.sectionsMenu[0], .2, {
          className: "sections-menu out",
          height: "94px",
          ease: "Power2.easeInOut",
          onComplete: function () {
            System.ui.components.sectionsMenu.css("height", "");
          }
        });
      }
    };

    System.ui.components.sectionsMenuList[0].addEventListener('item-selected', function (event) {
      if (event.detail.data.id === appBarVue.currentApp + '/' + appBarVue.currentSection) {
        return;
      }

      System.setHashParameters({
        app: event.detail.data.id
      });
    });

    System.ui.components.navigationMenu.on("mouseenter touchstart", function (e) {
      if (mouseInNavMenu)
        return;

      mouseInNavMenu = true;
      System.ui.components.navigationMenu.addClass("expand");

      if (System.ui.components.sectionsMenuList[0].data.length) {
        if (!enterOnLink) {
          System.ui.components.sectionsMenu[0].style.top = System.ui.components.appsMenu.find(".apps-menu-link.selected")[0].getBoundingClientRect().top + "px";
        }

        TweenLite.to(System.ui.components.sectionsMenu[0], .3, {
          className: "sections-menu in",
          ease: "Power2.easeInOut"
        });
      }
    });

    var moveAnim = null;

    System.ui.components.appsMenu.on('mouseenter touchstart', "a", function (e) {
      var app = e.currentTarget.dataset.app;
      EW.hoverApp = "system/" + app;

      var sections = System.modules["system/" + app] ? System.modules["system/" + app].data.sections : [];

      if (System.ui.components.sectionsMenuList[0].data !== sections) {
        System.ui.components.sectionsMenuList[0].data = sections;
      }

      if (EW.oldApp === app) {           
        System.ui.behaviors.highlightAppSection(currentSectionIndex, System.ui.components.sectionsMenuList[0].links[currentSectionIndex]);
      }

      if (!mouseInNavMenu) {
        System.ui.components.sectionsMenu[0].style.top = e.currentTarget.getBoundingClientRect().top + "px";
        enterOnLink = true;
        return;
      }

      moveAnim = TweenLite.to(System.ui.components.sectionsMenu[0], .2, {
        top: e.currentTarget.getBoundingClientRect().top
      });
    });

    System.ui.components.navigationMenu.on('click', function (event) {
      if (event.target === System.ui.components.navigationMenu[0]) {
        System.ui.components.navigationMenu.removeClass("expand");
        TweenLite.to(System.ui.components.sectionsMenu[0], .2, {
          className: "sections-menu",
          marginTop: 0,
          ease: "Power2.easeInOut",
          onComplete: function () {
            if (!states.loading_app && currentSectionIndex !== System.ui.components.sectionsMenuList[0].value) {
              System.ui.components.sectionsMenuList[0].data = EW.currentAppSections;
              System.ui.behaviors.highlightAppSection(currentSectionIndex, EW.selectedSection);
            }
          }
        });
      }
    });

    System.ui.components.navigationMenu.on('mouseleave', function () {
      mouseInNavMenu = false;
      enterOnLink = false;

      System.ui.components.navigationMenu.removeClass("expand");
      TweenLite.to(System.ui.components.sectionsMenu[0], .2, {
        className: "sections-menu",
        marginTop: 0,
        ease: "Power2.easeInOut",
        onComplete: function () {
          if (!states.loading_app && currentSectionIndex !== System.ui.components.sectionsMenuList[0].value) {
            System.ui.components.sectionsMenuList[0].data = EW.currentAppSections;
            System.ui.behaviors.highlightAppSection(currentSectionIndex, EW.selectedSection);
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
    //console.log(installModules);
    installModules.forEach(function (e) {
      EW.apps[e.id] = e;
    });

    var appsVue = new Vue({
      el: '#apps-menu',
      data: {
        apps: installModules
      }
    });

    var appBarVue = new Vue({
      el: '#app-bar',
      data: {
        sectionsMenuTitle: '',
        isLoading: false,
        subSections: null,
        currentState: null,
        currentApp: null,
        currentSection: null,
        currentSubSection: null
      },
      computed: {
        styleClass: function () {
          var classes = [];

          if (this.subSections && this.subSections.length) {
            classes.push('tabs-bar-on');
          }

          return classes.join(' ');
        }
      },
      methods: {
        goTo: function (tab, $event) {
          $event.preventDefault();

          System.app.setNav(this.currentApp + '/' + this.currentSection + '/' + tab.state);
        },
        goToState: function (state) {
          System.app.setNav(state);
        }
      }
    });

    System.entity('ui/app-bar', appBarVue);

    var mainContentVue = new Vue({
      el: '#main-content',
      data: {
        show: false
      },
      computed: {
        styleClass: function () {
          var classes = [];

          if (appBarVue.subSections && appBarVue.subSections.length) {
            classes.push('tabs-bar-on');
          }

          return classes.join(' ');
        }
      }
    });

    System.entity('ui/main-content', mainContentVue);

    $.each(installModules, function (key, val) {
      val.file = "index.php";
      val.id = val['id'];
      EW.apps[val['id']] = val;
    });

    System.init(installModules);
    System.app.on('app', System.services.app_service.load);
    System.start();

    appBarVue.selectedTab = System.getHashParam('app');

    if (!System.getHashParam('app')) {
      System.setHashParameters({
        app: "content-management"
      }, true);
    }

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
        console.log("ajaxError: ", e);
        console.log(data);
        console.log(e.stack);
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

    // select the target node
//    var target = document.getElementById('some-id');
//
// create an observer instance
    var observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        if (mutation.type === 'childList' && mutation.addedNodes.length) {
          initPlugins(mutation.addedNodes[0]);
        }
      });
    });

// pass in the target node, as well as the observer options
    observer.observe(document.body, {
      attributes: false,
      childList: true,
      characterData: false
    });

//    document.addEventListener("DOMNodeInserted", function (event) {
//    if(event.target){
//      initPlugins(event.target);  
//    }
//    
//    });
    //});

//    $(window).on("ew.screen.xs", function () {
//      $(".nav.xs-nav-tabs:not(.xs-nav-tabs-active)").each(function (i) {
//        $(this).data("xs-nav-bar-active")(this);
//      });
//    });
  });
</script>