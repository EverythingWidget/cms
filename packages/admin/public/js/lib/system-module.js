(function (System) {
  System.MODULE_ABSTRACT = {
    domain: null,
    inited: false,
    started: false,
    active: false,
    moduleIdentifier: "app",
    navigation: {},
    params: {},
    html: "",
    installModules: [],
    onEvents: {},
    installModulesOnInit: function (modules) {
      this.installModules = modules;
    },
    init: function (navigations, params, html) {
      var _this = this;
      this.inited = true;
      this.trigger("onInit", [System.UI.templates[this.id]]);
      this.triggerEvent('init', [System.UI.templates[this.id]]);

      this.installModules.forEach(function (lib) {
        _this.domain.loadModule(lib);
      });
    },
    start: function () {
      this.started = true;
      this.active = true;
      this.trigger("onStart");
      this.triggerEvent('start');
      var newNav = $.extend(true, {}, this.domain.app.navigation);
      var st = "system/" + this.domain.app.params[this.moduleIdentifier];
      var napPath = st.indexOf(this.id) === 0 ? st.substr(this.id.length).split("/").filter(Boolean) : [];

      newNav[this.moduleIdentifier] = napPath;
      var nav = newNav;
      var params = this.domain.app.params;
      this.navigation = {};
      this.params = {};
      // Empty navigation and params before call the hashChanged method at the starting phase.
      // This will force the module to call all its event handlers
      //console.log("Module started: " + this.id, n, p);

      // This code is commented because its bug prone
      // hashChanged should be called only when the module params are inited with valid data
      // in other word start should be called after hashChanged
      this.hashChanged(nav, params, this.hash, this.domain.getHashNav(this.moduleIdentifier));
      //console.log(this.id , )

      var index = this.domain.notYetStarted.indexOf(this.id);
      if (index > -1) {
        this.domain.notYetStarted.splice(index, 1);
      }
    },
    dispose: function () {

    },
    /** Creates a system module if does not exist and returns it.
     * A module is more like a url state handler. It handles event that are originated from url.
     * It's recommended that developer use modules only for listening to url events or as a controller
     * and implement the business logic in a different class/object (e.g. Decorator object or a Service object)
     * This way, developer wont mix the business logic whit the System library logic.
     * 
     * @param {String} id
     * @param {Object} object
     * @param {Boolean} set true to force the system to re init the module
     * @returns {System.MODULE_ABSTRACT}
     */
    module: function (id, object, forceReload) {
      var module, modulePath, moduleNavigation;
      var domain = this.domain;
      if (!domain) {
        throw "Domain can NOT be null";
      }
      id = this.id + '/' + id;

      //if forceReload is true, then init the module again
      if (!object && !forceReload/* && this.modules[id]*/) {
        // Add the module to notYetStarted list so it can be started by startLastLoadedModule method
        domain.notYetStarted.push(id);
        return domain.modules[id];
      }

      if (domain.modules[id]) {
        return domain.modules[id];
      }

      if (typeof (object) === "function") {
        module = $.extend(true, {}, System.MODULE_ABSTRACT);
        object.call(module);
      } else {
        module = $.extend(true, {}, System.MODULE_ABSTRACT, object || {});
      }

      module.domain = domain;
      module.id = id;

      modulePath = this.navigation[module.moduleIdentifier] ? this.navigation[module.moduleIdentifier] : [];
      moduleNavigation = $.extend(true, {}, this.navigation);
      moduleNavigation[module.moduleIdentifier] = modulePath.slice(id.split("/").length - 1);

      domain.modules[id] /*= this.modules[id]*/ = module;
      domain.notYetStarted.push(id);

      // Set module hash for this module when its inited
      // module hash will be set in the hashChanged method as well
      // if current navigation path is equal to this module id
      //module.hash = System.modulesHashes[id.replace("system/", "")] = module.moduleIdentifier + "=" + id.replace("system/", "");

      module.init(moduleNavigation, this.params);

      return module;
    },
    hashListeners: {},
    data: {},
    /**
     * 
     * @param {String} id
     * @param {Function} handler
     * @returns {undefined}
     */
    on: function (id, handler) {
      this.hashListeners[id] = handler;
    },
    getParam: function (key) {
      return this.domain.getHashParam(key);
    },
    getNav: function (key) {
      return this.domain.getHashNav(key);
    },
    setParam: function (param, value, replace) {
      var paramObject = {};
      paramObject[param] = value;
      this.domain.setHashParameters(paramObject, replace);
    },
    setParamIfNone: function (param, value) {
      if (!this.domain.getHashParam(param)) {
        var paramObject = {};
        paramObject[param] = value;
        this.domain.setHashParameters(paramObject, true);
      }
    },
    /**
     * 
     * @param {string} event name of module internal event
     * @param {function} action the action that bind one to one to the specified event
     * @returns {void}
     */
    bind: function (event, action) {
      if ('string' === typeof (event) && 'function' === typeof (action)) {
        this.onEvents[event] = action;
      }
    },
    /**
     * Call the event function if exist and pass the args to it
     * 
     * @param {String} event
     * @param {Array} args
     * @returns {undefined}
     */
    trigger: function (event, args) {
      if (typeof (this[event]) === "function") {
        this[event].apply(this, args);
      }
    },
    triggerEvent: function (event, args) {
      if (typeof (this.onEvents[event]) === "function") {
        this.onEvents[event].apply(this, args);
      }
    },
    hashChanged: function (navigation, params, hashValue, fullNav) {
      var _this = this;
      var moduleNavigation = navigation;

      var fullNavPath = params["app"];

      //console.log(this.id, "system/" + fullNavPath, params, this.domain);
      if (this.id === "system/" + fullNavPath/* && System.app.activeModule !== this*/) {
        this.domain.app.activeModule = this;
        this.domain.app.activeModule.active = true;
      } else {
        this.domain.app.activeModule = null;
        this.active = false;
      }

      this.hashHandler.call(this, navigation, params);
      var allNavigations = $.extend({}, this.navigation, navigation);

      var tempNav = _this.navigation;

      _this.navigation = navigation;
      _this.params = params;
      if (this.domain.app.activeModule && this.active && this.domain.app.activeModule.id === _this.id) {
        $.each(allNavigations, function (key, value) {
          var navHandler = _this.hashListeners[key];
          if (navHandler) {
            if (tempNav[key]) {
              var currentKeyValue = tempNav[key].join("/");

              if (navigation[key] && currentKeyValue === navigation[key].join("/")) {
                //console.log("Same, ignore: " + key/*, navigation[key], value.join("/")*/);
                return;
              }
            }

            var args = [];
            args.push(navigation[key]);
            for (var i = 0; i < value.length; ++i) {
              //i is always valid index in the arguments object
              args.push(value[i]);
            }
            navHandler.apply(_this, args);
          }
        });
      } else if (!this.active) {

        var navHandler = _this.hashListeners["app"];

        //if navHandler is null call sub module navHandler
        if (navHandler && navigation["app"]) {
          var currentKeyValue = tempNav["app"] ? tempNav["app"].join("/") : [];

          if (currentKeyValue !== navigation["app"].join("/")) {
            var args = [];
            args.push(navigation["app"]);

            for (var i = 0, len = navigation["app"].length; i < len; ++i) {
              //i is always valid index in the arguments object
              args.push(navigation["app"][i]);
            }

            navHandler.apply(_this, args);
          }
        }
      }


      //this.hash = hashValue;

      if (this.moduleIdentifier && navigation[this.moduleIdentifier] && navigation[this.moduleIdentifier][0])
      {
        // Set the app.activeModule according to the current navigation path
        if (this.domain.modules[this.id + "/" + navigation[this.moduleIdentifier][0]]) {
          this.activeModule = this.domain.modules[this.id + "/" + navigation[this.moduleIdentifier][0]];
        }
      } else {
        this.activeModule = null;
      }

      if (this.activeModule)
      {
        // Remove first part of navigation in order to force activeModule to only react to events at its level and higher 
        //var modNav = navigation[this.moduleIdentifier].slice(1);
        moduleNavigation = $.extend(true, {}, navigation);
        moduleNavigation[this.moduleIdentifier] = fullNav.slice(this.activeModule.id.split("/").length - 1);

        /* Removed - cause a bug
         * if (!this.activeModule.started) {
         return;
         }*/

        // Call module level events handlers
        this.activeModule.hashChanged(moduleNavigation, this.params, hashValue, fullNav);
      }
    },
    hashHandler: function (nav, params) {
    }
  }
})(System);