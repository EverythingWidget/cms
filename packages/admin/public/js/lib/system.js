(function () {

  var entities = {};
  System = {
    moduleIdentifier: "app",
    modules: {},
    uiTemplates: {},
    appPathfiledName: null,
    activityTree: [
    ],
    onLoadQueue: [
    ],
    activeModule: null,
    notYetStarted: [
    ],
    activeRequests: {},
    onModuleLoaded: {},
    UI: {
    },
    controllers: {},
    controller: function (controllerId, controllerObject) {
      if (!controllerObject) {
        return this.controllers[controllerId];
      }
      return this.controllers[controllerId] = controllerObject;
    },
    services: {},
    service: function (service_id, serviceObject) {
      if (!serviceObject) {
        return this.services[service_id];
      }
      return this.services[service_id] = serviceObject;
    },
    entity: function (entity_id, entityObject) {
      if (!entityObject) {
        return entities[entity_id];
      }

      if (entities[entity_id]) {
        throw new Error("An entity with this id already exist. Entities can't be overwriten");
      }

      entities[entity_id] = entityObject;
    },
    // Apps Management
    /* registerApp: function (id, object)
     {
     this.modules[id] = $.extend(true, {}, System.MODULE_ABSTRACT, object);
     },*/
    /**
     * 
     * @param {String} id
     * @param {Object} object
     * @returns {sys.ABSTRACT_MODULE}
     */
    state: function (id, decorator) {
      //return this.app.module(id, object, false);
      var module, modulePath, moduleNavigation;
      var domain = this;
      if (!domain) {
        throw "Domain can NOT be null";
      }
      id = this.app.id + '/' + id;

      //if forceReload is true, then init the module again
      if (!decorator/* && this.modules[id]*/) {
        // Add the module to notYetStarted list so it can be started by startLastLoadedModule method
        domain.notYetStarted.push(id);
        return domain.modules[id];
      }

      if (domain.modules[id]) {
        return domain.modules[id];
      }

      if (typeof (decorator) === "function") {
        module = $.extend(true, {}, System.MODULE_ABSTRACT);
        decorator.call(module);
      } else {
        module = $.extend(true, {}, System.MODULE_ABSTRACT, decorator || {});
      }

      module.domain = domain;
      module.id = id;

      modulePath = domain.app.navigation[module.moduleIdentifier] ? domain.app.navigation[module.moduleIdentifier] : [];
      moduleNavigation = $.extend(true, {}, domain.app.navigation);
      moduleNavigation[module.moduleIdentifier] = modulePath.slice(id.split("/").length - 1);

      domain.modules[id] = module;
      domain.notYetStarted.push(id);

      // Set module hash for this module when its inited
      // module hash will be set in the hashChanged method as well
      // if current navigation path is equal to this module id
      //module.hash = System.modulesHashes[id.replace("system/", "")] = module.moduleIdentifier + "=" + id.replace("system/", "");

      module.init(moduleNavigation, domain.app.params);

      return module;
    },
    /** This method will be called whenever System attempts to load an app
     * 
     * @param {Object} app
     * @returns {Boolean} True if the app should be loaded and false if the app may not be loaded
     */
    onLoadApp: function (app) {
      // Example: show a loading animation
      return true;
    },
    onAppLoaded: function (app, data) {
      // Example: add the content into the DOM
    },
    // Close App
    closeApp: function (appId) {
      if (this.onCloseApp(System.modules[appId])) {
        System.modules[appId].blur();
        var pos = this.activityTree.lastIndexOf(appId);
        if (pos !== -1) {
          this.activityTree.splice(pos, 1);
        }
        System.modules[appId].dispose();
        this.onAppClosed(System.modules[appId]);
      }
    },
    onCloseApp: function (app) {

    },
    onAppClosed: function () {
      return true;
    },
    abortLoadingApp: function () {
      if (this.loadingAppXHR) {
        this.loadingAppXHR.abort();
        this.loadingAppXHR = null;
        delete this.activeRequests[this.loadingAppXHR.creationId];
      }
    },
    modulesHashes: {},
    hashChecker: null,
    /**
     * 
     * @param {String} id
     * @param {Function} handler
     */
    on: function (id, handler) {
      this.app.on.call(this.app, id, handler);
    },
    hashHandler: function (nav, params) {
    },
    navigation: {},
    params: {},
    start: function () {
      var _this = this;
      var detect = function () {
        //console.log(self.app.oldHash, window.location.hash)
        if (_this.app.oldHash !== window.location.hash/* || self.app.newHandler*/) {
          var hashValue = window.location.hash,
                  navigation = {},
                  params = {};

          hashValue = hashValue.replace(/^#\/?/igm, '');

          hashValue.replace(/([^&]*)=([^&]*)/g, function (m, k, v) {
            navigation[k] = v.split("/").filter(Boolean);
            params[k] = v;
          });

          _this.setModuleHashValue(navigation, params, hashValue);
          _this.app.hashChanged(navigation, params, hashValue, navigation[_this.app.moduleIdentifier]); // System

          _this.app.oldHash = '#' + hashValue;
        }
      };

      detect();
      clearInterval(this.hashChecker);
      this.hashChecker = setInterval(function () {
        detect();
      }, 50);
    },
    setURLHash: function (hash) {
      //var hash = hash;
      hash = hash.replace(/^#\/?/igm, '');

      var navigation = {};
      var params = {};
      hash.replace(/([^&]*)=([^&]*)/g, function (m, k, v) {
        navigation[k] = v.split("/").filter(Boolean);
        params[k] = v;
      });

    },
    getHashParam: function (key, hashName) {
      return this.app.params[key] || null;
    },
    getHashNav: function (key, hashName) {
      return this.app.navigation[key] || [
      ];
    },
    detectActiveModule: function (moduleId, parameters) {
      var nav = parameters["app"];

      /*if ("system/" + nav === moduleId) {
       System.app.activeModule = System.modules["system/" + nav];
       }*/
    },
    firstTime: false,
    setModuleHashValue: function (navigation, parameters, hashValue, init) {
      var nav = parameters["app"];

      if (nav && System.modulesHashes[nav] && System.app.activeModule !== System.modules["system/" + nav]) {
        //window.location.hash = System.modulesHashes[nav];
        // When the navigation path is changed
        //alert(System.modulesHashes[nav] + " YES " + nav);
      } else if (nav && !this.firstTime) {
        // first time indicates that the page is (re)loaded and the window.location.hash should be set
        // as the module hash value for the module which is specified by app parameter in the hash value.
        // Other modules get default hash value
        System.modulesHashes[nav] = hashValue;
        this.firstTime = true;
        //alert("first time: " + System.modulesHashes[nav] + " " + hashValue);
      } else if (nav && !System.modulesHashes[nav]) {
        // When the module does not exist 
        System.modulesHashes[nav] = "app=" + nav;
        //alert(System.modulesHashes[nav] + " default hash");
      } else if (nav && System.modulesHashes[nav]) {
        // When the hash parameters value is changed from the browser url bar or originated from url bar
        System.modulesHashes[nav] = hashValue;
      }
    },
    /** Set parameters for app/nav. if app/nav was not in parameters, then set paraters for current app/nav
     * 
     * @param {type} parameters
     * @param {type} replace if true it overwrites last url history otherwise it create new url history
     * @param {type} clean clean all the existing parameters
     * @returns {undefined}
     */
    setHashParameters: function (parameters, replace, clean) {
      this.lastHashParams = parameters;
      var hashValue = window.location.hash;
      //var originHash = hashValue;
      var nav = parameters["app"];
      if (nav && !System.modulesHashes[nav]) {
        //console.log(hashValue, nav)
        System.modulesHashes[nav] = hashValue = "app=" + nav;

      } else if (nav && System.modulesHashes[nav]) {
        //console.log(hashValue, nav , System.modulesHashes[nav]);
        //alert("---------");
        hashValue = System.modulesHashes[nav];
      }
      //console.log(parameters, nav, System.modulesHashes[nav]);

      if (hashValue.indexOf("#") !== -1) {
        hashValue = hashValue.substring(1);
      }
      var pairs = hashValue.split("&");
      var newHash = "#";
      var and = false;
      hashValue.replace(/([^&]*)=([^&]*)/g, function (m, k, v) {
        if (parameters[k] != null) {
          newHash += k + "=" + parameters[k];
          newHash += '&';
          and = true;
          delete parameters[k];
        } else if (!parameters.hasOwnProperty(k) && !clean) {
          newHash += k + "=" + v;
          newHash += '&';
          and = true;
        }
      });
      // New keys
      $.each(parameters, function (key, value) {
        if (key && value) {
          newHash += key + "=" + value + "&";
          and = true;
        }
      });
      newHash = newHash.replace(/\&$/, '');

      if (replace) {
        window.location.replace(('' + window.location).split('#')[0] + newHash);
      } else {
        window.location.hash = newHash.replace(/\&$/, '');
      }
    },
    load: function (href, onDone) {

      return this.addActiveRequest($.get(href, function (response) {

        if ("function" === typeof (onDone)) {
          onDone.call(this, response);
        }

      }));
    },
    /**
     * 
     * @param {json} mod  id: the id of module, url: path to the module
     * @param {callback} onDone
     * @returns {void}
     */
    loadModule: function (mod, onDone) {
      System.onModuleLoaded["system/" + mod.id] = onDone;
      var module = System.modules["system/" + mod.id];

      if (module) {

        if ("function" === typeof (System.onModuleLoaded["system/" + mod.id])) {
          System.onModuleLoaded["system/" + mod.id].call(this, module, module.html);
          System.onModuleLoaded["system/" + mod.id] = null;
        }

        return;
      }
      if (System.onLoadQueue["system/" + mod.id]) {
        return;
      }

      System.onLoadQueue["system/" + mod.id] = true;

      $.get(mod.url, function (response) {
        if (System.modules["system/" + mod.id]) {
          $("#system_" + mod.id.replace(/[\/-]/g, "_")).remove();
          //return;
        }
        var scripts = null;
        var raw = $(response);
        //var scripts = raw.filter("script").remove();
        var html = raw.filter(function (i, e) {

          if (e.tagName && e.tagName.toLowerCase() === "script") {
            //console.log(e.tagName);
            scripts = $(e);
            return false;
          }
          return true;

        });
        var templates = {};
        html = html.filter(function (i, e) {

          if (e.dataset && e.dataset.uiTemplate) {
            //console.log(e.tagName);
            //templates[e.dataset.uiTemplate] = e;
            return false;
          }
          return true;

        });

        System.uiTemplates["system/" + mod.id] = templates;

        if (scripts)
          $("head").append(scripts);
        //var html = res;
        //System.apps[id] = $.extend({}, System.state, self.apps[id]);
        //System.activityTree.unshift(System.apps[id]);
        //console.log(System.app.modules);
        //var module = System.state(mod.id);
        if (!System.modules["system/" + mod.id]) {
          alert("Invalid module: " + mod.id);
          return;
        }

        System.modules["system/" + mod.id].html = html;

        if (scripts)
          scripts.attr("id", System.modules["system/" + mod.id].id.replace(/[\/-]/g, "_"));

        if ("function" === typeof (System.onModuleLoaded["system/" + mod.id])) {
          //onDone.call(this, System.modules["system/" + mod.id], response);
          //console.log(System.modules["system/" + mod.id].html.html());
          System.onModuleLoaded["system/" + mod.id].call(this, System.modules["system/" + mod.id], html);
          //console.log(System.modules["system/" + mod.id].html.html());
          System.onModuleLoaded["system/" + mod.id] = null;
        }

        delete System.onLoadQueue["system/" + mod.id];
      });
    },
    addActiveRequest: function (request) {
      var _this = this,
              parentSuccess = request.done,
              id;
      // Overwrite the done method in order to remove the request from the activeRequest list
      request.done = function (callback) {
        parentSuccess.call(this, callback);

        if (request.creationId) {
          delete _this.activeRequests[request.creationId];
        }
      };

      id = this.generateRequestId();

      request.creationId = id;
      this.oldRequestCreationId = id;
      this.activeRequests[id] = request;
      return request;
    },
    generateRequestId: function () {
      var id = new Date().valueOf();
      while (id === this.oldRequestCreationId) {
        id = new Date().valueOf();
      }
      return id;
    },
    abortAllRequests: function () {
      for (var request in this.activeRequests) {
        this.activeRequests[request].abort();
        //console.log("aborted: "+ this.activeRequests[request].creationId);
        delete this.activeRequests[request];
      }
//      this.onLoadQueue = [
//      ];
      this.currentOnLoad = null;
    },
    startModule: function (moduleId) {
      var module = this.modules[moduleId];
      if (!module) {
        alert("Module does not exist: " + moduleId);
        console.error("Module does not exist: " + moduleId);
        return;
      }
      this.modules[moduleId].start();

    },
    startLastLoadedModule: function () {
      if (this.notYetStarted.length > 0) {
        //alert(this.modules[this.notYetStarted[this.notYetStarted.length - 1]].id)
        this.modules[this.notYetStarted[this.notYetStarted.length - 1]].start();
      }
    },
    init: function (mods) {
      this.app = $.extend(true, {}, System.MODULE_ABSTRACT);
      this.app.domain = this;
      this.app.moduleIdentifier = this.moduleIdentifier;
      this.app.id = "system";
      this.app.installModules = mods;
      this.app.init({}, {}, "");
    }
  };

  System.utility = System.Util = {
    installModuleStateHandlers: function (module, states) {
      for (var state in states) {
        module.on(state, states[state]);
      }
    }
  };

}());