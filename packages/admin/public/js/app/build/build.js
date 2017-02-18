/* global System, ew_plugins, EW_APPS, EW, EW_ACTIVITIES */

window.addEventListener('load', function () {
  System.entity('stage/init-ui-components').call();

  var appBarComponent = System.entity('ui/app-bar');
  var appsComponent = System.entity('ui/apps');

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
      }, dur || 300, "Power2.easeInOut", function () {
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
        $element.val(JSON.stringify(link || '{}')).change();
        linkChooserDialog.trigger("close");
      };
      //this.$element = $(element);
      var settings = $.extend({}, defaults, options);
      //$element.EW().putInWrapper();
      //var wrapper = this.$element.parent();
      if (linkChooserDialog)
        linkChooserDialog.remove();
      $element.EW().inputButton({
        title: '<i class="link-icon"></i>',
        label: 'tr{Link Chooser}',
        class: 'btn-default',
        onClick: function () {
          linkChooserDialog = EW.createModal({
            class: 'center slim'
          });
          System.loadModule({
            url: 'html/admin/content-management/link-chooser/component.php', params: {
              callback: settings.callbackName,
              data: $element.val(),
              contentType: $element.data("content-type") || "all"
            }
          }, function (module) {
            module.scope.onSelect = settings.callback;
            linkChooserDialog.html(module.html);
          });
        }
      });
    }

    return this.each(function () {
      if (!$.data(this, 'ew_plugin_link_chooser')) {
        $.data(this, 'ew_plugin_link_chooser', true);
        new LinkChooser(this, options);
      }
    });
  };

  ew_plugins.imageChooser = function (options) {
    var ACTIVE_PLUGIN_ATTR = 'data-active-plugin-image-chooser';
    var defaults = {
      callbackName: 'function-reference'
    };
    var imageChooserDialog;

    function ImageChooser(element, options) {
      var base = this;
      var $element = $(element);
      $element.off('change.image-chooser');
      $element.on('change.image-chooser', function () {
        image.attr('src', $element.val() || 'html/admin/content-management/media/no-image.png');
      });

      defaults.callback = function (link) {
        imageChooserDialog.dispose();
      };

      var settings = $.extend({}, defaults, options);
      if (!$element.parent().attr('data-element-wrapper'))
        $element.wrap('<div class="element-wrapper" style="position:relative;padding-bottom:30px;" data-element-wrapper="true"><div style="padding:5px 0;border:2px dashed #aaa;background-color:#fff;display:block;overflow:hidden;" data-element-wrapper="true"></div></div>');
      $element.attr('type', 'hidden');
      var wrapper = $element.parent().parent();
      if (imageChooserDialog)
        imageChooserDialog.remove();
      var image = wrapper.find('img');
      if (image.length <= 0) {
        image = $(document.createElement('img'));
        wrapper.find('div').append(image);
      }

      image.css("max-height", $element.css("max-height"));
      var imageChooserBtn;
      // if the plugin has been called later again on same element
      if ($element.attr(ACTIVE_PLUGIN_ATTR)) {
        imageChooserBtn = wrapper.find('.btn-image-chooser');
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

        imageChooserBtn = $("<button type='button' class='btn btn-xs btn-link btn-image-chooser'>Choose Image</button>");
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
          //$.post("html/admin/content-management/link-chooser-media.php", {
//          id: "content-management/media",
          url: "html/admin/content-management/link-chooser/link-chooser-media.php",
//          fresh: true,
          params: {
            callback: settings.callbackName
          },
          fresh: true
        }, function (module) {
          imageChooserDialog.html(module.html);
          module.scope.selectMedia = function (image) {
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
    if (!element.innerHTML && element.nodeName.toLowerCase() !== 'input' &&
        element.nodeName.toLowerCase() !== 'textarea') {
      return;
    }

    EW.initPlugins($(element));
  }

  var mouseInNavMenu = false,
      enterOnLink = false,
      currentSectionIndex = null;

  System.ui.body = $("body")[0];
  System.ui.components = {
    appMainActions: $("#app-main-actions"),
    mainContent: $("#main-content"),
    body: $("body"),
    document: $(document),
    navigationMenu: $("#navigation-menu"),
    appsMenu: $("#apps-menu"),
    sectionsMenu: $("#sections-menu"),
    sectionsMenuList: $("#sections-menu-list")
  };

  System.ui.behaviors.highlightAppSection = function (index, section) {
    currentSectionIndex = index;

    if (EW.selectedSection) {
      System.ui.utility.removeClass(EW.selectedSection, "selected");
    }

    EW.selectedSection = section;
    System.ui.utility.addClass(EW.selectedSection, "selected");
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
    if (event.detail.data.id === appBarComponent.currentApp + '/' + appBarComponent.currentSection) {
      return;
    }

    System.setHashParameters({
      app: event.detail.data.id
    });
  });

  System.ui.components.navigationMenu.on('mouseenter touchstart', function () {
    if (mouseInNavMenu)
      return;

    mouseInNavMenu = true;
    System.ui.components.navigationMenu.addClass('expand');
    if (System.ui.components.sectionsMenuList[0].data.length) {
      if (!enterOnLink) {
        System.ui.components.sectionsMenu[0].style.top = System.ui.components.appsMenu.find('.apps-menu-link.selected')[0].offsetTop + 'px';
      }

      System.ui.behaviors.highlightAppSection(currentSectionIndex, System.ui.components.sectionsMenuList[0].links[currentSectionIndex]);

      TweenLite.to(System.ui.components.sectionsMenu[0], .3, {
        className: 'sections-menu in',
        ease: 'Power2.easeInOut'
      });
    }
  });

  var moveAnim = null;

  System.ui.components.appsMenu.on('mouseenter touchstart', 'a', function (event) {
    var app = event.currentTarget.dataset.app;
    EW.hoverApp = 'system/' + app;

    var sections = System.modules['system/' + app] ? System.modules['system/' + app].data.sections : [];

    if (System.ui.components.sectionsMenuList[0].data !== sections) {
      System.ui.components.sectionsMenuList[0].data = sections;
    }

    if (EW.oldApp === app) {
      System.ui.behaviors.highlightAppSection(currentSectionIndex, System.ui.components.sectionsMenuList[0].links[currentSectionIndex]);
    }

    if (!mouseInNavMenu) {
      System.ui.components.sectionsMenu[0].style.top = event.currentTarget.offsetTop + 'px';
      enterOnLink = true;
      return;
    }

    moveAnim = TweenLite.to(System.ui.components.sectionsMenu[0], .2, {
      top: event.currentTarget.offsetTop
    });
  });

  System.ui.components.navigationMenu.on('click', function (event) {
    if (event.target === System.ui.components.navigationMenu[0]) {
      System.ui.components.navigationMenu.removeClass('expand');
      TweenLite.to(System.ui.components.sectionsMenu[0], .2, {
        className: 'sections-menu',
        ease: 'Power2.easeInOut',
        onComplete: function () {
          if (!System.services.app_service.loading_app && currentSectionIndex !== System.ui.components.sectionsMenuList[0].value) {
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

    System.ui.components.navigationMenu.removeClass('expand');
    TweenLite.to(System.ui.components.sectionsMenu[0], .2, {
      className: 'sections-menu',
      ease: 'Power2.easeInOut',
      onComplete: function () {
        if (!System.services.app_service.loading_app && currentSectionIndex !== System.ui.components.sectionsMenuList[0].value) {
          System.ui.components.sectionsMenuList[0].data = EW.currentAppSections;
          System.ui.behaviors.highlightAppSection(currentSectionIndex, EW.selectedSection);
        }
      }
    });
  });

  // Hash handler for activities
  new hashHandler();

  EW.activities = EW_ACTIVITIES;
  EW.oldApp = null;
  EW.apps = {};

  // Init EW plugins
  initPlugins(document);

  EW_APPS.forEach(function (item) {
    EW.apps[item.id] = item;
  });

  System.init(EW_APPS);
  System.app.on('app', System.services.app_service.load);
  System.app.onGlobal('ew_activity', System.services.app_service.load_activity);
  System.start();

  appsComponent.apps = EW_APPS;
  appBarComponent.selectedTab = System.getHashParam('app');

  if (!System.getHashParam('app')) {
    System.setHashParameters({
      app: 'content-management'
    }, true);
  }

  var $document = $(document);

  // Notify error if an ajax request fail
  $document.ajaxError(function (event, data, status) {
    // Added to ignore aborted request and don't show them as a error
    if (data && data.statusText === 'abort') {
      return;
    }

    if (EW.customAjaxErrorHandler) {
      EW.customAjaxErrorHandler = false;
      return;
    }

    try {
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

    System.ui.components.body.EW().notify({
      message: {
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
      if (mutation.type === 'childList' &&
          mutation.addedNodes.length &&
          mutation.addedNodes[0].nodeType === Node.ELEMENT_NODE) {
        initPlugins(mutation.target);
      }
    });
  });

// pass in the target node, as well as the observer options
  observer.observe(document.body, {
    attributes: false,
    childList: true,
    characterData: false,
    subtree: true
  });
});
/* global System, EW */

(function () {
  System.entity('stage/init-ui-components', init);

  function init() {
    var navMenuVue = new Vue({
      el: '#navigation-menu',
      data: {
        apps: [],
        currentState: null,
        currentApp: null,
        currentSection: null,
        currentSubSection: null,
        isNavTitleIn: false,
        isNavMenuIn: false
      },
      methods: {
        goToState: function (state) {
          if (System.modules['system/' + state]) {
            var lastSelectedSection = System.modules['system/' + state].params['app'] || state;
            return System.app.setNav(lastSelectedSection);
          }

          System.app.setNav(state);
        },
        navMenuIn: function () {
          this.isNavMenuIn = true;
        },
        navMenuOut: function () {
          this.isNavMenuIn = false;
        }
      }
    });

    System.entity('ui/apps', navMenuVue);

    // ------ //

    var primaryActionsVue = new Vue({
      el: '#main-float-menu',
      data: {
        actions: []
      },
      methods: {
        callActivity: function (action) {
          var activityCaller = EW.getActivity(action);
          activityCaller(action.hash);
        }
      }
    });

    System.entity('ui/primary-menu', primaryActionsVue);

    // ------ //

    var appBarVue = new Vue({
      el: '#app-bar',
      data: {
        appTitle: '',
        sectionTitle: '',
        isLoading: false,
        subSections: null,
        currentSubSection: navMenuVue.currentSubSection
      },
      computed: {
        styleClass: function () {
          var classes = [];

          if (this.subSections && this.subSections.length) {
            classes.push('tabs-bar-on');
          }

          return classes.join(' ');
        },
        currentState: function () {
          return navMenuVue.currentState;
        }
      },
      methods: {
        goTo: function (tab, $event) {
          $event.preventDefault();

          System.app.setNav(navMenuVue.currentApp + '/' + navMenuVue.currentSection + '/' + tab.state);
        },
        goToState: function (state) {
          System.app.setNav(state);
        },
        navTitleIn: function () {
          navMenuVue.isNavTitleIn = true;
        },
        navTitleOut: function () {
          navMenuVue.isNavTitleIn = false;
        }
      }
    });

    System.entity('ui/app-bar', appBarVue);

    // ------ //

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

    // ------ //

    var linkChooserDialog = {};
    linkChooserDialog.open = function (onSelect) {
      var linkChooserDialog = EW.createModal({
        class: 'center slim'
      });

      System.loadModule({
        url: 'html/admin/content-management/link-chooser/component.php',
        params: {
          contentType: 'content'
        }
      }, function (module) {
        module.scope.onSelect = function (content) {
          onSelect.call(null, content);

          linkChooserDialog.dispose();
        };

        linkChooserDialog.html(module.html);
      });

      return linkChooserDialog;
    };

    System.entity('ui/dialogs/link-chooser', linkChooserDialog);

    // ------ //

    ContentTools.Tools.EWMedia = (function (superClass) {
      System.utility.extend(EWMedia, superClass);

      function EWMedia() {
        return EWMedia.__super__.constructor.apply(this, arguments);
      }

      ContentTools.ToolShelf.stow(EWMedia, 'ew-media');
      EWMedia.label = 'EW Media';
      EWMedia.icon = 'ew-media';
      EWMedia.tagName = 'p';
      EWMedia.canApply = function (element, selection) {
        return true;
      };

      EWMedia.apply = function (item, selection, callback) {
        var app, forceAdd, paragraph, region, _this = this;
        app = ContentTools.EditorApp.get();
        var imageChooserDialog = EW.createModal({
          autoOpen: false,
          class: "center"
        });

        System.loadModule({
          id: 'media-chooser',
          url: 'html/admin/content-management/link-chooser/link-chooser-media.php',
          params: {
            callback: ''
          }
        }, function (module, html) {
          imageChooserDialog.html(html);

          var ref = _this._insertAt(item), node = ref[0], index = ref[1];
          module.scope.selectMedia = function (item) {
            if (item === false) {
              imageChooserDialog.dispose();
              return;
            }

            switch (item.type) {
              case 'text':
                var text = new ContentEdit.Text('p', {}, item.text);
                if (node.parent()) {
                  node.parent().attach(text, index);
                } else {
                  var firstRegion = app.orderedRegions()[0];
                  firstRegion.attach(text, index);
                }
                text.focus();

                break;

              case 'image':
                var image = new ContentEdit.Image({
                  src: item.src,
                  width: item.width,
                  height: item.height
                });
                if (node.parent()) {
                  node.parent().attach(image, index);
                } else {
                  var firstRegion = app.orderedRegions()[0];
                  firstRegion.attach(image, index);
                }
                image.focus();

                break;
            }

            imageChooserDialog.dispose();
          };
        });

        imageChooserDialog.open();
        return callback(true);
      };

      return EWMedia;

    })(ContentTools.Tool);
  }
})();

/* global System, Vue, EW */

(function () {
  System.services.app_service = {
    loading_app: false
  };


  System.services.app_service.on_load = function (app) {
    if (!app)
      return;

    this.loading_app = true;

    System.entity('ui/app-bar').isLoading = true;
    if (EW.selectedSection) {
      System.ui.utility.addClass(EW.selectedSection, 'inline-loader');
    }

    System.entity('ui/main-content').show = false;
  };

  System.services.app_service.on_loaded = function (app, html) {
    $('#app-content').append(html);
    if (app.type === 'app'/* && app.id === "system/" + System.getHashParam("app")*/) {
      EW.currentAppSections = System.modules[app.id].data.sections;
      EW.hoverApp = app.id;

      System.ui.components.sectionsMenuList[0].data = EW.currentAppSections;

      app.start();
    }

    this.loading_app = false;
  };

  System.services.app_service.load = function (path, app) {
    if (!app || app === 'Home') {
      app = 'content-management';
    }

    System.entity('ui/apps').currentState = path.join('/');
    System.entity('ui/apps').currentApp = path[0];
    System.entity('ui/apps').currentSection = path[1];
    System.entity('ui/apps').currentSubSection = path[2];

    if (app !== EW.oldApp) {
      EW.oldApp = app;
      System.entity('ui/app-bar').appTitle = EW.apps[app].title;
      System.services.app_service.on_load(EW.apps[app]);

      System.loadModule(EW.apps[app], function (module) {
        System.services.app_service.on_loaded(module, module.html);
      });
      return;
    }
  };

  System.services.app_service.load_section = function (moduleId) {
    var element = System.ui.components.sectionsMenuList[0].links[EW.oldApp + '/' + moduleId];
    System.ui.behaviors.highlightAppSection(element.dataset.index, element);

    if (element) {
      var moduleConfig = System.ui.components.sectionsMenuList[0].data[element.dataset.index];
      if (!moduleConfig/* || sectionData.id === EW.oldSectionId*/) {
        return;
      }

      EW.oldSectionId = moduleConfig.id;
      System.entity('ui/app-bar').sectionTitle = moduleConfig.title;
      System.entity('ui/app-bar').isLoading = true;
      System.ui.utility.addClass(element, 'inline-loader');

      System.entity('ui/app-bar').subSections = [];
      System.entity('ui/primary-menu').actions = [];
      $('#app-main-actions').empty();

      System.ui.components.mainContent.empty();
      System.abortAllRequests();

      System.loadModule(moduleConfig, function (module, html) {
        $('#action-bar-items').find("button,div").remove();

        if (!System.getHashNav('app')[0]) {
          return;
        }

        System.entity('ui/app-bar').subSections = module.data.subSections || [];

        System.entity('ui/main-content').show = false;
        System.ui.components.mainContent.html(html);

        module.start();

        System.entity('ui/app-bar').isLoading = false;
        System.ui.utility.removeClass(element, 'inline-loader');
        Vue.nextTick(function () {
          System.entity('ui/main-content').show = true;
        });
      });
    }
  };

  System.services.app_service.load_tab = function (module) {
    System.entity('ui/app-bar').isLoading = true;
    System.entity('ui/primary-menu').actions = [];
    $('#app-main-actions').empty();

    System.ui.components.mainContent.empty();
    System.abortAllRequests();

    System.loadModule(module, tab_loaded);

    function tab_loaded(module, html) {
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
  };

  System.services.app_service.select_app_section = function (component, full, section) {
    if (!section) {
      System.ui.components.sectionsMenuList[0].value = '0';
      return;
    }

    if (component.data.tab === section) {
      return;
    }

    component.data.tab = section;
    System.services.app_service.load_section(section);
  };

  System.services.app_service.select_sub_section = function (component, full, tab) {
    if (!tab) {
      if (component.data.activeSubSection) {
        var appBar = System.entity('ui/apps');
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

  var oldEWActivity = null;
  System.services.app_service.load_activity = function (activityId) {
    var url = activityId ? activityId.substr(0, activityId.lastIndexOf('_')) : null;
    var activityName = activityId;

    if (url && url !== activityId) {
      activityName = url;
    }

    var currentActivity = EW.activities[activityId] || EW.activities[activityName];

    if (activityName && activityName !== oldEWActivity) {
      var settings = {
        closeHash: {}, /*hash: {key: "ew_activity", value: activity},*/
        onOpen: function () {
          var modal = this;
          var activityParameters = EW.getHashParameters();
          // Manage post data if it is set
          if (currentActivity.parameters) {
            // Add user defined post data to the postData variable
            // Call post data if it is a function
            if (typeof currentActivity.parameters === 'function') {
              $.extend(activityParameters, currentActivity.parameters(activityParameters));
              System.setHashParameters(activityParameters, true);
            } else if (currentActivity.parameters) {
              $.extend(activityParameters, currentActivity.parameters);
              System.setHashParameters(activityParameters, true);
            }
          }
          // Add the parameters which have been pass to the activity caller function 
          if (currentActivity.newParams) {
            $.extend(activityParameters, currentActivity.newParams);
            System.setHashParameters(activityParameters, true);
          }

          activityParameters = EW.getHashParameters();

          var method = currentActivity.request.method || 'GET';
          $.ajax({
            type: method,
            url: currentActivity.request.url,
            data: $.extend({}, activityParameters, currentActivity.privateParams),
            success: function (data) {
              modal.html(data);
            },
            error: function (result) {
              console.log(result);
              //alert(result.responseText);
              modal.html(result.responseText);
              if (result.responseJSON) {
                alert(result.responseJSON.message);
              }

              EW.customAjaxErrorHandler = true;
            }
          });

        },
        onClose: function () {
          currentActivity = EW.activities[activityId] || EW.activities[activityName];

          if (!currentActivity) {
            return;
          }

          EW.activitySource = null;
          var closeHashParameters = {
            ew_activity: null
          };
          //var customHashParameters = {};
          if (currentActivity.onDone) {

            if (typeof currentActivity.onDone === 'function') {
              currentActivity.onDone(closeHashParameters);
            } else {
              $.extend(closeHashParameters, currentActivity.onDone);
            }
          }
          // Trigger close activity event and pass closeHashParameters to it
          $(document).trigger(activityName + '.close', closeHashParameters);
          $.extend(closeHashParameters, settings.closeHash);
          EW.setHashParameters(closeHashParameters);
        }
      };

      if (currentActivity) {
        // Trigger open activity event and pass settings to it before creating modal
        $(document).trigger(activityName + '.open', settings);
        $.extend(settings, currentActivity.modal);
        currentActivity.modalObject = EW.activities[activityName].modalObject = EW.createModal(settings);
      } else {
        alert('Activity not found: ' + activityName);
        EW.setHashParameters({
          ew_activity: null
        });
      }
      oldEWActivity = activityName;
    } else if (oldEWActivity !== activityName) {
      if (oldEWActivity && EW.activities[oldEWActivity].modalObject) {
        EW.activities[oldEWActivity].modalObject.trigger('close');
      }

      oldEWActivity = activityName;
    }
  };

})();