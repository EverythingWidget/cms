(function () {

  var entities = {};
  var importedLibraries = {};
  System = {
    stateKey: "app",
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
    state: function (id, handlaer) {
      //return this.app.module(id, object, false);
      var module, modulePath, moduleNavigation;
      var domain = this;
      if (!domain) {
        throw "Domain can NOT be null";
      }
      id = this.app.id + '/' + id;

      //if forceReload is true, then init the module again
      if (!handlaer/* && this.modules[id]*/) {
        // Add the module to notYetStarted list so it can be started by startLastLoadedModule method
        domain.notYetStarted.push(id);
        return domain.modules[id];
      }

      if (domain.modules[id]) {
        return domain.modules[id];
      }

      if (typeof (handlaer) === "function") {
        module = $.extend(true, {}, System.MODULE_ABSTRACT);
        handlaer.call(null, module);
      } else {
        module = $.extend(true, {}, System.MODULE_ABSTRACT, handlaer || {});
      }

      module.domain = domain;
      module.id = id;

      modulePath = domain.app.navigation[module.stateKey] ? domain.app.navigation[module.stateKey] : [];
      moduleNavigation = $.extend(true, {}, domain.app.navigation);
      moduleNavigation[module.stateKey] = modulePath.slice(id.split("/").length - 1);

      domain.modules[id] = module;
      domain.notYetStarted.push(id);

      // Set module hash for this module when its inited
      // module hash will be set in the hashChanged method as well
      // if current navigation path is equal to this module id
      //module.hash = System.modulesHashes[id.replace("system/", "")] = module.stateKey + "=" + id.replace("system/", "");

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
          _this.app.hashChanged(navigation, params, hashValue, navigation[_this.app.stateKey]); // System
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

      if (!nav) {
        return;
      }

      if (System.modulesHashes[nav] && System.app.activeModule !== System.modules["system/" + nav] && System.app.activeModule && System.app.activeModule.stateKey === 'app') {
        //window.location.hash = System.modulesHashes[nav];
        // When the navigation path is changed
        //alert(System.modulesHashes[nav] + " YES " + nav);
      } else if (!this.firstTime) {
        // first time indicates that the page is (re)loaded and the window.location.hash should be set
        // as the module hash value for the module which is specified by app parameter in the hash value.
        // Other modules get default hash value
        System.modulesHashes[nav] = hashValue;
        this.firstTime = true;
        //alert("first time: " + System.modulesHashes[nav] + " " + hashValue);
      } else if (!System.modulesHashes[nav]) {
        // When the module does not exist 
        System.modulesHashes[nav] = "app=" + nav;
        //alert(System.modulesHashes[nav] + " default hash");
      } else if (System.modulesHashes[nav]) {
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
      var newParams = System.utility.clone(parameters);
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
        if (newParams[k] !== null && typeof newParams[k] !== 'undefined') {
          newHash += k + "=" + newParams[k];
          newHash += '&';
          and = true;
          delete newParams[k];
        } else if (!newParams.hasOwnProperty(k) && !clean) {
          newHash += k + "=" + v;
          newHash += '&';
          and = true;
        }
      });
      // New keys
      $.each(newParams, function (key, value) {
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
     * @param {json} module  id: the id of module, url: path to the module
     * @param {callback} onDone
     * @returns {void}
     */
    loadModule: function (module, onDone, parentScope) {
      System.onModuleLoaded["system/" + module.id] = onDone;
      var moduleExist = System.modules["system/" + module.id];

      if (moduleExist) {
        if ("function" === typeof (System.onModuleLoaded["system/" + module.id])) {
          System.onModuleLoaded["system/" + module.id].call(this, moduleExist, moduleExist.html);
          System.onModuleLoaded["system/" + module.id] = null;
        }

        return;
      }

      if (System.onLoadQueue["system/" + module.id]) {
        return;
      }

      System.onLoadQueue["system/" + module.id] = true;

      $.get(module.url, module.params | {}, function (response) {
        if (System.modules["system/" + module.id]) {
          $("#system_" + module.id.replace(/[\/-]/g, "_")).remove();
          //return;
        }

        var filtered = System.parseContent(response);

        setTimeout(function () {
          compile(filtered);
        }, 1);
      });

      function compile(filtered) {
        //if (filtered.script) {
        var scopeUIViews = {};
        Array.prototype.forEach.call(filtered.uiView, function (item) {
          var key = item.getAttribute('name').replace(/([A-Z])|(\-)|(\s)/g, function ($1) {
            return "_" + (/[A-Z]/.test($1) ? $1.toLowerCase() : '');
          });

          scopeUIViews[key] = item;
        });

        var scope = {
          __moduleId: "system/" + module.id,
          parentScope: parentScope,
          uiViews: scopeUIViews,
          ui: filtered.html,
          imports: {}
        };
        var imports = [];
        //var importsOfScope = {};
        var scriptContent = filtered.script || '';

        // extract imports from the source code
        scriptContent = scriptContent.replace(/\/\*[\s\S]*?\*\/|([^:]|^)\/\/.*$/gm, '');
        filtered.script = scriptContent.replace(/Scope\.import\(['|"](.*)['|"]\)\;/gm, function (match, path) {
          imports.push(path);
          return "Scope.imports['" + path + "']";
        });

        //console.log('Libraries to be imported: ', imports);

        if (imports.length) {
          imports.forEach(function (item) {
            if (importedLibraries[item]) {
              //importedLibraries[item];
              imports.splice(imports.indexOf(item), 1);
              doneImporting(module, scope, imports, filtered, importedLibraries);
            } else {
              System.loadModule({
                id: (new Date()).valueOf() + '-' + performance.now(),
                url: item
              }, function (loaded) {
                doneImporting(module, scope, imports, filtered, importedLibraries);
              });
            }
          });

          return false;
        }


        moduleLoaded(module, scope, filtered, importedLibraries);
      }

      function doneImporting(module, scope, imports, filtered, importsOfScope) {
        imports.splice(imports.indexOf(module.url), 1);

        if (imports.length === 0) {
          // This will load the original initilizer
          moduleLoaded(module, scope, filtered, importsOfScope);
        }
      }

      function moduleLoaded(module, scope, filtered, importsOfScope) {
//        if (!module.isDependency && !System.modules["system/" + module.id]) {
//          //alert("Invalid module: " + mod.id);
//          throw new Error('Could not find module: system/' + module.id + ', url: ' + module.url);
//          return false;
//        }

        scope.imports = importsOfScope;
        (new Function('Scope', filtered.script)).call(null, scope);
        importedLibraries[module.url] = scope.export;
        //console.log(scope.export);
        delete scope.export;

        if (!System.modules["system/" + module.id]) {
          System.modules["system/" + module.id] = {};
        }

        System.modules["system/" + module.id].html = filtered ? filtered.html : '';
        System.modules["system/" + module.id].scope = scope;


        if ('function' === typeof (System.onModuleLoaded['system/' + module.id])) {
          System.onModuleLoaded['system/' + module.id].call(this, System.modules['system/' + module.id], filtered.html);
          System.onModuleLoaded['system/' + module.id] = null;
        }

        delete System.onLoadQueue["system/" + module.id];
      }
    },
    parseContent: function (raw) {
      var scripts = null;
      var raw = $(raw);
      //var scripts = raw.filter("script").remove();
      var html = raw.filter(function (i, e) {
        if (e.tagName && e.tagName.toLowerCase() === "script") {
          scripts = e.innerHTML;
          return false;
        }

//        if (e.tagName && e.tagName.toLowerCase() === 'link') {
//          return false;
//        }

        return true;
      });
      var templates = {};
      var temp = document.createElement('div');
      for (var i = 0, len = html.length; i < len; i++) {
        temp.appendChild(html[i]);
      }
      document.getElementsByTagName('body')[0].appendChild(temp);
      var uiView = temp.querySelectorAll('system-ui-view');
      temp.parentNode.removeChild(temp);

      return {
        html: html,
        uiView: uiView,
        script: scripts
      };
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
      this.app.stateKey = this.stateKey;
      this.app.id = "system";
      this.app.installModules = mods;
      this.app.init({}, {}, "");
    }
  };

  System.utility = System.Util = {
    clone: function (source) {
      if (null === source || "object" !== typeof source)
        return source;
      var copy = source.constructor();
      for (var property in source) {
        if (source.hasOwnProperty(property)) {
          copy[property] = source[property];
        }
      }

      return copy;
    },
    extend: function (child, parent) {
      var hasProp = {}.hasOwnProperty;
      for (var key in parent) {
        if (hasProp.call(parent, key))
          child[key] = parent[key];
      }
//      function ctor() {
//        this.constructor = child;
//      }
//      ctor.prototype = parent.prototype;
      //child.prototype = new ctor();
      child.__super__ = parent.prototype;
      return child;
    },
    installModuleStateHandlers: function (module, states) {
      for (var state in states) {
        module.on(state, states[state]);
      }
    },
    getProperty: function (obj, propString) {
      if (!propString)
        return obj;

      var prop, props = propString.split('.');

      for (var i = 0, iLen = props.length - 1; i < iLen; i++) {
        prop = props[i];

        var candidate = obj[prop];
        if (candidate !== undefined) {
          obj = candidate;
        } else {
          break;
        }
      }

      return obj[props[i]];
    }
  };

}());