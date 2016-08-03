/* global System, Vue, EW */

(function (System) {
  System.services.app_service = {
    loading_app: false
  };


  System.services.app_service.on_load = function (app) {
    if (!app)
      return;

    this.loading_app = true;

    System.entity('ui/app-bar').isLoading = true;
    if (EW.selectedSection) {
      System.ui.utility.addClass(EW.selectedSection, "inline-loader");
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
    if (!app || app === "Home") {
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
      System.entity('ui/primary-actions').actions = [];
      $("#app-main-actions").empty();

      System.ui.components.mainContent.empty();
      System.abortAllRequests();

      System.loadModule(moduleConfig, function (module, html) {
        $("#action-bar-items").find("button,div").remove();

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
    System.entity('ui/primary-actions').actions = [];
    $("#app-main-actions").empty();

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
      System.UI.components.sectionsMenuList[0].value = '0';
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

})(System);