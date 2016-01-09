(function () {
   //System = system;
   System = {
      moduleIdentifier: "app",
      modules: {},
      appPathfiledName: null,
      activityTree: [],
      onLoadQueue: [],
      activeModule: null,
      notYetStarted: [],
      activeRequests: {},
      /*activeHashHandler: function () {
       },*/
      UI: {},
      MODULE_ABSTRACT: {
         inited: false,
         started: false,
         moduleIdentifier: "app",
         navigation: {},
         params: {},
         html: "",
         modules: {},
         activeModule: null,
         init: function (navigations, params, html)
         {
            this.inited = true;
            this.navigation = navigations;
            this.params = params;
            this.html = html;
            this.trigger("onInit");
         },
         start: function ()
         {
            this.started = true;
            //System.app.activeModule = this;
            this.trigger("onStart");
            var n = this.navigation;
            var p = this.params;
            this.navigation = {};
            this.params = {};
            // Empty navigation and params before call the hashChanged method at the starting phase.
            // This will force the module to call all its event handlers
            console.log("Module started: " + this.id);
            this.hashChanged(n, p, this.hash, System.getHashParam(this.moduleIdentifier));

            var index = System.notYetStarted.indexOf(this.id);
            if (index > -1) {
               System.notYetStarted.splice(index, 1);
            }
         },
         dispose: function ()
         {

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
            id = this.id + '/' + id;

            //if forceReload is true, then init the module again
            if (/*!object && */!forceReload && this.modules[id]) {
               // Add the module to notYetStarted list so it can be started by startLastLoadedModule method
               System.notYetStarted.push(id);
               return this.modules[id];
            }

            if (typeof (object) === "function") {
               module = $.extend(true, {}, System.MODULE_ABSTRACT);
               object.call(module);
            } else {
               module = $.extend(true, {}, System.MODULE_ABSTRACT, object || {});
            }

            module.id = id;

            var modNav = this.navigation[module.moduleIdentifier] ? this.navigation[module.moduleIdentifier].slice(1) : [];
            var newNav = $.extend(true, {}, this.navigation);
            newNav[module.moduleIdentifier] = modNav;

            System.modules[id] = this.modules[id] = module;
            System.notYetStarted.push(id);

            // Set module hash for this module when its inited
            // module hash will be set in the hashChanged method as well
            // if current navigation path is equal to this module id
            //module.hash = System.modulesHashes[id.replace("system/", "")] = module.moduleIdentifier + "=" + id.replace("system/", "");

            module.init(newNav, this.params);
            //console.log("Module is inited: " + id);

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
         on: function (id, handler)
         {
            this.hashListeners[id] = handler;
            //System.main.newHandler = true;
            /*if (this.navigation[id])
             {
             var args = [];
             args.push(this.navigation[id]);
             for (var i = 0; i < this.navigation[id].length; ++i)
             {
             args.push(this.navigation[id][i]);
             }
             handler.apply(this, args);
             }*/
         },
         setParam: function (param, value) {
            var paramObject = {};
            paramObject[param] = value;
            System.setHashParameters(paramObject);
         },
         setParamIfNone: function (param, value) {
            if (!this.params[param]) {
               var paramObject = {};
               paramObject[param] = value;
               System.setHashParameters(paramObject, true);
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
         hashChanged: function (navigation, params, hashValue, fullNavPath) {
            var _this = this;
            var moduleNav = navigation;
            this.hashHandler.call(this, navigation, params);
            //console.log(navigation)
            $.each(navigation, function (key, value) {
               var navHandler = _this.hashListeners[key];
               // Call same level events handlers    
               //console.log(key, _this.navigation[key], value.join("/"))
               if (navHandler) {
                  //console.log(key, _this.navigation[key].join("/"), value.join("/"))
                  if (_this.navigation[key] && _this.navigation[key].join("/") === value.join("/")) {
                     //console.log("rid")
                     return;
                  }
                  var args = [];
                  args.push(value);
                  for (var i = 0; i < value.length; ++i)
                  {
                     //i is always valid index in the arguments object
                     args.push(value[i]);
                  }
                  navHandler.apply(_this, args);
               }
            });
            this.navigation = navigation;
            this.params = params;
            //this.hash = hashValue;

            if (this.moduleIdentifier && navigation[this.moduleIdentifier] && navigation[this.moduleIdentifier][0])
            {
               // Set the app.activeModule according to the current navigation path
               if (this.modules[this.id + "/" + navigation[this.moduleIdentifier][0]]) {
                  /*System.app.activeModule = */this.activeModule = this.modules[this.id + "/" + navigation[this.moduleIdentifier][0]];
               }
            } else {
               // if current nav path is equal to this module id, then set the current hash value as module hash 
               // for the current module id. This keeps the hash parameters consistent 
               // when module get inited on page (re)load or after the hash value is already set
               /*var urlId = this.id.replace("system/", "");
                if (urlId.length && System.getHashParam("app") === urlId) {
                System.modulesHashes[urlId] = window.location.hash.substr(1);
                }*/
               this.activeModule = null;
            }
            //console.log(this.id, this.activeModule);

            // if full nav pointing to this module, then update the hash value of the current nav
            /*if (this.id === "system/" + fullNavPath) {
             //console.log(this.id, fullNavPath , hashValue);
             System.modulesHashes[fullNavPath] = hashValue;
             }*/
            //alert(this.id);
            //console.log(this.id, this.params)
            fullNavPath = this.params["app"];
            if (this.id === "system/" + fullNavPath && System.app.activeModule !== this) {
               System.app.activeModule = this;
               //console.log(System.getHashParam("app"), this.id, window.location.hash);
               //alert(window.location.hash + "===" + System.modulesHashes[fullNavPath] + " active >>> " + this.id);
               //window.location.hash = System.modulesHashes[fullNavPath];

               //System.modulesHashes[fullNavPath] = hashValue;
            }

            if (this.activeModule)
            {
               // Remove first part of navigation in order to force activeModule to only react to events at its level and higher 
               var modNav = navigation[this.moduleIdentifier].slice(1);
               moduleNav = $.extend(true, {}, navigation);
               moduleNav[this.moduleIdentifier] = modNav;
               if (!this.activeModule.started) {
                  //alert("system." + navigation[this.moduleIdentifier][0])
                  return;
               }

               // Call module level events handlers
               this.activeModule.hashChanged(moduleNav, this.params, hashValue, fullNavPath || navigation[this.moduleIdentifier].join("/"));
            }
         },
         hashHandler: function (nav, params) {
            /*if (this.activeModule && !e.isDefaultPrevented())
             this.activeModule.hashHandler(e, data);*/
         }
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
      module: function (id, object)
      {
         return this.app.module(id, object);
      },
      // Open app
      openApp: function (app, reload)
      {
         // Start the opening app process
         this.onLoadQueue.push(app);
         this.appLoaderService();
      },
      appPattern: /var app =\s*{([\s\S]*)}/gi,
      appLoaderService: function () {
         var self = this;
         if (self.currentOnLoad)
            return;
         if (self.onLoadQueue[0] && self.currentOnLoad !== self.onLoadQueue[0])
         {
            self.currentOnLoad = self.onLoadQueue[0];
            //console.log("Loading app: " + self.currentOnLoad.id);
            var package = self.currentOnLoad.package,
                    id = self.currentOnLoad.id,
                    file = self.currentOnLoad.file,
                    data = self.currentOnLoad.data;

            if (self.onLoadApp(self.currentOnLoad)) {
               /*if (System.modules["system/" + id]) {
                  return;
               }*/
               self.loadingAppXHR = self.load(package + '/' + id + '/' + file, data).done(function (response, status) {
                  //if (self.navHashes["system/" + id])
                  //window.location.hash = self.navHashes["system/" + id];
                  //alert("app current nav hash: "+self.navHashes[id]);
                  var html = $(response);
                  var scripts = html.filter("script").detach();
                  $("head").append(scripts);
                  //var html = res;
                  //System.apps[id] = $.extend({}, System.module, self.apps[id]);
                  //System.activityTree.unshift(System.apps[id]);
                  var module = System.module(id);

                  self.onAppLoaded(module, html, status);
                  //
                  var modNav = [];
                  if (self.app.navigation[module.moduleIdentifier])
                     modNav = self.app.navigation[module.moduleIdentifier].slice(1);
                  var newNav = $.extend(true, {}, self.app.navigation);
                  newNav[module.moduleIdentifier] = modNav;
                  this.activeModule = module;
                  if (!this.activeModule.inited)
                  {
                     //this.activeModule.inited = true;
                     //this.activeModule.navigation = newNav;
                     //this.activeModule.params = self.main.params;
                     this.activeModule.init.call(module, newNav, self.app.params);
                     //this.activeModule.hashChanged(newNav, self.main.params);
                  }
                  //this.activeModule.trigger("onActive");
                  //
                  self.currentOnLoad = null;
                  self.onLoadQueue.shift();
                  self.appLoaderService();
               });
            } else
            {
               //window.location.hash = '';
               self.currentOnLoad = null;
               self.onLoadQueue.shift();
            }
         }
         setTimeout(function () {
            self.appLoaderService();
         }, 1);
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
         var self = this;
         var detect = function () {
            //console.log(self.app.oldHash, window.location.hash)
            if (self.app.oldHash !== window.location.hash || self.app.newHandler) {
               //alert("change detected");
               self.app.newHandler = false;
               var hashValue = window.location.hash;
               hashValue = hashValue.replace(/^#\/?/igm, '');

               var navigation = {};
               var params = {};
               hashValue.replace(/([^&]*)=([^&]*)/g, function (m, k, v) {
                  navigation[k] = v.split("/").filter(Boolean);
                  params[k] = v;
               });

               self.setModuleHashValue(navigation, params, hashValue);
               self.app.hashChanged(navigation, params, hashValue); // System

               self.app.oldHash = '#' + hashValue;
            }
         };

         detect();
         clearInterval(this.hashChecker);
         this.hashChecker = setInterval(function () {
            detect();
         }, 50);
      },
      setURLHash: function (hash)
      {
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
         return this.app.navigation[key] || [];
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
            //alert(System.modulesHashes[nav] + " YES");
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
            System.modulesHashes[nav] = hashValue = "app=" + nav;
         } else if (nav && System.modulesHashes[nav]) {
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

         /*if (System.modulesHashes[System.getHashParam("app")] && System.modulesHashes[System.getHashParam("app")] !== newHash) {
          
          alert(System.getHashParam("app") + " " + newHash);
          //System.modulesHashes[System.getHashParam("app")] = newHash;
          }*/

         /*if (moduleId && this.modulesHashes[moduleId]) {
          this.modulesHashes[moduleId] = newHash;
          //console.log(moduleId, hashValue, newHash);
          }*/

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
      ajax: function (href, onDone) {

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
         this.onLoadQueue = [];
         this.currentOnLoad = null;
      },
      startLastLoadedModule: function () {
         if (this.notYetStarted.length > 0) {
            this.modules[this.notYetStarted[this.notYetStarted.length - 1]].start();
         }
      },
      init: function () {
         this.app = $.extend(true, {}, System.MODULE_ABSTRACT);
         this.app.moduleIdentifier = this.moduleIdentifier;
         this.app.id = "system";
         //this.activeModule = this.app;
      }
   };
}());