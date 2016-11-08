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
    $("#app-content").append(html);

    if (app.type === "app"/* && app.id === "system/" + System.getHashParam("app")*/) {
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

      System.services.app_service.on_load(EW.apps[app]);

      System.loadModule(EW.apps[app], function (module) {
        System.services.app_service.on_loaded(module, module.html);
      });
      return;
    }
  };

  System.services.app_service.load_section = function (moduleId) {
    var element = System.ui.components.sectionsMenuList[0].links[EW.oldApp + "/" + moduleId];
    System.ui.behaviors.highlightAppSection(element.dataset.index, element);
    //System.ui.components.sectionsMenuList[0].value = element.dataset.index;

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
      System.entity('ui/primary-menu').actions = [];
      $('#app-main-actions').empty();

      System.ui.components.mainContent.empty();
      System.abortAllRequests();

      System.loadModule(moduleConfig, function (module, html) {
        $('#action-bar-items').find("button,div").remove();

        if (!System.getHashNav("app")[0]) {
          return;
        }

        System.entity('ui/app-bar').subSections = module.data.subSections || [];

        System.entity('ui/main-content').show = false;
        System.ui.components.mainContent.html(html);

        module.start();

        System.entity('ui/app-bar').isLoading = false;
        System.ui.utility.removeClass(element, "inline-loader");
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
            } else {
              $.extend(activityParameters, currentActivity.parameters);
            }
          }
          // Add the parameters which have been pass to the activity caller function 
          if (currentActivity.newParams) {
            $.extend(activityParameters, currentActivity.newParams);
          }

          System.setHashParameters(activityParameters, true);
          activityParameters = EW.getHashParameters();

          var method = currentActivity.request.method || 'GET';

//          if (true) {
//            System.loadModule({
//              url: currentActivity.request.url,
//              params: $.extend({}, activityParameters, currentActivity.privateParams)
//            }, function (module) {
//              modal.html(module.html);
//            });
//          } else {
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
//          }
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
          $(document).trigger(activityName + ".close", closeHashParameters);
          $.extend(closeHashParameters, settings.closeHash);
          EW.setHashParameters(closeHashParameters);
        }
      };

      if (currentActivity) {
        // Trigger open activity event and pass settings to it before creating modal
        $(document).trigger(activityName + ".open", settings);

        // Do not create modal if activity has a modal already
        //if (self.activities[activity].hasModal)
        //return;

        $.extend(settings, currentActivity.modal);
        //modal = self.createModal(settings);
        currentActivity.modalObject = EW.activities[activityName].modalObject = EW.createModal(settings);
        //EW.activities[activity].
      } else {
        alert("Activity not found: " + activityName);
        EW.setHashParameters({
          ew_activity: null
        });
      }
      oldEWActivity = activityName;
    } else if (oldEWActivity !== activityName) {
      if (oldEWActivity && EW.activities[oldEWActivity].modalObject) {
        EW.activities[oldEWActivity].modalObject.trigger("close");
      }

      oldEWActivity = activityName;
    }
  };

})();