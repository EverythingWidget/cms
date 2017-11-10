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
    newStateHandler: function (scope, handler) {
      var app = this.getHashParam('app');

//      console.log('new state', app, scope._stateId, app.indexOf(scope._stateId));
      if (app.indexOf(scope._stateId) === 0) {
        return this.state(scope._stateId, handler);
      } else {
        scope._doNotRegister = true;
      }

      return null;
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
      var promise = $.get(href, function (response) {
        if ("function" === typeof (onDone)) {
          onDone.call(this, response);
        }
      });
      return this.addActiveRequest(promise);
    },
    /**
     * 
     * @param {json} module  id: the id of module, url: path to the module
     * @param {callback} onDone
     * @returns {void}
     */
    loadModule: function (module, onDone) {
      module.id = module.id || (new Date()).valueOf() + '-' + performance.now();

      System.onModuleLoaded['system/' + module.id] = onDone;
      var moduleExist = System.modules['system/' + module.id];

      var invokers = [module.url];

      if (module.invokers) {
        if (module.invokers.indexOf(module.url) !== -1) {
          throw new Error('circular dependencies: \n' + module.invokers.join('\n') + '\nwanna load: ' + module.url);
        }

        invokers = module.invokers;
        invokers.push(module.url);
      }

      if (moduleExist) {
//        console.log(module.id);
        if ('function' === typeof (System.onModuleLoaded['system/' + module.id])) {
          System.onModuleLoaded['system/' + module.id].call(this, moduleExist, moduleExist.html);
          delete System.onModuleLoaded['system/' + module.id];
        }

        return;
      }

      if (System.onLoadQueue["system/" + module.id]) {
        return;
      }

      System.onLoadQueue["system/" + module.id] = true;

      $.get(module.url, module.params || {}, function (response) {
        var parsedContent = System.parseContent(response, module);

        setTimeout(function () {
          compile(parsedContent);
        }, 1);
      });

      function compile(parsedContent) {
        var scopeUIViews = {};
        Array.prototype.forEach.call(parsedContent.uiView, function (item) {
          var uiViewName = item.getAttribute('system-ui-view') || item.getAttribute('name');
          var key = uiViewName.replace(/([A-Z])|(\-)|(\s)/g, function ($1) {
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

//        delete scope.export;

        var currentModule = System.modules['system/' + module.id];

        if (module.temprory || scope._doNotRegister) {
//          console.log('do not register', module.id);
          delete scope._doNotRegister;
          currentModule = {};
        } else if (!currentModule) {
//          console.log('empty', module.id);
          currentModule = System.modules['system/' + module.id] = {};
        }

        currentModule.html = filtered.html;
        currentModule.scope = scope;

        if ('function' === typeof (System.onModuleLoaded['system/' + module.id])) {
          //console.log('immidiate load: ', currentModule, System.onModuleLoaded);
          System.onModuleLoaded['system/' + module.id].call(this, currentModule, currentModule.html);
          delete System.onModuleLoaded['system/' + module.id];
        }

        delete System.onLoadQueue['system/' + module.id];
      }
    },
    parseContent: function (raw, module) {
      var scripts = [];
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
        if (e.nodeType === Node.ELEMENT_NODE) {
          var scriptTags = Array.prototype.slice.call(e.querySelectorAll('script'));
          scriptTags.forEach(function (tag) {
            scripts.push(tag.innerHTML);
            tag.parentNode.removeChild(tag);
          });
        }

        if (e.tagName && e.tagName.toLowerCase() === 'script') {
          scripts.push(e.innerHTML);
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
      var uiView = temp.querySelectorAll('system-ui-view,[system-ui-view]');
      temp.parentNode.removeChild(temp);

      return {
        html: html,
        imports: imports,
        uiView: uiView,
        script: scripts.join('\n')
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
}());


/* global TweenLite, Vue, System */

(function () {
  Vue.transition('slide', {
    beforeEnter: function () {
    },
    enter: function (el, done) {
      el.className += ' slide-out';
      TweenLite.to(el, .3, {
        className: '-=slide-out',
        onComplete: function () {
          done();
        }
      });
    },
    afterEnter: function () {
    },
    enterCancelled: function () {
      // handle cancellation
    },
    beforeLeave: function () {
    },
    leave: function (el, done) {
      TweenLite.to(el, .3, {
        className: '+=slide-out',
        ease: 'Power2.easeInOut',
        onComplete: function () {
          done();
        }
      });
    },
    afterLeave: function () {
    },
    leaveCancelled: function () {
      // handle cancellation
    }
  });

  Vue.transition('slide-vertical', {
    enter: function (element, done) {
      element.className += ' trans-slide-vertical';
      TweenLite.to(element, .3, {
        className: '-=trans-slide-vertical',
        onComplete: function () {
          done();
        }
      });
    },
    leave: function (element, done) {
      TweenLite.to(element, .3, {
        className: '+=trans-slide-vertical',
        ease: 'Power2.easeInOut',
        onComplete: function () {
          done();
        }
      });
    }
  });

  Vue.transition('in', {
    css: false,
    enter: function (element, done) {
      var timeline = new TimelineLite({});
      timeline.fromTo(element, .6, {
        opacity: 0,
        y: 50
//        z: -550,
//        rotationX: -22,
//        transformOrigin: 'center center'
      }, {
        opacity: 1,
        y: 0,
//        rotationX: 0,
//        z: 0,
        ease: 'Power3.easeOut',
        clearProps: 'y',
        onComplete: function () {
          done();
        }
      });
    },
    leave: function (element, done) {
      done();
    }
  });
})();

(function (TweenLite) {
  System.spiritAnimations = {
    CONFIG: {
      baseDuration: 0.4,
      staggerDuration: 0.04
    }
  };

  // spirit animations should follow the service pattern, so the animation is a singleton object which is
  // responsible for registering elements and managing their animations
  System.spiritAnimations.liveHeight = {
    register: function (element) {
      new LiveHeightAnimation(element);
    },
    deregister: function (element) {
      if (element.xtag.liveHeightAnimation) {
        element.xtag.liveHeightAnimation.off();
        element.xtag.liveHeightAnimation = null;
      }
    }
  };

  function LiveHeightAnimation(element) {
    var _this = this;
    _this.element = element;
    _this.element.xtag.liveHeightAnimation = this;

    if (!_this.observer) {
      _this.observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (item) {
          if (item.addedNodes[0] && item.addedNodes[0].__ui_neutral) {
            return null;
          }

          if (item.removedNodes[0] && item.removedNodes[0].__ui_neutral) {
            return null;
          }

          _this.animate();
        });
      });

      window.requestAnimationFrame(function () {
        if (!_this.observer) {
          return;
        }

        _this.height = _this.element.offsetHeight;
        TweenLite.set(_this.element, {
          height: _this.height
        });

        _this.observer.observe(_this.element, {
          attributes: false,
          childList: true,
          characterData: false,
          subtree: true
        });

        _this.animate();
      });

      _this.resizeHandler = function () {
        _this.animate();
      };
      window.addEventListener('resize', _this.resizeHandler);
    }
  }

  LiveHeightAnimation.prototype.off = function () {
    clearTimeout(this.animationThrottle);
    if (this.observer) {
      this.observer.disconnect();
      this.observer = null;

      window.removeEventListener('resize', this.resizeHandler);
      TweenLite.set(this.element, {
        height: ''
      });
    }
  };

  LiveHeightAnimation.prototype.animate = function () {
    var _this = this;
    clearTimeout(_this.animationThrottle);
    _this.animationThrottle = setTimeout(function () {
      if (_this.animation) {
        _this.animation.pause();
        _this.animation = null;
      }

      var newHeight = System.ui.utility.getContentHeight(_this.element, true);

      if (_this.height !== newHeight) {
        _this.animation = TweenLite.fromTo(_this.element, System.spiritAnimations.CONFIG.baseDuration, {
          height: _this.height
        }, {
          height: _this.element.offsetParent ? newHeight : '',
          clearProps: newHeight === 0 ? 'height' : '',
          ease: 'Power2.easeInOut',
          onComplete: function () {
            _this.height = newHeight;
            _this.animation = null;
          }
        });
      }
    }, 250);
  };

  // ------ //

  System.spiritAnimations.zoom = {
    register: function (element) {
      new ZoomInAnimation(element);
    },
    deregister: function (element) {
      if (element.xtag.PopInAnimation) {
        element.xtag.PopInAnimation.off();
      }
    }
  };

  function ZoomInAnimation(element) {
    var _this = this;
    _this.element = element;
    _this.zoomItem = element.getAttribute('zoom');

    if (!this.observer) {
      var existedNodes = element.querySelectorAll('.' + _this.zoomItem);
      _this.animate(existedNodes);

      _this.observer = new MutationObserver(function (mutations) {
        _this.stagger = 0;
        var nodes = [];

        mutations.forEach(function (item) {
          var node = null;
          if (item.addedNodes[0]) {

            if (item.addedNodes[0].__ui_neutral ||
                item.addedNodes[0].nodeType !== Node.ELEMENT_NODE || !item.addedNodes[0].classList.contains(_this.zoomItem))
              return null;

            node = item.addedNodes[0];
          }

          if (item.removedNodes[0] && item.removedNodes[0].__ui_neutral) {
            return null;
          }

          node && nodes.push(node);
        });

        _this.animate(nodes);
      });

      _this.observer.observe(_this.element, {
        attributes: false,
        childList: true,
        characterData: false,
        subtree: true
      });
    }

    _this.element.xtag.PopInAnimation = this;
  }

  ZoomInAnimation.prototype.off = function () {
    if (this.observer) {
      this.observer.disconnect();
    }
  };

  ZoomInAnimation.prototype.animate = function (nodes) {
    if (!nodes.length) {
      return;
    }

    var timelineItems = [];
    var timeline = new TimelineLite({
      paused: true,
      smoothChildTiming: true,
      onComplete: function () {
      }
    });

    nodes.forEach(function (element) {
      TweenLite.set(element, {
        transition: 'none',
        scale: element.dataset.zoomFrom || 0.01,
        opacity: 0
      });

      timelineItems.push(TweenLite.to(element, System.spiritAnimations.CONFIG.baseDuration, {
        scale: 1,
        opacity: 1,
        clearProps: 'transition,scale,opacity',
        ease: 'Power3.easeOut',
        onComplete: function () {
        }
      }));
    });

    timeline.add(timelineItems, null, null, System.spiritAnimations.CONFIG.staggerDuration);

    timeline.play(0);
  };

  // ------ //

  System.spiritAnimations.zoomContent = {
    register: function (element) {
      new ZoomContentAnimation(element);
    },
    deregister: function (element) {
      if (element.xtag.ZoomContentAnimation) {
        element.xtag.ZoomContentAnimation.off();
      }
    }
  };

  function ZoomContentAnimation(element) {
    var _this = this;
    _this.element = element;

    if (!this.observer) {
      var existedNodes = Array.prototype.slice.call(element.children);
      _this.animate(existedNodes);

      _this.observer = new MutationObserver(function (mutations) {
        _this.stagger = 0;
        var nodes = [];

        mutations.forEach(function (item) {
          var node = null;
          if (item.addedNodes[0]) {

            if (item.addedNodes[0].__ui_neutral ||
                item.addedNodes[0].nodeType !== Node.ELEMENT_NODE)
              return null;

            node = item.addedNodes[0];
          }

          if (item.removedNodes[0] && item.removedNodes[0].__ui_neutral) {
            return null;
          }

          node && nodes.push(node);
        });

        _this.animate(nodes);
      });

      _this.observer.observe(_this.element, {
        attributes: false,
        childList: true,
        characterData: false,
        subtree: true
      });
    }

    _this.element.xtag.ZoomContentAnimation = this;
  }

  ZoomContentAnimation.prototype.off = function () {
    if (this.observer) {
      this.observer.disconnect();
    }
  };

  ZoomContentAnimation.prototype.animate = function (nodes, style) {
    if (!nodes.length) {
      return;
    }

    var timelineItems = [];
    var timeline = new TimelineLite({
      paused: true,
      smoothChildTiming: true,
      onComplete: function () {
      }
    });

    nodes.forEach(function (element) {
      TweenLite.set(element, {
        transition: 'none',
        scale: element.dataset.zoomFrom || 0.8,
        opacity: 0
      });

      timelineItems.push(TweenLite.to(element, System.spiritAnimations.CONFIG.baseDuration, {
        scale: 1,
        opacity: 1,
        clearProps: 'transition,scale,opacity',
        ease: 'Power3.easeOut',
        onComplete: function () {
        }
      }));
    });

    timeline.add(timelineItems, null, null);

    timeline.play(0);
  };

  // ------ //

  System.spiritAnimations.inOut = {
    register: function (element) {
      new InOutAnimation(element);
    },
    deregister: function (element) {
      if (element.xtag.InOutAnimation) {
        element.xtag.InOutAnimation.off();
      }
    }
  };

  function InOutAnimation(element) {
    var _this = this;
    _this.element = element;
    _this.item = element.getAttribute('in-out-item');
    _this.class = element.getAttribute('in-out-class');

    if (!this.observer) {
      _this.observer = new MutationObserver(function (mutations) {
        _this.stagger = 0;
        var nodesIn = [];
        var nodesOut = [];

        mutations.forEach(function (item) {
          var node = null;

          if (item.addedNodes[0]) {
            if (item.addedNodes[0].__ui_neutral || item.addedNodes[0].__in_out ||
                item.addedNodes[0].nodeType !== Node.ELEMENT_NODE || !item.addedNodes[0].classList.contains(_this.item))
              return null;

            node = item.addedNodes[0];
            nodesIn.push(node);
          }

          if (item.removedNodes[0] && (item.removedNodes[0].__ui_neutral || item.removedNodes[0].__in_out)) {
            //item.removedNodes[0].__in_out = false;
            return null;
          }

          if (item.removedNodes[0] &&
              item.removedNodes[0].nodeType === Node.ELEMENT_NODE &&
              item.removedNodes[0].classList.contains(_this.item)) {
            node = item.removedNodes[0];
            node.__in_out = true;
            item.target.insertBefore(node, item.previousSibling.nextSibling);

            nodesOut.push(node);
          }
        });

        if (nodesOut.length) {
          _this.animate(nodesOut, 'out');
        }

        if (nodesIn.length) {
          _this.animate(nodesIn, 'in');
        }
      });

      _this.observer.observe(_this.element, {
        attributes: false,
        childList: true,
        characterData: false,
        subtree: true
      });
    }

    _this.element.xtag.PopInAnimation = this;
  }

  InOutAnimation.prototype.off = function () {
    if (this.observer) {
      this.observer.disconnect();
    }
  };

  InOutAnimation.prototype.animate = function (nodes, style) {
    var _this = this;

    if (!nodes.length || !_this.class) {
      return;
    }

    _this.timelineItems = [];
    var timeline = new TimelineLite({
      paused: true,
      smoothChildTiming: true,
      onComplete: function () {
        _this.timeline = null;
      }
    });

    if (style === 'in') {
      TweenLite.set(nodes, {
        transition: '',
        className: '+=' + _this.class,
        immediateRender: true
      });

      nodes.forEach(function (element) {
        _this.timelineItems.push(TweenLite.to(element, .4, {
          className: '-=' + _this.class,
          ease: 'Power3.easeOut',
          onComplete: function () {
          }
        }));
      });

      timeline.add(_this.timelineItems, null, null, 0.2);
    } else {
      TweenLite.set(nodes, {
        className: '-=' + _this.class
      });

      nodes.forEach(function (element) {
        _this.timelineItems.push(TweenLite.to(element, .4, {
          className: '+=' + _this.class,
          ease: 'Power3.easeOut',
          onComplete: function () {
            element.parentNode && element.parentNode.removeChild(element);
          }
        }));
      });

      timeline.add(_this.timelineItems, null, null);
    }

    timeline.play(0);
  };

  // ------ //

  System.spiritAnimations.verticalShift = {
    register: function (element) {
      new verticalShift(element);
    },
    deregister: function (element) {
      if (element.xtag.verticalShift) {
        element.xtag.verticalShift.off();
      }
    }
  };

  function verticalShift(element) {
    var _this = this;

    _this.allNodes = [];
    _this.element = element;
    _this.verticalShiftItem = element.getAttribute('vertical-shift');

    if (!this.observer) {
      _this.observer = new MutationObserver(function (mutations) {
        _this.stagger = 0;
        var nodes = [];

        mutations.forEach(function (item) {
          var node = null;
          if (item.addedNodes[0]) {
            if (item.addedNodes[0].__ui_neutral ||
                item.addedNodes[0].nodeType !== Node.ELEMENT_NODE || !item.addedNodes[0].classList.contains(_this.verticalShiftItem))
              return null;

            node = item.addedNodes[0];
          }

          if (item.removedNodes[0] && item.removedNodes[0].__ui_neutral) {
            return null;
          }

          node && nodes.push(node);
        });

        _this.animate(nodes);
      });

      _this.observer.observe(_this.element, {
        attributes: false,
        childList: true,
        characterData: false,
        subtree: true
      });
    }

    _this.element.xtag.verticalShift = this;
  }

  verticalShift.prototype.off = function () {
    if (this.observer) {
      this.observer.disconnect();
    }
  };

  verticalShift.prototype.animate = function (nodes) {
    if (!nodes.length) {
      return;
    }

    var _this = this;

    if (!_this.throttle) {
      _this.allNodes = nodes;
    } else {
      clearTimeout(_this.throttle);
      _this.allNodes = _this.allNodes.concat(nodes);
      _this.throttle = null;
    }

    _this.allNodes.forEach(function (element) {
      TweenLite.set(element, {
        transition: 'none',
        y: 50,
        opacity: 0
      });
    });

    _this.throttle = setTimeout(function () {
      var timelineItems = [];
      var timeline = new TimelineLite({
        paused: true,
        smoothChildTiming: true,
        onComplete: function () {
        }
      });

      _this.allNodes.forEach(function (element) {
        timelineItems.push(TweenLite.to(element, System.spiritAnimations.CONFIG.baseDuration, {
          opacity: 1,
          y: 0,
          clearProps: 'transition,y,opacity',
          ease: 'Power3.easeOut',
          onComplete: function () {
            _this.timeline = null;
          }
        }));
      });

      timeline.add(timelineItems, null, null, System.spiritAnimations.CONFIG.staggerDuration);

      timeline.play(0);
      _this.throttle = null;
    }, 100);
  };

})(TweenLite);

(function (System) {
  function Domain(hashString) {
    this.ui = {};
    this.app = null;
    this.domainHashString = hashString || '';
    this.stateKey = "app";
    this.modules = {};
    this.modulesHashes = {};
    this.appPathfiledName = null;
    this.activityTree = [
    ];
    this.onLoadQueue = [
    ];
    this.notYetStarted = [
    ];
    this.activeRequests = {};
    this.onModuleLoaded = {};
  }

  Domain.prototype.setModuleHashValue = function (navigation, parameters, hashValue, init) {
    var nav = parameters["app"];

    if (nav
            && this.modulesHashes[nav]
            && this.app.activeModule !== this.modules["system/" + nav]) {
      // window.location.hash = System.modulesHashes[nav];
      // When the navigation path is changed
      //alert(System.modulesHashes[nav] + " YES " + nav);
    } else if (nav && !this.firstTime) {
      // first time indicates that the page is (re)loaded and the window.location.hash should be set
      // as the module hash value for the module which is specified by app parameter in the hash value.
      // Other modules get default hash value
      this.modulesHashes[nav] = hashValue;
      this.firstTime = true;
      //alert("first time: " + this.modulesHashes[nav] + " " + hashValue);
    } else if (nav && !this.modulesHashes[nav]) {
      // When the module does not exist 
      this.modulesHashes[nav] = "app=" + nav;
      //alert(System.modulesHashes[nav] + " default hash");
    } else if (nav && this.modulesHashes[nav]) {
      // When the hash parameters value is changed from the browser url bar or originated from url bar
      this.modulesHashes[nav] = hashValue;
    }
  };

  Domain.prototype.init = function (mods) {
    this.app = $.extend(true, {}, System.MODULE_ABSTRACT);
    this.app.domain = this;
    this.app.stateKey = this.stateKey;
    this.app.id = "system";
    this.app.installModules = mods || [];
    this.app.init({}, {}, "");
  };

  Domain.prototype.start = function () {
    var _this = this;
    var detect = function () {
      if (_this.app.oldHash !== _this.domainHashString) {
        var hashValue = _this.domainHashString,
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
  };

  Domain.prototype.destroy = function () {
    if (this.hashChecker) {
      clearInterval(this.hashChecker);
    }

    this.modules = null;
  };

  /** Set parameters for app/nav. if app/nav was not in parameters, then set paraters for current app/nav
   * 
   * @param {type} parameters
   * @param {type} replace if true it overwrites last url history otherwise it create new url history
   * @param {type} clean clean all the existing parameters
   * @returns {undefined}
   */
  Domain.prototype.setHashParameters = function (parameters, replace, clean) {
    this.lastHashParams = parameters;
    var hashValue = this.domainHashString;
    var nav = parameters["app"];
    if (nav && !this.modulesHashes[nav]) {
      //console.log(hashValue, nav)
      this.modulesHashes[nav] = hashValue = "app=" + nav;

    } else if (nav && this.modulesHashes[nav]) {
      //console.log(hashValue, nav , System.modulesHashes[nav]);
      hashValue = this.modulesHashes[nav];
    }
    //console.log(parameters, nav, System.modulesHashes[nav]);

    if (hashValue.indexOf("#") !== -1) {
      hashValue = hashValue.substring(1);
    }
    //var pairs = hashValue.split("&");
    var newHash = "#";
    //var and = false;
    hashValue.replace(/([^&]*)=([^&]*)/g, function (m, k, v) {
      if (parameters[k] != null) {
        newHash += k + "=" + parameters[k];
        newHash += '&';
        //and = true;
        delete parameters[k];
      } else if (!parameters.hasOwnProperty(k) && !clean) {
        newHash += k + "=" + v;
        newHash += '&';
        //and = true;
      }
    });
    // New keys
    $.each(parameters, function (key, value) {
      if (key && value) {
        newHash += key + "=" + value + "&";
        //and = true;
      }
    });
    newHash = newHash.replace(/\&$/, '');

    /*if (replace) {
     window.location.replace(('' + window.location).split('#')[0] + newHash);
     } else {
     window.location.hash = newHash.replace(/\&$/, '');
     }*/

    this.domainHashString = newHash.replace(/\&$/, '');
  };

  Domain.prototype.getHashParam = function (key, hashName) {
    return this.app.params[key] || null;
  };

  Domain.prototype.getHashNav = function (key, hashName) {
    return this.app.navigation[key] || [];
  };

  Domain.prototype.state = function (id, decorator) {
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
      module.domain = domain;
      module.id = id;
      module.stateId = id.replace('system/', '');

      decorator.call(null, module);
    } else {
      module = $.extend(true, {}, System.MODULE_ABSTRACT, decorator || {});
      module.domain = domain;
      module.id = id;
      module.stateId = id.replace('system/', '');
    }

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
  };

  System.Domain = Domain;
})(System);
/* global System */

(function () {
  System.MODULE_ABSTRACT = {
    domain: null,
    inited: false,
    started: false,
    active: false,
    stateKey: 'app',
    navigation: {},
    params: {},
    html: "",
    installModules: [],
    binds: {},
    installModulesOnInit: function (modules) {
      this.installModules = modules;
    },
    onInit: null,
    onStart: null,
    onStop: null,
    init: function (navigations, params, html) {
      var _this = this;
      this.inited = true;
      this.trigger('onInit');
      //this.triggerEvent('init');

      this.installModules.forEach(function (lib) {
        _this.domain.loadModule(lib);
      });
    },
    start: function () {
      this.started = true;
      this.active = true;
      this.trigger('onStart');
      //this.triggerEvent('start');
      if (('system/' + this.domain.app.params[this.stateKey]).indexOf(this.id) <= -1) {
        console.log(this.domain.app.params[this.stateKey]);
        throw new Error('Could not find module `' + this.id + '` by state key `' + this.stateKey + '`');
      }
      var newNav = $.extend(true, {}, this.domain.app.navigation);
      var st = 'system/' + this.domain.app.params[this.stateKey];
      var napPath = st.indexOf(this.id) === 0 ? st.substr(this.id.length).split('/').filter(Boolean) : [];

      newNav[this.stateKey] = napPath;
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
      this.hashChanged(nav, params, this.hash, this.domain.getHashNav(this.stateKey));
      //console.log(this.id , )

      var index = this.domain.notYetStarted.indexOf(this.id);
      if (index > -1) {
        this.domain.notYetStarted.splice(index, 1);
      }
    },
    dispose: function () {
    },
    hashListeners: [],
    globalHashListeners: [],
    data: {},
    /** Register an state hanlder with the specified id
     * 
     * @param {String} id
     * @param {Function} handler
     */
    on: function (id, handler) {
      this.hashListeners.push({id: id, handler: handler});
    },
    /** Register an state handler globaly with the specified id.
     * Global state handlers will be called even if the mudole is not active
     * 
     * @param {String} id
     * @param {Function} handler
     */
    onGlobal: function (id, handler) {
      this.globalHashListeners.push({id: id, handler: handler});
    },
    getNav: function (key) {
      return this.domain.getHashNav(key);
    },
    setNav: function (value, key) {
      var pathKey = key || 'app';
      var pathValue = value === null || value === undefined ? '' : value;

      this.setParam(pathKey, (this.id + '/').replace('system/', '') + pathValue);
    },
    getParam: function (key) {
      return this.domain.getHashParam(key);
    },
    /**
     * 
     * @param {string} key Name of the parameter
     * @param {string} value Value of the parameter
     * @param {boolean} replace
     */
    setParam: function (key, value, replace) {
      var paramObject = {};
      paramObject[key] = value;
      this.domain.setHashParameters(paramObject, replace);
    },
    setParamIfNull: function (param, value) {
      if (!this.domain.getHashParam(param)) {
        var paramObject = {};
        paramObject[param] = value;
        this.domain.setHashParameters(paramObject, true);
      }
    },
    setParamIfNot: function (param, value) {
      if (this.domain.getHashParam(param) !== value) {
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
        this.binds[event] = action;
      }
    },
    stage: function (event, action) {
      if ('string' === typeof (event) && 'function' === typeof (action)) {
        this.binds[event] = action;
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
      if (typeof (this[event]) === 'function') {
        this[event].apply(this, args);
      }
    },
    hashChanged: function (navigation, params, hashValue, fullNav) {
      var _this = this;
      var moduleNavigation = navigation;

      var fullNavPath = params[_this.stateKey];

      if (this.id === 'system/' + fullNavPath/* && System.app.activeModule !== this*/) {
        this.domain.app.activeModule = this;
        this.domain.app.activeModule.active = true;
      } else if (!this.solo) {
        if (this.domain.app.activeModule && this.domain.app.activeModule.active) {
          this.domain.app.activeModule.trigger('onStop');
        }
        this.domain.app.activeModule = null;
        this.active = false;
      }

      this.hashHandler.call(this, navigation, params);
      var allNavigations = $.extend({}, this.navigation, navigation);

      var tempNav = _this.navigation;

      _this.navigation = navigation;
      _this.params = params;

      if (this.domain.app.activeModule && this.active && this.domain.app.activeModule.id === _this.id) {
        for (var key in allNavigations) {
          if (allNavigations.hasOwnProperty(key)) {
            var stateHandlers = _this.hashListeners.filter(function (item) {
              return item.id === key;
            });

            if (stateHandlers.length) {
              if (tempNav[key]) {
                var currentKeyValue = tempNav[key].join('/');
                if (navigation[key] && currentKeyValue === navigation[key].join('/')) {
                  continue;
                }
              }

              var parameters = [];
              parameters.push(null);
              var navigationValue = navigation[key];
              if (navigationValue) {
                parameters[0] = navigationValue.join('/');
                for (var i = 0; i < navigationValue.length; i++) {
                  var arg = System.utility.isNumber(navigationValue[i]) ? parseFloat(navigationValue[i]) : navigationValue[i];

                  parameters.push(arg);
                }
              }

              stateHandlers.forEach(function (item) {
                item.handler.apply(_this, parameters);
              });
            }
          }
        }
      } else if (!this.active) {
        var keyStateHandlers = _this.hashListeners.filter(function (item) {
          return item.id === _this.stateKey;
        });
        
        var stateKeyNavigationValue = navigation[_this.stateKey];

        //if navHandler is null call sub module navHandler
        if (keyStateHandlers.length && stateKeyNavigationValue) {
          var currentKeyValue = tempNav[_this.stateKey] ? tempNav[_this.stateKey].join('/') : [];

          if (currentKeyValue !== stateKeyNavigationValue.join('/')) {
            var args = [];
            args.push(stateKeyNavigationValue);

            for (var i = 0, len = stateKeyNavigationValue.length; i < len; ++i) {
              //i is always valid index in the arguments object
              args.push(stateKeyNavigationValue[i]);
            }

            keyStateHandlers.forEach(function (item) {
              item.handler.apply(_this, args);
            });
          }
        }
      }

      for (var key in allNavigations) {
        if (allNavigations.hasOwnProperty(key)) {
          var globalStateHandlers = _this.globalHashListeners.filter(function (item) {
            return item.id === key;
          });

          if (globalStateHandlers.length) {
            if (tempNav[key]) {
              var currentKeyValue = tempNav[key].join('/');
              if (navigation[key] && currentKeyValue === navigation[key].join('/')) {
                continue;
              }
            }

            parameters = [];
            parameters.push(null);

            navigationValue = navigation[key];
            if (navigationValue) {
              parameters[0] = navigationValue.join('/');
              for (var i = 0; i < navigationValue.length; i++) {
                var arg = System.utility.isNumber(navigationValue[i]) ? parseFloat(navigationValue[i]) : navigationValue[i];

                parameters.push(arg);
              }
            }

            globalStateHandlers.forEach(function (item) {
              item.handler.apply(_this, parameters);
            });
          }
        }
      }

      //if (this.stateKey && navigation[this.stateKey] && navigation[this.stateKey][0])
      //{
      // Set the app.activeModule according to the current navigation path
      if (navigation[this.stateKey] && this.domain.modules[this.id + '/' + navigation[this.stateKey][0]]) {
        this.activeModule = this.domain.modules[this.id + '/' + navigation[this.stateKey][0]];
      }
      //} else if (!this.solo) {
      //this.activeModule = null;
      //}

      if (this.activeModule && this.activeModule.id === this.id + '/' + navigation[this.stateKey][0])
      {
        // Remove first part of navigation in order to force activeModule to only react to events at its level and higher 
        moduleNavigation = $.extend(true, {}, navigation);
        moduleNavigation[this.stateKey] = fullNav.slice(this.activeModule.id.split('/').length - 1);
        // Call module level events handlers
        this.activeModule.hashChanged(moduleNavigation, this.params, hashValue, fullNav);
      }
    },
    loadModule: function (module, onDone) {
      System.loadModule(module, onDone, this.scope);
    },
    hashHandler: function (nav, params) {}
  };

})();
/* global System, TweenLite */

(function (tween) {
  System.ui = new SystemUI();

  /**
   * System ui
   */
  function SystemUI() {
    this.DEFAULTS = {
      animationDuration: 1
    };
    this.COMPONENT_STRUCTURE = {
      el: null,
      events: {},
      on: function (event, handler) {
        this.events[event] = handler;
      },
      trigger: function (event) {
        if (this.events[event])
          this.events[event].apply(this, Array.prototype.slice.call(arguments, 1));
      }
    };
    this.body = document.getElementsByTagName('body')[0];
    this.components = {
      body: this.body
    };
    this.forms = {};
    this.elements = {};
    this.containers = {};
    this.templates = {};
    this.behaviors = {};
  }

  SystemUI.prototype.utility = {
    viewRegex: /\{\{([^\{\}]*)\}\}/g
  };

  // Simply replace {{key}} with its value in the template string and returns it
  SystemUI.prototype.utility.populate = function (template, data) {
    template = template.replace(this.viewRegex, function (match, key) {
      //eval make it possible to reach nested objects
      return eval("data." + key) || "";
    });
    return template;
  };

  SystemUI.prototype.utility.hasClass = function (element, className) {
    if (element.classList)
      return element.classList.contains(className);
    else
      return new RegExp('(^| )' + className + '( |$)', 'gi').test(element.className);
  };

  SystemUI.prototype.utility.addClass = function (el, className) {
    if (!el)
      return;

    if (el.classList)
      el.classList.add(className);
    else
      el.className += ' ' + className;
  };

  SystemUI.prototype.utility.removeClass = function (el, className) {
    if (!el)
      return;

    if (el.classList)
      el.classList.remove(className);
    else
      el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
  };

  SystemUI.prototype.utility.toTreeObject = function (element) {
    var jsTree = {
      _: element,
      _children: []
    };
    var indexIndicator = {};
    for (var index in element.childNodes) {
      var node = element.childNodes[index];

      if (node.nodeType === Node.ELEMENT_NODE) {
        var key = node.nodeName.toLowerCase();
        if (indexIndicator[key]) {
          indexIndicator[key]++;
          jsTree[key + '_' + indexIndicator[key]] = System.ui.utility.toTreeObject(node);
        } else {
          indexIndicator[key] = 1;
          jsTree[node.nodeName.toLowerCase()] = System.ui.utility.toTreeObject(node);
        }

        jsTree._children.push(node);
      }
    }

    return jsTree;
  };

  SystemUI.prototype.utility.getContentHeight = function (element, withPaddings) {
    var height = 0;
    var logs = [];
    var children = element.children;

    for (var index = 0, length = children.length; index < length; index++) {
      if (children[index].__ui_neutral) {
        continue;
      }

      var cs = window.getComputedStyle(children[index], null);

      if (cs.position === 'absolute') {
        continue;
      }

      //var dimension = children[index].getBoundingClientRect();
      var dimension = children[index].offsetTop + children[index].offsetHeight;
      var marginBottom = parseInt(cs.marginBottom || 0);

      height = dimension + marginBottom > height ? dimension + marginBottom : height;
    }

    if (withPaddings) {
      height += parseInt(window.getComputedStyle(element).paddingBottom || 0);
    }

    return height;
  };

  SystemUI.prototype.behaviorProxy = function (component, behaviorId) {
    var ui = this;
    var proxied = function () {
      Array.prototype.unshift.call(arguments, component);
      if (!ui.behaviors[behaviorId]) {
        throw 'Behavior does not exist: ' + behaviorId;
      }

      ui.behaviors[behaviorId].apply(null, arguments);
    };

    return proxied;
  };

  /**
   *
   * @param {type} behavior The behavior method that should apply on `hostObject`
   * @param {type} hostObject The object that will be passed to the behavior method as the host
   * @returns {mixed} Returned value from behavior method
   */
  SystemUI.prototype.behave = function (behavior, hostObject) {
    if ('function' !== typeof (behavior)) {
      var error = new Error('Behavior should be a function ');
      throw error;
    }

    var proxiedBehavior = function () {
      Array.prototype.unshift.call(arguments, hostObject);
      return behavior.apply(null, arguments);
    };

    return proxiedBehavior;
  };

  SystemUI.prototype.clone = function (obj) {
    var target = {};
    for (var i in obj) {
      if (obj.hasOwnProperty(i)) {
        target[i] = obj[i];
      }
    }
    return target;
  };

  SystemUI.prototype.getCenterPoint = function (rect) {
    var pos = document.activeElement.getBoundingClientRect();
    return {
      left: rect.left + (rect.width / 2),
      top: rect.top + (rect.height / 2)
    };

  };

  SystemUI.prototype.createModal = function (ori, text) {
    var lockPane;
    var modal = System.ui.clone(System.ui.COMPONENT_STRUCTURE);
    modal.close = function () {

    };

    modal.dispose = function () {

    };

    modal.remove = function () {
      this.el.parentNode.removeChild(this.el);
    };

    modal.el = document.createElement("div"); //or use jQuery's $("#photo")
    modal.el.className = "dialog center open";
    modal.el.innerHTML = "<h1 class='dialog-header-bar'>" + text + "</h1><div class='dialog-content-pane'></div><div class='dialog-action-bar'></div>";
    var origin = ori || document.activeElement;

    var loadModal = setTimeout(function () {
      lockPane = System.ui.lock(document.getElementsByClassName("app-pane")[0]);
      document.getElementsByTagName("body")[0].appendChild(modal.el);
      System.ui.animation.transform({
        from: loader.el,
        to: modal.el,
        el: modal.el,
        time: .4,
        flow: true,
        onComplete: function () {
          loader.dispose();
          origin.style.visibility = "hidden";

        }
      });

    }, 1000);

    var loader = System.ui.animation.toLoader(origin, "btn-loader");
    loader.on("cancel", function () {
      clearTimeout(loadModal);
    });

    //origin.style.opacity = "0";
    modal.el.addEventListener("click", function () {
      lockPane.dispose();
      System.ui.animation.scaleTransform({
        from: modal.el,
        to: origin,
        time: .6,
        onComplete: function () {
          origin.style.visibility = "";
        }
      });
      modal.remove();
    });
  };
  /**
   *
   * @param {object} conf
   * @param {number} t
   * @returns {HTMLElement}
   */
  SystemUI.prototype.lock = function (conf, t) {
    var _this = this;
    t = t || System.ui.DEFAULTS.animationDuration;
    var sourceRect = conf.element.getBoundingClientRect();
    var ss = window.getComputedStyle(conf.element);
    var lockPane = document.createElement('div');
    lockPane.__ui_disposed = false;
    lockPane.__ui_neutral = true;
    lockPane.className = 'lock-pane';
    lockPane.style.position = 'fixed';
    lockPane.style.left = sourceRect.left + 'px';
    lockPane.style.top = sourceRect.top + 'px';
    lockPane.style.width = sourceRect.width + "px";
    lockPane.style.height = sourceRect.height + "px";

    lockPane.style.zIndex = (ss.zIndex === "0" || ss.zIndex === "auto") ? 1 : ss.zIndex;
    //conf.element.style.opacity = .5;
    //lockPane.style.transition = "opacity " + t + "s";

    if (conf.akcent) {
      var akcent = document.createElement('div');
      akcent.className = conf.akcent;
      akcent.style.transform = 'scale(0)';
      lockPane.appendChild(akcent);
      tween.to(akcent, t, {
        transform: 'scale(1)',
        ease: Back.easeOut.config(2),
        y: 0
      });
    }

    conf.element.parentNode.insertBefore(lockPane, conf.element.nextSibling);
    tween.to(lockPane, t, {
      className: 'lock-pane show'
    });


    lockPane.dispose = function (fast) {
      if (lockPane.__ui_disposed) {
        return;
      }

      if (conf.akcent) {
        tween.to(akcent, fast ? 0 : t, {
          transform: 'scale(.5)',
          opacity: 0,
          ease: 'Power2.easeInOut'
        });
      }

      tween.to(lockPane, fast ? 0 : t, {
        opacity: "0",
        onComplete: function () {
          lockPane.parentNode.removeChild(lockPane);
          lockPane.__ui_disposed = true;
        }
      });

    };

    return lockPane;
  };

  SystemUI.prototype.animations = SystemUI.prototype.Animation = {
    transformBetween: function (conf) {
      var time = conf.time || System.ui.DEFAULTS.animationDuration;
      var sourceRect = conf.from.getBoundingClientRect();
      var distRect = conf.to.getBoundingClientRect();
      var ss = window.getComputedStyle(conf.from);
      var ds = window.getComputedStyle(conf.to);

      tween.fromTo(conf.el, time, {
        opacity: .1,
        left: sourceRect.left,
        top: sourceRect.top,
        borderRadius: distRect.width * parseInt(ss.borderRadius, 10) / sourceRect.width,
        margin: 0,
        transform: "scale(" + sourceRect.width / distRect.width + "," + sourceRect.height / distRect.height + ")",
        //boxShadow: ss.boxShadow,
        transformOrigin: "0 0"
      }, {
        opacity: 1,
        left: ds.left,
        top: ds.top,
        margin: ds.margin,
        transform: "scale(1,1)",
        borderRadius: ds.borderRadius,
        //boxShadow: ds.boxShadow,
        ease: conf.ease || "Power2.easeInOut",
        onComplete: function () {
          if (conf.onComplete)
            conf.onComplete();
        }
      });
    },
    slideOut: function (conf) {
      var t = conf.time || System.ui.DEFAULTS.animationDuration,
          sourceRect = conf.element.getBoundingClientRect(),
          direction = conf.to;

      tween.to(conf.element, t, {
        left: -sourceRect.width,
        ease: conf.ease || "Power2.easeInOut",
        delay: conf.delay || 0,
        onComplete: function () {
          conf.element.style.visibility = "";

          if (conf.onComplete)
            conf.onComplete();
        }
      });
    },
    slideIn: function (conf) {
      var t = conf.time || System.ui.DEFAULTS.animationDuration,
          sourceRect = conf.element.getBoundingClientRect(),
          direction = conf.from,
          transformBox = document.createElement("div"),
          sourceStyle = window.getComputedStyle(conf.element, null);

      transformBox.style.position = "absolute";
      transformBox.style.textAlign = "center";
      transformBox.style.backgroundColor = (sourceStyle.backgroundColor.indexOf("rgba") !== -1 ||
          sourceStyle.backgroundColor === "transparent") ? "rgb(190,190,190)" : sourceStyle.backgroundColor;
      transformBox.style.boxShadow = sourceStyle.boxShadow;
      transformBox.style.borderRadius = sourceStyle.borderRadius;
      //transformBox.style.padding = ss.padding;
      transformBox.style.color = conf.textColor || sourceStyle.color;
      transformBox.style.fontSize = sourceStyle.fontSize;
      transformBox.style.fontWeight = sourceStyle.fontWeight;
      transformBox.style.lineHeight = sourceRect.height + 'px';
      transformBox.style.textTransform = sourceStyle.textTransform;
      transformBox.style.zIndex = (sourceStyle.zIndex === "0" || sourceStyle.zIndex === "auto") ? 1 : sourceStyle.zIndex;
      transformBox.style.overflow = "hidden";
      if (conf.text) {
        transformBox.innerHTML = conf.text;
      }

      conf.element.style.visibility = "hidden";

      System.ui.body.appendChild(transformBox);

      tween.fromTo(transformBox, t, {
        width: sourceRect.width,
        height: sourceRect.height,
        left: -sourceRect.width,
        lineHeight: sourceRect.height + 'px',
        fontSize: '3em'
      }, {
        left: 0,
        ease: conf.ease || "Power2.easeInOut",
        delay: conf.delay || 0,
        onComplete: function () {
          conf.element.style.visibility = "";

          if (conf.fade > 0) {
            tween.to(transformBox, conf.fade, {
              opacity: 0,
              ease: "Power0.easeNone",
              onComplete: function () {
                transformBox.parentNode.removeChild(transformBox);
              }
            });
          } else {
            transformBox.parentNode.removeChild(transformBox);
          }

          if (conf.onComplete)
            conf.onComplete();
        }
      });
    },
    blastTo: function (conf) {
      var t = conf.time || System.ui.DEFAULTS.animationDuration;
      //var sourceRect = conf.from.getBoundingClientRect();
      //var sourceRect = conf.fromPoint;
      var sourceRect = conf.fromPoint;
      var distRect = conf.area.getBoundingClientRect();
      var to = conf.to.getBoundingClientRect();
      var wrapper = document.createElement("div");
      var blast = document.createElement("div");
      //var sourceStyle = window.getComputedStyle(conf.from, null);
      var ds = window.getComputedStyle(conf.area, null);
      var radius = distRect.width > distRect.height ? distRect.width : distRect.height;
      radius = 25;
      tween.set(wrapper, {
        position: 'absolute',
        overflow: 'hidden',
        width: '100%',
        height: '100%',
        top: 0,
        left: 0,
        backgroundColor: 'rgba(255,255,255,0)'
      });

      blast.style.position = "absolute";
      blast.style.backgroundColor = (ds.backgroundColor.indexOf("rgba") !== -1 ||
          ds.backgroundColor === "transparent") ? "rgb(190,190,190)" : ds.backgroundColor;

      if (conf.color) {
        blast.style.backgroundColor = conf.color;
      }
      blast.style.width = blast.style.height = radius + "px";
      blast.style.borderRadius = (radius / 2) + 'px';
      blast.style.zIndex = 1;

      //var initScale = sourceRect.width < sourceRect.height ? sourceRect.width / distRect.width : sourceRect.height / distRect.height;
      blast.style.transform = "scale(0)";
      blast.style.top = (sourceRect.top + (sourceRect.height / 2)) - (radius / 2) - distRect.top + "px";
      blast.style.left = (sourceRect.left + (sourceRect.width / 2)) - (radius / 2) - distRect.left + "px";


      if (conf.text) {
        blast.innerHTML = conf.text;
      }

      //conf.to.style.visibility = "hidden";
      if (conf.flow) {
        //conf.from.style.visibility = "hidden";
        //conf.from.style.transition = "none";
      }

      wrapper.appendChild(blast);
      wrapper.__ui_neutral = true;

      //wrapper
      conf.area.style.position = "relative";
      conf.area.appendChild(wrapper);
      if (conf.from) {
        tween.to(conf.from, t / 2, {
          opacity: 0
        });
      }

      tween.to(wrapper, t / 2, {
        delay: t / 2,
        backgroundColor: conf.toColor || null
      });

      tween.to(blast, t, {
        scale: 35,
        backgroundColor: conf.toColor || null,
        ease: "Power0.easeInOut",
        onComplete: function () {
          blast.style.border = 'none';
          if (conf.fade > 0) {
            tween.to(wrapper, conf.fade, {
              opacity: 0,
              ease: "Power0.easeInOut",
              onComplete: function () {
                conf.area.style.position = "";
                wrapper.parentNode.removeChild(wrapper);
              }
            });
          } else {
            wrapper.parentNode.removeChild(wrapper);
          }

          if (conf.onComplete) {
            conf.onComplete();
          }
        }
      });
    },
    transform: function (options) {
      var duration = options.time || System.ui.DEFAULTS.animationDuration;
      var sourceRect = options.from.getBoundingClientRect();
      var distRect = options.to.getBoundingClientRect();
      var sourceStyle = window.getComputedStyle(options.from, null);
      var ds = window.getComputedStyle(options.to, null);

      var transformBox = document.createElement('div');

      tween.set(transformBox, {
        position: 'absolute',
        x: sourceRect.left,
        y: sourceRect.top,
        backgroundColor: (sourceStyle.backgroundColor.indexOf("rgba") !== -1 ||
            sourceStyle.backgroundColor === "transparent") ? "rgb(190,190,190)" : sourceStyle.backgroundColor,
        boxShadow: sourceStyle.boxShadow,
        borderRadius: sourceStyle.borderRadius,
        zIndex: (ds.zIndex === "0" || ds.zIndex === "auto") ? 1 : ds.zIndex,
        overflow: "hidden",
        transformOrigin: "top left",
        width: sourceRect.width + 'px',
        height: sourceRect.height + 'px',
        left: 0,
        top: 0
      });

      if (!options.hasOwnProperty('evolve') || options.evolve) {
        options.to.style.visibility = "hidden";
      }

      if (options.flow) {
        options.from.style.visibility = "hidden";
        options.from.style.transition = "none";
      }

      transformBox.__ui_neutral = true;
      System.ui.body.appendChild(transformBox);

      setTimeout(function () {
        animate();
      });

      function animate() {
        tween.to(transformBox, duration, {
          width: distRect.width,
          height: distRect.height,
          x: distRect.left,
          y: distRect.top,
          backgroundColor: (ds.backgroundColor.indexOf("rgba") !== -1 ||
              ds.backgroundColor === "transparent") ? "rgb(190,190,190)" : ds.backgroundColor,
          borderRadius: ds.borderRadius,
          ease: options.ease || "Power2.easeInOut",
          delay: options.delay || 0,
          onComplete: function () {
            options.from.style.transition = "";
            options.to.style.visibility = "";

            if (options.fade > 0) {
              tween.to(transformBox, options.fade, {
                opacity: 0,
                ease: "Power0.easeNone",
                delay: .01,
                onComplete: function () {
                  transformBox.parentNode.removeChild(transformBox);
                }
              });
            } else {
              transformBox.parentNode.removeChild(transformBox);
            }

            if (options.onComplete) {
              options.onComplete();
            }
          }
        });
      }
    },
    /**
     *
     * @param {object} conf
     * @returns {undefined}
     */
    sizeTransform: function (conf) {
      var t = conf.time || System.ui.DEFAULTS.animationDuration;
      var sourceRect = conf.from.getBoundingClientRect();
      var distRect = conf.to.getBoundingClientRect();
      var transformBox = document.createElement("div");
      //var sourceStyle = window.getComputedStyle(conf.from, null);
      //var ds = window.getComputedStyle(conf.to, null);
      transformBox.style.position = "absolute";
      transformBox.style.textAlign = "center";
      transformBox.className = conf.to.className;

      conf.to.style.visibility = "hidden";
      if (conf.flow) {
        conf.from.style.visibility = "hidden";
        conf.from.style.transition = "none";
      }

      if (conf.to.parentNode) {
        conf.to.parentNode.appendChild(transformBox);
      } else {
        System.ui.body.appendChild(transformBox);
      }

      tween.fromTo(transformBox, t, {
        width: sourceRect.width,
        height: sourceRect.height,
        left: sourceRect.left,
        top: sourceRect.top
        //opacity: 1
      }, {
        width: distRect.width,
        height: distRect.height,
        left: distRect.left,
        top: distRect.top,
        ease: conf.ease || "Power2.easeInOut",
        onComplete: function () {
          conf.to.style.visibility = "";
          conf.from.style.transition = "";
          if (conf.fade > 0) {
            tween.to(transformBox, conf.fade, {
              opacity: 0,
              ease: "Power0.easeNone",
              onComplete: function () {
                transformBox.parentNode.removeChild(transformBox);
              }
            });
          } else {
            transformBox.parentNode.removeChild(transformBox);
          }

          if (conf.onComplete)
            conf.onComplete();
        }
      });
    },
    /**
     *
     * @param {object} conf
     * @returns {undefined}
     */
    rippleOut: function (conf) {
      var t = conf.time || System.ui.DEFAULTS.animationDuration;
      var sourceRect = conf.from.getBoundingClientRect();
      var distRect = conf.to.getBoundingClientRect();
      var transformBox = document.createElement("div");
      var ds = window.getComputedStyle(conf.to, null);
      transformBox.style.position = "absolute";
      transformBox.style.textAlign = "center";
      transformBox.style.borderRadius = "50%";
      transformBox.style.backgroundColor = ds.backgroundColor;
      transformBox.style.zIndex = ds.zIndex;

      conf.to.style.visibility = "hidden";
      if (conf.flow) {
        conf.from.style.visibility = "hidden";
        conf.from.style.transition = "none";
      }

      if (conf.to.parentNode) {
        conf.to.parentNode.appendChild(transformBox);
      } else {
        System.ui.body.appendChild(transformBox);
      }
      var width = distRect.width > distRect.height ? distRect.width : distRect.height,
          halfWidth = distRect.width / 2,
          sourceLeft = sourceRect.left + (sourceRect.width / 2),
          sourceTop = sourceRect.top + (sourceRect.height / 2);
      tween.fromTo(transformBox, t, {
        width: width,
        height: width,
        left: sourceLeft - halfWidth,
        top: sourceTop - halfWidth,
        transform: "scale(0)"
        //opacity: 1
      }, {
        transform: "scale(2)",
        ease: conf.ease || "Power2.easeInOut",
        delay: conf.delay || 0,
        onComplete: function () {
          conf.to.style.visibility = "";
          conf.from.style.transition = "";
          if (conf.fade > 0) {
            tween.to(transformBox, conf.fade, {
              opacity: 0,
              ease: "Power0.easeNone",
              onComplete: function () {
                transformBox.parentNode.removeChild(transformBox);
              }
            });
          } else if (transformBox.parentNode) {
            transformBox.parentNode.removeChild(transformBox);
          }

          if (conf.onComplete)
            conf.onComplete();
        }
      });
    },
    scaleTransform: function (conf) {
      var time = conf.time || System.ui.DEFAULTS.animationDuration;
      var ease = conf.ease || "Power2.easeInOut";
      var sourceRect = conf.from.getBoundingClientRect();
      var distRect = conf.to.getBoundingClientRect();
      //console.log(sourceRect, distRect);
      var distBox = document.createElement("div");
      var sourceStyle = window.getComputedStyle(conf.from, null);
      var distStyle = window.getComputedStyle(conf.to, null);
      var distBoxStyle = distBox.style;
      distBoxStyle.position = "absolute";
      distBoxStyle.backgroundColor = (distStyle.backgroundColor.indexOf("rgba") !== -1 ||
          distStyle.backgroundColor === "transparent") ? "rgb(255,255,255)" : distStyle.backgroundColor;
      distBoxStyle.boxShadow = distStyle.boxShadow;
      distBoxStyle.borderRadius = conf.to.style.borderRadius;
      distBoxStyle.padding = distStyle.padding;
      distBoxStyle.color = distStyle.color;
      distBoxStyle.fontSize = distStyle.fontSize;
      distBoxStyle.fontWeight = distStyle.fontWeight;
      distBoxStyle.textAlign = distStyle.textAlign;
      distBoxStyle.textTransform = distStyle.textTransform;
      distBoxStyle.zIndex = (System.ui.body.style.zIndex === "0" || System.ui.body.style.zIndex === "auto") ? 1 : System.ui.body.style.zIndex || 1;
      distBoxStyle.width = distRect.width + "px";
      distBoxStyle.height = distRect.height + "px";
      distBoxStyle.lineHeight = distStyle.lineHeight;
      distBoxStyle.border = distStyle.border;
      distBoxStyle.borderRadius = distStyle.borderRadius;
      distBoxStyle.margin = "0px";
      distBoxStyle.transition = "none";
      distBox.innerHTML = conf.to.innerHTML;
      distBox.className = conf.to.className;

      var originBox = document.createElement("div");
      originBox.style.position = "absolute";
      originBox.style.backgroundColor = (sourceStyle.backgroundColor.indexOf("rgba") !== -1 ||
          sourceStyle.backgroundColor === "transparent") ? "rgb(255,255,255)" : sourceStyle.backgroundColor;
      originBox.style.boxShadow = 'none';
      //origin.style.borderRadius = conf.from.style.borderRadius;
      originBox.style.padding = sourceStyle.padding;
      originBox.style.color = sourceStyle.color;
      originBox.style.fontSize = sourceStyle.fontSize;
      originBox.style.fontWeight = sourceStyle.fontWeight;
      originBox.style.textAlign = sourceStyle.textAlign;
      originBox.style.textDecoration = sourceStyle.textDecoration;
      originBox.style.zIndex = (System.ui.body.style.zIndex === "0" || System.ui.body.style.zIndex === "auto") ? 2 : parseInt(System.ui.body.style.zIndex || 1) + 1;
      //alert((Anim.body.zIndex === "0" || Anim.body.zIndex === "auto") ? 2 : parseInt(Anim.body.zIndex) +1)
      originBox.style.margin = "0px";
      originBox.style.width = sourceRect.width + "px";
      originBox.style.height = sourceRect.height + "px";
      originBox.style.lineHeight = sourceStyle.lineHeight;
      originBox.style.border = sourceStyle.border;
      originBox.style.transition = "none";
      originBox.innerHTML = conf.from.innerHTML;
      originBox.className = conf.from.className;

      conf.to.style.visibility = "hidden";
      if (conf.flow) {
        conf.from.style.visibility = "hidden";
        conf.from.style.transition = "none";
      }

      System.ui.body.appendChild(distBox);
      System.ui.body.appendChild(originBox);

      tween.fromTo(originBox, time, {
        left: sourceRect.left,
        top: sourceRect.top,
        transformOrigin: "0 0"
      }, {
        left: distRect.left,
        top: distRect.top,
        borderRadius: distStyle.borderRadius,
        opacity: 0,
        transform: "scale(" + distRect.width / sourceRect.width + "," + distRect.height / sourceRect.height + ")",
        ease: ease,
        onComplete: function () {
          originBox.parentNode.removeChild(originBox);
          conf.from.style.transition = "";
        }
      });

      tween.fromTo(distBox, time, {
        left: sourceRect.left,
        top: sourceRect.top,
        margin: 0,
        transform: "scale(" + sourceRect.width / distRect.width + "," + sourceRect.height / distRect.height + ")",
        transformOrigin: "0 0"
      }, {
        left: distRect.left,
        top: distRect.top,
        transform: "scale(1,1)",
        ease: ease,
        onComplete: function () {
          conf.to.style.visibility = "";
          if (conf.onComplete)
            conf.onComplete();
          setTimeout(function () {
            distBox.parentNode.removeChild(distBox);
          }, 1);

        }
      });
    },
    toLoader: function (el, loaderClass) {
      var loader = System.ui.clone(System.ui.COMPONENT_STRUCTURE);

      loader.el = document.createElement("div");
      loader.cancel = function () {
        this.trigger("cancel");
        this.dispose();
      };
      loader.dispose = function () {
        tween.fromTo(el, .15, {
              opacity: 0
            },
            {
              opacity: 1
            });
        el.style.visibility = "";
        this.disposed = true;
        loader.el.parentNode.removeChild(loader.el);
        this.trigger('dispose');
      };


      var elemStyle = window.getComputedStyle(el);
      var elemRect = el.getBoundingClientRect();
      var elemCent = System.ui.getCenterPoint(elemRect);
      loader.el.className = loaderClass;
      System.ui.body.appendChild(loader.el);

      var loaderStyle = window.getComputedStyle(loader.el);
      var loaderRect = loader.el.getBoundingClientRect();
      var loaderElStyle = loader.el.style;

      loaderElStyle.position = "absolute";
      loaderElStyle.width = elemRect.width + "px";
      loaderElStyle.height = elemRect.height + "px";
      loaderElStyle.top = elemRect.top + 'px';
      loaderElStyle.left = elemRect.left + 'px';
      loaderElStyle.zIndex = (elemStyle.zIndex === "0" || elemStyle.zIndex === "auto") ? 1 : elemStyle.zIndex;

      var animProperties = (loaderClass) ? {
        top: elemCent.top - loaderRect.width / 2,
        left: elemCent.left - loaderRect.height / 2,
        width: loaderRect.width,
        height: loaderRect.height,
        borderRadius: loaderStyle.borderRadius,
        //backgroundColor: loaderStyle.backgroundColor,
        boxShadow: loaderStyle.boxShadow,
        ease: "Power3.easeOut"
      } : {
        top: elemCent.top - 30,
        left: elemCent.left - 30,
        width: 60,
        height: 60,
        borderRadius: 30,
        ease: "Power2.easeOut"
      };

      loaderElStyle.visibility = "hidden";
      setTimeout(function () {
        loader.el.className = "";
        loaderElStyle.visibility = "";
        loaderElStyle.borderRadius = elemStyle.borderRadius;
        loaderElStyle.backgroundColor = (elemStyle.backgroundColor.indexOf("rgba") !== -1 ||
            elemStyle.backgroundColor === "transparent" || elemStyle.backgroundColor === "rgb(255, 255, 255)") ? elemStyle.color : elemStyle.backgroundColor;
        el.style.visibility = "hidden";
        tween.to(loader.el, 5,
            {
              top: elemCent.top - 14,
              left: elemCent.left - 14,
              width: 28,
              height: 28,
              borderRadius: 28,
              ease: "Power4.easeOut",
              onComplete: function () {
                loader.el.className = loaderClass;
              }
            });

        animProperties.delay = 5;
        tween.to(loader.el, .3, animProperties);
        loader.el.addEventListener("click", function () {
          loader.cancel();
        });
      }, 0);
      return loader;
    }
  };
}(TweenLite));

/* global xtag */

(function () {
  var Field = {
    lifecycle: {
      created: function () {
        var element = this;
        var input = this.querySelectorAll('input, textarea, select');
        if (input.length > 1) {
          console.warn('Only one input field is allowed inside system-field', this);
        }

        element.xtag._input = this.querySelectorAll('input, textarea, select, system-input-json')[0] || {};
        element.xtag._label = this.querySelectorAll('label')[0];
        if (element.xtag._label) {
          element.xtag._label.addEventListener('click', element.xtag._input.focus.bind(element.xtag._input));
        }

        if (element.xtag._input) {
          element.setEmptiness();

          element.xtag._input.addEventListener('focus', function () {
            element.setAttribute('focus', '');
            element.setEmptiness();
          });

          element.xtag._input.addEventListener('blur', function () {
            element.removeAttribute('focus');
          });

          element.xtag._input.onchange = function (e) {
            element.setEmptiness();
          };

          element.xtag._input.addEventListener('input', function (e) {
            element.setEmptiness();
          });
        }
      },
      inserted: function () {
        var tag = this;
        tag.xtag.observer = setInterval(function () {
          if (tag.xtag._input.value !== tag.xtag.oldValue) {
            tag.setEmptiness();
            tag.xtag.oldValue = tag.xtag._input.value;
          }
        }, 250);

        tag.setEmptiness();
      },
      removed: function () {
        clearInterval(this.xtag.observer);
      }
    },
    accessors: {},
    events: {},
    methods: {
      setEmptiness: function () {
        var element = this;

        if (element.xtag._input.value || element.xtag._input.type === 'file') {
          element.removeAttribute('empty');
        } else {
          element.setAttribute('empty', '');
        }
      }
    }
  };

  xtag.register('system-field', Field);
})();
/* global System, xtag */

(function () {
  var FloatMenu = {
    lifecycle: {
      created: function () {
        var _this = this;
        _this.xtag.indicator = _this.querySelector('[indicator]') || _this.getElementsByTagName('div')[0];
        _this.xtag.actionsContainer = _this.querySelector('[actions]') || _this.getElementsByTagName('div')[1];

        var expand = function (e) {
          e.stopPropagation();
          e.preventDefault();

          if (!_this.expanded) {
            _this.expand();
            window.addEventListener('touchstart', contract);
          }
        };

        var contract = function (e) {
          e.stopPropagation();
          e.preventDefault();

          if (_this.expanded) {
            _this.contract();
          }

          window.removeEventListener('touchstart', contract);
        };

        //_this.xtag.indicator.addEventListener('mouseenter', expand);
        //_this.xtag.indicator.addEventListener('touchstart', expand);

        _this.addEventListener('mouseenter', expand);
        _this.addEventListener('touchstart', expand);

        _this.addEventListener('mouseleave', contract);

        this.style.position = 'absolute';
        this.xtag.originClassName = this.className;

        this.xtag.observer = new MutationObserver(function (mutations) {
          if (_this.xtag.actionsContainer.children.length) {
            _this.on();
          } else {
            _this.off();
          }
        });
      },
      inserted: function () {
        var _this = this;

        _this.xtag.observer.observe(_this.xtag.actionsContainer, {
          attributes: false,
          childList: true,
          characterData: false
        });

        if (_this.children.length) {
          _this.on();
        } else {
          _this.off();
        }
      },
      removed: function () {
        this.off(true);
      }
    },
    accessors: {
      position: {
        attribute: {}
      },
      parent: {
        attribute: {}
      },
      onAttached: {
        attribute: {},
        set: function (value) {
          this.xtag.onAttached = value;
        },
        get: function (value) {
          return this.xtag.onAttached;
        }
      }
    },
    methods: {
      expand: function () {
        if (this.expanded)
          return;

        this.expanded = true;
        System.ui.utility.addClass(this, 'expand');
      },
      contract: function () {
        this.expanded = false;
        System.ui.utility.removeClass(this, 'expand');
      },
      on: function (flag) {
        System.ui.utility.removeClass(this, 'off');
      },
      off: function (flag) {
        System.ui.utility.addClass(this, 'off');
      },
      clean: function () {
        this.innerHTML = "";
      }
    },
    events: {}
  };

  xtag.register('system-float-menu', FloatMenu);
})();
/* global xtag */

(function () {
  var InputJson = {
    lifecycle: {
      created: function () {
        this.xtag.elementType = 'input';
        this.xtag.allFields = [];
        this.xtag.fields = [];
        this.xtag.lastField = this.createField('', '');
        this.xtag.active = this.xtag.lastField;

        this.elementType = this.xtag.elementType;

        this.updateFieldsCount();
      }
    },
    methods: {
      createField: function (nameValue, valueValue) {
        var jsonInput = this;
        var name = document.createElement('input');
        name.value = nameValue;
        name.className = 'name';
        name.placeholder = 'name';

        var splitter = document.createElement('span');
        splitter.className = 'splitter';

        var value = document.createElement('input');
        if ('object' === typeof valueValue) {
          value = document.createElement('system-input-json');
        }
        value.value = valueValue;
        value.className = 'value';
        value.placeholder = 'value';
        value.elementType = '';

        var field = document.createElement('p');

        name.addEventListener('keyup', function (e) {
          jsonInput.updateFieldsCount();
        });

        name.addEventListener('focus', function (e) {
          jsonInput.xtag.active = field;
        });

        value.addEventListener('keyup', function (e) {
          jsonInput.updateFieldsCount();
        });

        value.addEventListener('focus', function (e) {
          jsonInput.xtag.active = field;
        });

        field._name = name;
        field.appendChild(name);
        field.appendChild(splitter);
        field.appendChild(value);

        this.xtag.allFields.push({
          name: name,
          value: value,
          field: field
        });

        this.appendChild(field);

        return {
          name: name,
          value: value,
          field: field
        };
      },
      updateFieldsCount: function () {
        var jsonInput = this;
        var newFields = [];
        this.xtag.fields = [];
        this.xtag.allFields.forEach(function (item) {
          if (!item.name.value && (!item.value.value || Object.keys(item.value.value).length === 0) && item.field.parentNode && item.field !== jsonInput.xtag.lastField.field) {
            item.field.parentNode.removeChild(item.field);
            return;
          }

          if (item.value.nodeName === 'INPUT' && item.value.value === '{}') {
            var json = document.createElement('system-input-json');
            json.className = 'value';
            item.field.replaceChild(json, item.value);
            item.value = json;
            json.focus();
          }

          if (item.field !== jsonInput.xtag.lastField.field) {
            jsonInput.xtag.fields.push(item);
          }

          newFields.push(item);
        });

        this.xtag.allFields = newFields;

        if (!jsonInput.xtag.lastField.name || jsonInput.xtag.lastField.name.value) {
          jsonInput.xtag.lastField = this.createField('', '');
        }

        if (jsonInput.xtag.active && jsonInput.xtag.active.parentNode) {
          jsonInput.xtag.active.focus();
        } else {
          jsonInput.xtag.lastField.name.focus();
        }
      },
      focus: function () {
        this.xtag.allFields[this.xtag.allFields.length - 1].name.focus();
      }
    },
    accessors: {
      value: {
        set: function (data) {
          var jsonInput = this;

          if (jsonInput.xtag.allFields)
            jsonInput.xtag.allFields.forEach(function (item) {
              if (item.field.parentNode)
                item.field.parentNode.removeChild(item.field);
            });

          jsonInput.xtag.allFields = [];
          jsonInput.xtag.fields = [];

          if ('string' === typeof data)
            data = JSON.parse(data || '{}');

          if ('object' !== typeof data) {
            return;
          }

          if (Object.keys(data).length === 0) {
            jsonInput.xtag.lastField = jsonInput.createField('', '');
          } else {
            for (var key in data) {
              if (data.hasOwnProperty(key)) {
                jsonInput.createField(key, data[key]);
              }
            }

            jsonInput.xtag.lastField = jsonInput.createField('', '');
          }

          jsonInput.updateFieldsCount();
        },
        get: function () {
          var value = {};
          this.xtag.fields.forEach(function (item) {
            if (item.name.value !== '') {
              value[item.name.value] = item.value.value;
            }
          });

          return value;
        }
      },
      elementType: {
        attribute: {},
        set: function (value) {
          this.xtag.elementType = value;
        },
        get: function () {
          return this.xtag.elementType;
        }
      }
    }
  };

  xtag.register('system-input-json', InputJson);
})();
/* global xtag, System */

(function () {
  var List = {
    lifecycle: {
      created: function () {
        this.template = this.innerHTML;
        this.xtag.selectedStyle = 'selected';
        this.xtag.action = '[item]';
        this.innerHTML = "";
        this.links = {};
        this.data = [];
        this.value = -1;
      },
      inserted: function () {
      },
      attributeChanged: function (attrName, oldValue, newValue) {
      }
    },
    methods: {
      render: function (data, action) {
        //var data = this.data;
        this.innerHTML = "";
        var selectableItem = null;
        for (var i = 0, len = data.length; i < len; i++) {
          //data[i]._itemIndex = i;
          var item = xtag.createFragment(System.ui.utility.populate(this.template, data[i]));
          if (action) {
            selectableItem = xtag.query(item, action)[0];

            if (selectableItem) {
              selectableItem.dataset.index = i;
              selectableItem.setAttribute('item', '');

              if (data[i].id) {
                this.links[data[i].id] = selectableItem;
              }

              this.links[i] = selectableItem;
            }
          }

          this.appendChild(item);
        }
      },
      selectItem: function (i, element) {
        var oldItem = this.links[this.xtag.value];
        if (oldItem) {
          System.ui.utility.removeClass(oldItem, this.xtag.selectedStyle);
        }

        var newItem = this.links[i];
        if (this.data[i].id) {
          newItem = this.links[this.data[i].id];
        }

        System.ui.utility.addClass(newItem, this.xtag.selectedStyle);

        xtag.fireEvent(this, 'item-selected', {
          detail: {
            index: i,
            data: this.xtag.data[i],
            element: element
          }
        });
      }
    },
    accessors: {
      data: {
        set: function (value) {
          var element = this;

          this.value = -1;
          if ("object" !== typeof value) {
            this.xtag.data = [];
            value = [];
          }

          var toRender = value;

          this.xtag.data = value;

          if (this.onSetData) {
            this.onSetData(toRender);
          }

          this.render(toRender, this.xtag.action);
        },
        get: function () {
          return this.xtag.data;
        }
      },
      onSetData: {
        attribute: {
          validate: function (value) {
            this.xtag.onSetData = value;
            return '[ function ]';
          }
        },
        set: function (value) {
        },
        get: function (value) {
          return this.xtag.onSetData;
        }
      },
      selectedStyle: {
        attribute: {},
        set: function (value) {
          this.xtag.selectedStyle = value;
        },
        get: function () {
          return this.xtag.selectedStyle;
        }
      },
      value: {
        attribute: {},
        set: function (value, oldValue) {
          if (value === oldValue) {
            return;
          }

          value = parseInt(value);

          if (value > -1 && /*value !== this.xtag.value && */this.xtag.data.length) {
            this.selectItem(value, this.links[value]);
          }

          this.xtag.value = value;


        },
        get: function () {
          return this.xtag.value;
        }
      },
      action: {
        attribute: {},
        set: function (value) {
          this.xtag.action = value;
        },
        get: function () {
          return this.xtag.action;
        }
      }
    },
    events: {
      "click:delegate([item])": function (e) {
        e.preventDefault();
        e.currentTarget.value = this.dataset.index;
      },
      "tap:delegate([item])": function (e) {
        e.preventDefault();
        e.currentTarget.value = this.dataset.index;
      }
    }
  };

  xtag.register('system-list', List);
})();

(function () {
  var SortableList = {
    lifecycle: {
      created: function () {
        this.xtag.placeHolder = document.createElement("li");
        this.xtag.placeHolder.className += "placeholder";

        this.xtag.glass = document.createElement("div");
        this.xtag.glass.style.position = "absolute";
        this.xtag.glass.style.width = "100%";
        this.xtag.glass.style.height = "100%";

        this.style.overflow = "hidden";
        this.isValidParent = function () {
          return true;
        };
        this.onDrop = function () {
        };
      },
      inserted: function () {

      },
      removed: function () {

      }
    },
    events: {
      mousedown: function (event) {
        //console.log("down");
      },
      "mousedown:delegate(.handle)": function (e) {
        var dim = this.getBoundingClientRect();
        e.currentTarget.xtag.initDragPosition = {
          x: e.pageX - dim.left,
          y: e.pageY - dim.top
        };

        var draggedItem = this;
        while (draggedItem.tagName.toLowerCase() !== "li") {
          draggedItem = draggedItem.parentNode;
        }

        var diDimension = draggedItem.getBoundingClientRect();
        e.currentTarget.xtag.draggedItem = draggedItem;
        draggedItem.style.position = "fixed";
        draggedItem.style.width = diDimension.width + "px";
        draggedItem.style.height = diDimension.height + "px";
        e.currentTarget.xtag.glass.width = diDimension.width + "px";
        e.currentTarget.xtag.glass.height = diDimension.height + "px";
        draggedItem.appendChild(e.currentTarget.xtag.glass);
        System.ui.utility.addClass(draggedItem, "dragged");

        //console.log(e, draggedItem);
        e.stopPropagation();
        e.preventDefault();
      },
      "mouseup:delegate(.handle)": function (e) {
        e.stopPropagation();
        e.preventDefault();
      },
      mousemove: function (event) {
        if (!this.xtag.draggedItem)
          return;

        var groups = this.querySelectorAll("ul");
        var groupDim = [
        ];
        for (var i = 0, len = groups.length; i < len; i++) {
          groupDim.push(groups[i].getBoundingClientRect());
        }

        var parent = null;
        var index = 0;
        var indexElement = null;

        for (var i = groupDim.length - 1; i >= 0; i--) {
          var parentDim = groupDim[i];
          if (event.pageX > parentDim.left && event.pageX < parentDim.right && event.pageY > parentDim.top && event.pageY < parentDim.bottom) {
            parent = groups[i];
            //indexElement = parent.lastChild;
            var children = parent.childNodes || [
            ];
            var childElements = [
            ];
            //index = childElements.length;
            for (var n = 0; n < children.length; n++) {
              if (children[n].tagName.toLowerCase() !== "li" || children[n] === this.xtag.draggedItem /*|| children[n].className === "placeholder"*/)
                continue;
              childElements.push(children[n]);
            }
            //console.log(childElements)
            var extra = {
              height: 0,
              left: 0
            };
            for (n = childElements.length - 1; n >= 0; n--) {
              if (childElements[n].className === "placeholder") {
                //extra = childElements[n].getBoundingClientRect();
                //console.log(extra.height)
                continue;
              }

              var childDim = childElements[n].getBoundingClientRect();

              if (event.pageY > childDim.top && event.pageY < childDim.top + (childDim.height / 2) /*&& event.pageY + extra.height < childDim.bottom - (childDim.height / 2)*/) {
                index = n;
                indexElement = childElements[index] /*|| parent.firstChild*/;
                //console.log("above", index);
                //console.log(childDim, event.pageY, n)
                break;
              } else if (event.pageY >= childDim.top + (childDim.height / 2) /*&& event.pageY < childDim.bottom*/) {
                index = n + 1;
                indexElement = childElements[index];
                //console.log("lower", index);
                //console.log(childDim, event.pageY, n)
                break;
              } else {
                indexElement = this.xtag.tempIndexElement;
                //console.log(extra, event.pageY)
              }
              //console.log(extra)
              //extra.height = 0;
              //extra.top = 0;
            }
            break;
          }
        }

        this.xtag.draggedItem.style.left = event.pageX - this.xtag.initDragPosition.x + "px";
        this.xtag.draggedItem.style.top = event.pageY - this.xtag.initDragPosition.y + "px";

        if (parent && (this.xtag.tempParent !== parent || this.xtag.tempIndexElement !== indexElement)) {
          this.xtag.tempParent = parent;
          this.xtag.tempIndex = index;
          this.xtag.tempIndexElement = indexElement;
          if (this.isValidParent(this.xtag.draggedItem, parent, this.xtag.tempIndex)) {
            //console.log(indexElement)
            if (indexElement && indexElement.parentNode === parent)
              parent.insertBefore(this.xtag.placeHolder, indexElement);
            else if (!indexElement)
              parent.insertBefore(this.xtag.placeHolder, indexElement);
          }
        }
      },
      mouseup: function (event) {
        //console.log("up");
        if (this.xtag.draggedItem) {
          this.xtag.draggedItem.style.position = "";
          this.xtag.draggedItem.style.width = "";
          this.xtag.draggedItem.style.height = "";
          this.xtag.draggedItem.style.left = "";
          this.xtag.draggedItem.style.top = "";
          this.xtag.draggedItem.removeChild(this.xtag.glass);
          System.ui.utility.removeClass(this.xtag.draggedItem, "dragged");

          if (this.xtag.placeHolder.parentNode) {
            this.onDrop(this.xtag.draggedItem, this.xtag.tempParent, this.xtag.tempIndex);
            this.xtag.placeHolder.parentNode.replaceChild(this.xtag.draggedItem, this.xtag.placeHolder);
          }

          this.xtag.draggedItem = null;
          this.xtag.tempParent = null;
          this.xtag.tempIndex = null;
        }
        event.preventDefault();
        event.stopPropagation();
      }

    }
  };

  xtag.register('system-sortable-list', SortableList);
})();
(function () {
  var Spirit = {
    lifecycle: {
      created: function () {
        var element = this;
        element.xtag.animations = element.getAttribute('animations').split(/[\s,]+/).filter(Boolean);
        element.xtag.registeredAnimations = [];
        element.xtag.cachedAnimations = element.getAttribute('animations');
      },
      inserted: function () {
        if (this.xtag.cachedAnimations && !this.xtag.animations.length) {
          this.setAttribute('animations', this.xtag.cachedAnimations);
          this.xtag.animations = this.xtag.cachedAnimations.split(/[\s,]+/).filter(Boolean);
          this.xtag.cachedAnimations = null;
          this.prepare();
        }
      },
      removed: function () {
        this.xtag.cachedAnimations = xtag.clone(this.xtag.animations).join(',');
        this.xtag.animations = [];
        this.prepare();
      }
    },
    accessors: {
      animations: {
        attribute: {},
        set: function (value) {
          var element = this;
          if (typeof value === 'string') {
            this.xtag.animations = value.split(/[\s,]+/).filter(Boolean);
          } else {
            this.xtag.animations = [];
          }

          element.prepare();
        },
        get: function () {
          return this.xtag.animations;
        }
      }
    },
    events: {},
    methods: {
      prepare: function () {
        var element = this;
        this.xtag.animations.forEach(function (item) {
          if (element.xtag.registeredAnimations.indexOf(item) !== -1) {
            return null;
          }

          if (!System.spiritAnimations[item]) {
            return console.warn('spirit animation not found:', item);
          }

          System.spiritAnimations[item].register(element);
          element.xtag.registeredAnimations.push(item);
        });

        var animations = element.getAttribute('animations').split(/[\s,]+/).filter(Boolean);
        this.xtag.registeredAnimations = this.xtag.registeredAnimations.filter(function (item) {
          if (animations.indexOf(item) === -1) {
            System.spiritAnimations[item].deregister(element);
            return false;
          }

          return true;
        });
      }
    }
  };

  xtag.register('system-spirit', Spirit);
})();
(function () {
  var SwitchButton = {
    lifecycle: {
      created: function () {
        this.xtag.active = false;
      },
      inserted: function () {
      },
      removed: function () {
      },
      attributeChanged: function (attrName, oldValue, newValue) {

      }
    },
    accessors: {
      name: {
        attribute: {}
      },
      module: {
        attribute: {}
      },
      active: {
        attribute: {
          //boolean: true
        },
        set: function (value) {
          xtag.fireEvent(this, 'switched', {
            detail: {
              active: Boolean(value)
            },
            bubbles: true,
            cancelable: true
          });
          this.xtag.active = Boolean(value);
        },
        get: function () {
          return this.xtag.active;
        }
      }
    },
    events: {
      click: function (event) {
        if (this.xtag.active) {
          event.currentTarget.removeAttribute('active');
        } else {
          event.currentTarget.setAttribute('active', 'true');
        }
      }
    }
  };

  xtag.register('system-button-switch', SwitchButton);
})();
/* global xtag */

(function () {
  var UITemplate = {
    lifecycle: {
      created: function () {
        this.xtag.validate = false;
        this.xtag.show = true;

        if (!this.name) {
          throw "system-ui-view missing the `name` attribute";
        }

        this.xtag.placeholder = document.createComment(' ' + this.module + '/' + this.name + ' ');
      },
      inserted: function () {
        if (this.xtag.validate) {
          this.xtag.originalParent = this.parentNode;
          return;
        }

        this.xtag.originalParent = this.parentNode;
        if (this.xtag.showWhenAdded) {
          this.xtag.showWhenAdded = null;
          this.show();
          return;
        }
      },
      removed: function () {
        this.xtag.validate = false;
      }
    },
    methods: {
      show: function () {
        this.xtag.validate = true;
        this.xtag.shouldBeShown = true;
        if (!this.xtag.originalParent) {
          this.xtag.showAsSoonAsAdded = true;
          return;
        }
        if (this.xtag.placeholder.parentNode)
          this.xtag.originalParent.replaceChild(this, this.xtag.placeholder);
      },
      hide: function () {
        this.xtag.originalParent = this.parentNode;
        this.xtag.originalParent.replaceChild(this.xtag.placeholder, this);
      }
    },
    accessors: {
      name: {
        attribute: {}
      },
      module: {
        attribute: {}
      },
      validate: {
        attribute: {},
        set: function (value) {
          this.xtag.validate = value;
        },
        get: function (value) {
          return this.xtag.validate;
        }
      }
    }
  };

  xtag.register('system-ui-view', UITemplate);
})();