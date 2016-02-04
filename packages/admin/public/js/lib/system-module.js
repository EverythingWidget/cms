(function (System) {
  /*function Module() {
   
   }
   
   Module.prototype.init = function () {
   
   };
   
   Module.prototype.start = function () {
   
   };
   
   Module.prototype.dispose = function () {
   
   };
   
   Module.prototype.module = function () {
   
   };
   
   Module.prototype.on = function () {
   
   };
   
   Module.prototype.setParam = function () {
   
   };
   
   Module.prototype.setParamIfNone = function () {
   
   };
   
   Module.prototype.trigger = function () {
   
   };
   
   Module.prototype.hashChanged = function () {
   
   };*/

  System.MODULE_ABSTRACT = {
    domain: null,
    inited: false,
    started: false,
    active: false,
    moduleIdentifier: "app",
    navigation: {},
    params: {},
    html: "",
    //modules: {},
    installModules: [
    ],
    //activeModule: null,
    init: function (navigations, params, html) {
      this.inited = true;
      //this.navigation = navigations;
      //this.params = params;
      //this.html = html;
      this.trigger("onInit",html);

      this.installModules.forEach(function (lib) {
        //alert("install: " + lib.id);
        System.loadModule(lib/*, function () {
         alert("install completed: " + lib.id);
         }*/);
      });
    },
    start: function () {
      this.started = true;
      this.active = true;
      //System.app.activeModule = this;
      this.trigger("onStart");
      /*var modNav = this.navigation[this.moduleIdentifier] ? this.navigation[this.moduleIdentifier].slice(1) : [];
       var newNav = $.extend(true, {}, this.navigation);
       newNav[this.moduleIdentifier] = modNav;*/
      //var modNav = System.app.navigation[this.moduleIdentifier] ? System.app.navigation[this.moduleIdentifier].slice(1) : [];
      var newNav = $.extend(true, {}, this.domain.app.navigation);
      var st = "system/" + this.domain.app.params[this.moduleIdentifier];
      var napPath = st.indexOf(this.id) === 0 ? st.substr(this.id.length).split("/").filter(Boolean) : [
      ];

      newNav[this.moduleIdentifier] = napPath;
      var n = newNav;
      var p = this.domain.app.params;
      this.navigation = {};
      this.params = {};
      // Empty navigation and params before call the hashChanged method at the starting phase.
      // This will force the module to call all its event handlers
      //console.log("Module started: " + this.id, n, p);
      this.hashChanged(n, p, this.hash, this.domain.getHashParam(this.moduleIdentifier));

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
      var module;
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

      /*var modulePath = this.navigation[module.moduleIdentifier] ? this.navigation[module.moduleIdentifier].slice(1) : [
       ];*/

      var modulePath = this.navigation[module.moduleIdentifier] ? this.navigation[module.moduleIdentifier] : [
      ];
      //console.log(id, id.split("/").length, modulePath.slice(id.split("/").length-1));
      var moduleNavigation = $.extend(true, {}, this.navigation);
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
    hashChanged: function (navigation, params, hashValue) {
      var _this = this;
      var moduleNavigation = navigation;

      var fullNavPath = params["app"];

      //console.log(this.id, "system/" + fullNavPath, params, this.domain);
      if (this.id === "system/" + fullNavPath/* && System.app.activeModule !== this*/) {
        this.domain.app.activeModule = this;

      } else {
        this.domain.app.activeModule = null;
        this.active = false;
      }

      this.hashHandler.call(this, navigation, params);
      var allNavigations = $.extend({}, this.navigation, navigation);

      if (this.domain.app.activeModule && this.active && this.domain.app.activeModule.id === _this.id) {
        $.each(allNavigations, function (key, value) {
          var navHandler = _this.hashListeners[key];
          if (navHandler) {
            if (_this.navigation[key]) {
              var currentKeyValue = _this.navigation[key].join("/");

              if (navigation[key] && currentKeyValue === navigation[key].join("/")) {
                //console.log("Same, ignore: " + key/*, navigation[key], value.join("/")*/);
                return;
              }
            }

            var args = [
            ];
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
//console.log("navHandler", navHandler)
        //if navHandler is null call sub module navHandler
        if (navHandler && navigation["app"]) {
          if (navigation["app"]) {

            var currentKeyValue = _this.navigation["app"] ? _this.navigation["app"].join("/") : [
            ];

            if (navigation["app"] && currentKeyValue !== navigation["app"].join("/")) {
              var args = [
              ];
              args.push(navigation["app"]);
              for (var i = 0, len = navigation["app"].length; i < len; ++i) {
                //i is always valid index in the arguments object
                args.push(navigation["app"][i]);
              }

              navHandler.apply(_this, args);
            }
          }

        }
      }

      this.navigation = navigation;
      this.params = params;
      //this.hash = hashValue;

      if (this.moduleIdentifier && navigation[this.moduleIdentifier] && navigation[this.moduleIdentifier][0])
      {
        // Set the app.activeModule according to the current navigation path
        if (this.domain.modules[this.id + "/" + navigation[this.moduleIdentifier][0]]) {
          /*System.app.activeModule = */this.activeModule = this.domain.modules[this.id + "/" + navigation[this.moduleIdentifier][0]];
        }
      } else {
        this.activeModule = null;
      }

      if (this.activeModule)
      {
        // Remove first part of navigation in order to force activeModule to only react to events at its level and higher 
        var modNav = navigation[this.moduleIdentifier].slice(1);
        moduleNavigation = $.extend(true, {}, navigation);
        moduleNavigation[this.moduleIdentifier] = navigation[this.moduleIdentifier].slice(this.activeModule.id.split("/").length - 1);
        ;
        if (!this.activeModule.started) {
          //alert("system." + navigation[this.moduleIdentifier][0])
          return;
        }

        // Call module level events handlers
        this.activeModule.hashChanged(moduleNavigation, this.params, hashValue, fullNavPath || navigation[this.moduleIdentifier].join("/"));
      }
    },
    hashHandler: function (nav, params) {
      /*if (this.activeModule && !e.isDefaultPrevented())
       this.activeModule.hashHandler(e, data);*/
    }
  }
})(System);