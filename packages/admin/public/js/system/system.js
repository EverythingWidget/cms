(function () {

  var entities = {};
  var importedLibraries = {};
  System = {
    stateKey: 'app',
    registry: {},
    modules: {},
    activities: {},
    getActivity: function (conf) {
      var _this = this, settings, url, activityId, activityController;
      settings = {
        title: "",
        defaultClass: "btn-primary",
        activity: null
      };

      System.utility.extend(settings, conf);

      url = settings.activity.substr(0, settings.activity.lastIndexOf("_")) || settings.activity;

      if (settings.verb) {
        url += '-' + this.verbs[settings.verb.toLowerCase()];
      }

      if (!_this.activities[url]) {
        console.log("activity does not exist: " + url + "!");
        console.log(_this.activities);
        return null;
      }

      if (url !== settings.activity) {
        _this.activities[settings.activity] = System.utility.extend({}, _this.activities[url]);
      }

      activityId = settings.activity;
      activityController = _this.activities[activityId];

      if (activityController.modalObject) {
        if (settings.modal && settings.modal.class) {
          activityController.modalObject.css("left", "");
          activityController.modalObject.attr("class", "top-pane " + settings.modal.class);
          activityController.modalObject.methods.setCloseButton();
        }

        activityController = System.utility.extend({}, activityController, conf);
        _this.activitySource = activityController;
      }

      activityController = System.utility.extend({}, activityController, conf);
      _this.activities[activityId] = activityController;

      var activityCaller = function (hash, privateParams) {
        var hashParameters = {
          ew_activity: activityId
        };

        // if the activity contains a form then set a main hash parameter
        if (activityController.form) {
          _this.activitySource = activityController;
          activityController.newParams = hash;
          activityController.privateParams = privateParams;

          // 2016-06-12: the `true` can cause issue 
          System.setHashParameters(hashParameters, true);
        }
        // if the activity does not contains any form then set a formless hash parameter
        else {
          //console.log(activityController);
          _this.activitySource = activityController;
          _this.setHashParameters(hashParameters, "FORMLESS_ACTIVITY");
        }
      };

      return activityCaller;
    },
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
        //throw new Error("An entity with this id already exist. Entities can't be overwriten");
        return console.warn("An entity with this id already exist. Entities can't be overwriten");
      }

      entities[entity_id] = entityObject;
      this.addToRegistery(entity_id, entityObject);
    },
    addToRegistery: function (id, item) {
      var groups = id.split('/');
      var registery = System.registry;

      groups.forEach(function (group, index) {
        var groupName = group.replace(/\.?([A-Z]+|[\-]+)/g, function (x, y) {
          return '_' + y.toLowerCase();
        }).replace(/^_|\-/g, '');

        if (!registery[groupName]) {
          registery[groupName] = {};
        }

        if (index === groups.length - 1) {
          registery[groupName] = item;
          return;
        }

        registery = registery[groupName];
      });
    },
    /**
     * 
     * @param {String} id
     * @param {Function|Object} handler
     * @returns {System.MODULE_ABSTRACT}
     */
    state: function (id, handler) {
      //return this.app.module(id, object, false);
      var module, modulePath, moduleNavigation;
      var domain = this;
      if (!domain) {
        throw "Domain can NOT be null";
      }
      id = this.app.id + '/' + id;

      //if forceReload is true, then init the module again
      if (!handler/* && this.modules[id]*/) {
        // Add the module to notYetStarted list so it can be started by startLastLoadedModule method
        domain.notYetStarted.push(id);
        return domain.modules[id];
      }

      if (domain.modules[id]) {
        return domain.modules[id];
      }

      if (typeof (handler) === "function") {
        module = System.utility.extend(true, {}, System.MODULE_ABSTRACT);
        module.domain = domain;
        module.id = id;
        module.stateId = id.replace('system/', '');

        handler.call(null, module);
      } else {
        module = System.utility.extend(true, {}, System.MODULE_ABSTRACT, handler || {});
        module.domain = domain;
        module.id = id;
        module.stateId = id.replace('system/', '');
      }

      modulePath = domain.app.navigation[module.stateKey] ? domain.app.navigation[module.stateKey] : [];
      moduleNavigation = System.utility.extend(true, {}, domain.app.navigation);
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
      var asNumber = parseFloat(this.app.params[key]);
      return asNumber || this.app.params[key] || null;
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
      for (var key in newParams) {
        if (newParams.hasOwnProperty(key)) {
          var value = newParams[key];

          if (key && value) {
            newHash += key + "=" + value + "&";
          }
        }
      }

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
    loadModule: function (module, onDone) {
      module.id = module.id || (new Date()).valueOf() + '-' + performance.now();

      System.onModuleLoaded["system/" + module.id] = onDone;
      var moduleExist = System.modules["system/" + module.id];

      var invokers = [module.url];

      if (module.invokers) {
        if (module.invokers.indexOf(module.url) !== -1) {
          throw new Error('circular dependencies: \n' + module.invokers.join('\n') + '\nwanna load: ' + module.url);
        }

        invokers = module.invokers;
        invokers.push(module.url);
      }

      if (moduleExist) {
        if ('function' === typeof (System.onModuleLoaded["system/" + module.id])) {
          System.onModuleLoaded["system/" + module.id].call(this, moduleExist, moduleExist.html);
          delete System.onModuleLoaded["system/" + module.id];
        }

        return;
      }

      if (System.onLoadQueue["system/" + module.id]) {
        return;
      }

      System.onLoadQueue["system/" + module.id] = true;

      $.get(module.url, module.params | {}, function (response) {
        var parsedContent = System.parseContent(response, module);

        setTimeout(function () {
          compile(parsedContent);
        }, 1);
      });

      function compile(parsedContent) {
        //if (filtered.script) {
        var scopeUIViews = {};
        Array.prototype.forEach.call(parsedContent.uiView, function (item) {
          var key = item.getAttribute('name').replace(/([A-Z])|(\-)|(\s)/g, function ($1) {
            return "_" + (/[A-Z]/.test($1) ? $1.toLowerCase() : '');
          });

          scopeUIViews[key] = item;
        });

        var scope = {
          _moduleId: 'system/' + module.id,
          _stateId: module.id,
          parentScope: module.scope || null,
          uiViews: scopeUIViews,
          ui: parsedContent.html,
          html: parsedContent.html,
          views: scopeUIViews,
          imports: {}
        };

//        console.log(parsedContent.imports);
        var imports = Array.prototype.slice.call(parsedContent.imports, 0);
        //var importsOfScope = {};
        var scriptContent = parsedContent.script || '';

        // extract imports from the source code
        scriptContent = scriptContent.replace(/\/\*[\s\S]*?\*\/|([^:]|^)\/\/.*$/gm, '');
        parsedContent.script = scriptContent.replace(/Scope\.import\(['|"](.*)['|"]\)\;/gm, function (match, path) {
          var query = path.match(/([\S]+)/gm);
          imports.push({
            url: query[query.length - 1],
            fresh: query.indexOf('new') !== -1
          });

          return "Scope.imports['" + query[query.length - 1] + "']";
        });

//       console.log('Libraries to be imported: ', JSON.stringify(imports));

        if (imports.length) {
          imports.forEach(function (item) {
            if (importedLibraries[item.url] && !item.fresh) {
              doneImporting(module, scope, imports, parsedContent);
            } else {
              System.loadModule({
                id: (new Date()).valueOf() + '-' + performance.now(),
                name: item.name,
                url: item.url,
                fresh: item.fresh,
                scope: scope,
                invokers: invokers,
                temprory: true
              }, function (loaded) {
                doneImporting(module, scope, imports, parsedContent);
              });
            }
          });

          return false;
        }

        moduleLoaded(module, scope, parsedContent);
      }

      function doneImporting(module, scope, imports, filtered) {
        imports.splice(imports.indexOf(module.url), 1);

        if (imports.length === 0) {
          // This will load the original initilizer
          moduleLoaded(module, scope, filtered);
        }
      }

      function moduleLoaded(module, scope, filtered) {
//        if (!module.isDependency && !System.modules["system/" + module.id]) {
//          //alert("Invalid module: " + mod.id);
//          throw new Error('Could not find module: system/' + module.id + ', url: ' + module.url);
//          return false;
//        }

        /*if (importedLibraries[module.url] && !module.fresh) {
         scope.imports[module.name] = importedLibraries[module.url].module;
         
         } else {*/

        for (var item in importedLibraries) {
          if (importedLibraries.hasOwnProperty(item)) {
            var asset = importedLibraries[item];
            scope.imports[asset.name] = asset.module;
          }
        }

        (new Function('Scope', filtered.script)).call(null, scope);

        if (!importedLibraries[module.url]) {
          importedLibraries[module.url] = {
            name: module.name || module.url,
            module: scope.export
          };
        } else if (module.fresh) {
          importedLibraries[module.url].module = scope.export;
        } else {
          scope.imports[module.name] = importedLibraries[module.url].module;
        }

        delete scope.export;
        //}

        var currentModule = System.modules['system/' + module.id];

        if (module.temprory) {
          currentModule = {};
        } else if (!currentModule) {
          currentModule = System.modules['system/' + module.id] = {};
        }

        currentModule.html = filtered.html;
        currentModule.scope = scope;

        if ('function' === typeof (System.onModuleLoaded['system/' + module.id])) {
          System.onModuleLoaded['system/' + module.id].call(this, currentModule, currentModule.html);
          delete System.onModuleLoaded['system/' + module.id];
        }

        delete System.onLoadQueue['system/' + module.id];
      }
    },
    parseContent: function (raw, module) {
      var scripts = null;
      var imports = [];
      if (!System.utility.isHTML(raw)) {
        console.log('Resource is not a valid html file:', module.url);

        return {
          html: [],
          imports: [],
          uiView: [],
          script: []
        };
      }
      var raw = $(raw);
      //var scripts = raw.filter("script").remove();
      var html = raw.filter(function (i, e) {
        if (e.tagName && e.tagName.toLowerCase() === 'script') {
          scripts = e.innerHTML;
          return false;
        }

        if (e.tagName && e.tagName.toLowerCase() === 'import') {
          imports.push({
            name: e.getAttribute('name'),
            from: e.getAttribute('from'),
            fresh: e.hasAttribute('fresh')
          });

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
        html[i] = temp.appendChild(html[i]);
      }
      document.getElementsByTagName('body')[0].appendChild(temp);
      var uiView = temp.querySelectorAll('system-ui-view');
      temp.parentNode.removeChild(temp);

      return {
        html: html,
        imports: imports,
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
      this.app = System.utility.extend(true, {}, System.MODULE_ABSTRACT);
      this.app.domain = this;
      this.app.stateKey = this.stateKey;
      this.app.id = "system";
      this.app.installModules = mods;
      this.app.init({}, {}, "");
    }
  };

  System.utility = {
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
    extend: function (out) {
      var isDeep = false;
      //out = out || {};

      if (out === true) {
        isDeep = true;
        out = {};
      }

      for (var i = 1; i < arguments.length; i++) {
        var obj = arguments[i];

        if (!obj)
          continue;

        for (var key in obj) {
          if (obj.hasOwnProperty(key)) {
            if (typeof obj[key] === 'object' && isDeep) {
              if (Array.isArray(obj[key])) {
                out[key] = System.utility.extend([], obj[key]);
              } else {
                out[key] = System.utility.extend({}, obj[key]);
              }
            } else {
              out[key] = obj[key];
            }
          }
        }
      }

      return out;
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
    },
    isHTML: function (str) {
      var element = document.createElement('div');
      element.innerHTML = str;
      for (var c = element.childNodes, i = c.length; i--; ) {
        if (c[i].nodeType === 1)
          return true;
      }
      return false;
    },
    decorate: function (hostObject) {
      return {
        'with': function (behavior) {
          Array.prototype.unshift.call(arguments, hostObject);
          return behavior.apply(null, arguments);
        }
      }
    },
    withHost: function (hostObject) {
      return {
        behave: function (behavior) {
          if (typeof behavior !== 'function') {
            throw 'Behavior is not a function: ' + behavior;
          }

          return function () {
            Array.prototype.unshift.call(arguments, hostObject);

            return behavior.apply(null, arguments);
          };
        }
      };
    },
    isNumber: function (o) {
      return !isNaN(o - 0) && o !== null && o !== "" && o !== false;
    }
  };

//  window.System = System;

  //exports.System = System;
}());

