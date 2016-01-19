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
      onModuleLoaded: {},
      /*activeHashHandler: function () {
       },*/
      UI: {},
      MODULE_ABSTRACT: {
         inited: false,
         started: false,
         active: false,
         moduleIdentifier: "app",
         navigation: {},
         params: {},
         html: "",
         modules: {},
         installModules: [],
         activeModule: null,
         init: function (navigations, params, html)
         {
            this.inited = true;
            //this.navigation = navigations;
            //this.params = params;
            //this.html = html;
            this.trigger("onInit");

            this.installModules.forEach(function (lib) {
               //alert("install: " + lib.id);
               System.loadModule(lib/*, function () {
                alert("install completed: " + lib.id);
                }*/);
            });
         },
         start: function ()
         {
            this.started = true;
            this.active = true;
            //System.app.activeModule = this;
            this.trigger("onStart");
            /*var modNav = this.navigation[this.moduleIdentifier] ? this.navigation[this.moduleIdentifier].slice(1) : [];
             var newNav = $.extend(true, {}, this.navigation);
             newNav[this.moduleIdentifier] = modNav;*/
            //var modNav = System.app.navigation[this.moduleIdentifier] ? System.app.navigation[this.moduleIdentifier].slice(1) : [];
            var newNav = $.extend(true, {}, System.app.navigation);
            var st = "system/" + System.app.params[this.moduleIdentifier];
            var napPath = st.indexOf(this.id) === 0 ? st.substr(this.id.length).split("/").filter(Boolean) : [];

            newNav[this.moduleIdentifier] = napPath;
            var n = newNav;
            var p = System.app.params;
            this.navigation = {};
            this.params = {};
            // Empty navigation and params before call the hashChanged method at the starting phase.
            // This will force the module to call all its event handlers
            //console.log("Module started: " + this.id, n, p);
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
            if (!object && !forceReload/* && this.modules[id]*/) {
               // Add the module to notYetStarted list so it can be started by startLastLoadedModule method
               //if (this.modules[id])
               System.notYetStarted.push(id);
               return this.modules[id];
            }

            if (this.modules[id]) {
               //alert("already registered: " + id);
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
         setParam: function (param, value, replace) {
            var paramObject = {};
            paramObject[param] = value;
            System.setHashParameters(paramObject, replace);
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
         hashChanged: function (navigation, params, hashValue) {
            var _this = this;
            var moduleNavigation = navigation;

            var fullNavPath = params["app"];

            //console.log(this.id,"system/" + fullNavPath,this.params,System.app.activeModule)
            if (this.id === "system/" + fullNavPath/* && System.app.activeModule !== this*/) {
               System.app.activeModule = this;

            } else {
               System.app.activeModule = null;
               this.active = false;
            }

            //alert(this.id +" --- "+ "system/" + fullNavPath+" "+this.active)

            this.hashHandler.call(this, navigation, params);
            //console.log(navigation)
            var allNavigations = $.extend({}, this.navigation, navigation);
            //console.log(allNavs, navigation);

            if (System.app.activeModule && this.active && System.app.activeModule.id === _this.id) {
               //console.log(_this.id, allNavigations, params["app"]);
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

               if (navHandler && navigation["app"]) {
                  if (navigation["app"]) {

                     var currentKeyValue = _this.navigation["app"] ? _this.navigation["app"].join("/") : [];

                     if (navigation["app"] && currentKeyValue !== navigation["app"].join("/")) {
                        //console.log(navigation["app"].join("/"));
                        //alert(currentKeyValue)
                        var args = [];
                        args.push(navigation["app"]);
                        for (var i = 0, len = navigation["app"].length; i < len; ++i) {
                           //i is always valid index in the arguments object
                           args.push(navigation["app"][i]);
                        }
                        //if (!System.app.activeModule || System.app.activeModule === _this)
                        //console.log(System.app.activeModule, "APP", args);
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
               if (this.modules[this.id + "/" + navigation[this.moduleIdentifier][0]]) {
                  /*System.app.activeModule = */this.activeModule = this.modules[this.id + "/" + navigation[this.moduleIdentifier][0]];
               }
            } else {
               this.activeModule = null;
            }




            if (this.activeModule)
            {
               // Remove first part of navigation in order to force activeModule to only react to events at its level and higher 
               var modNav = navigation[this.moduleIdentifier].slice(1);
               moduleNavigation = $.extend(true, {}, navigation);
               moduleNavigation[this.moduleIdentifier] = modNav;
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
      /*openApp: function (app, reload)
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
            //console.log("Loading app: " + self.currentOnLoad.id);0px 1px 1px rgba(0,0,0,.12) , 0 0px 3px rgba(0,0,0,.14)
            var package = self.currentOnLoad.package,
                    id = self.currentOnLoad.id,
                    moduleName = self.currentOnLoad.module,
                    file = self.currentOnLoad.file,
                    data = self.currentOnLoad.data;

            if (self.onLoadApp(self.currentOnLoad)) {
               if (System.modules["system/" + id]) {
                  $("#system_" + id.replace(/[\/-]/g, "_")).remove();
                  //return;
               }
               self.loadingAppXHR = self.load(self.url || (package + '/' + moduleName + '/' + file), data).done(function (response, status) {
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
                  scripts.attr("id", module.id.replace(/[\/-]/g, "_"));

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
      },*/
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
            if (self.app.oldHash !== window.location.hash/* || self.app.newHandler*/) {
               var hashValue = window.location.hash,
                       navigation = {},
                       params = {};

               hashValue = hashValue.replace(/^#\/?/igm, '');

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
      loadModule: function (mod, onDone) {
         System.onModuleLoaded["system/" + mod.id] = onDone;

         if (System.modules["system/" + mod.id]) {
            //alert("loaded so call onDone " + mod.id + " " + System.onModuleLoaded["system/" + mod.id]);
            if ("function" === typeof (System.onModuleLoaded["system/" + mod.id])) {
               //onDone.call(this, System.modules["system/" + mod.id], System.modules["system/" + mod.id].html);
               System.onModuleLoaded["system/" + mod.id].call(this, System.modules["system/" + mod.id],
                       System.modules["system/" + mod.id].html);

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

            var html = $(response);
            var scripts = html.filter("script").detach();

            $("head").append(scripts);
            //var html = res;
            //System.apps[id] = $.extend({}, System.module, self.apps[id]);
            //System.activityTree.unshift(System.apps[id]);
            //console.log(System.app.modules);
            //var module = System.module(mod.id);
            if (!System.modules["system/" + mod.id]) {
               alert("Invalid module: " + mod.id);
               return;
            }

            System.modules["system/" + mod.id].html = html;

            scripts.attr("id", System.modules["system/" + mod.id].id.replace(/[\/-]/g, "_"));

            if ("function" === typeof (System.onModuleLoaded["system/" + mod.id])) {
               //onDone.call(this, System.modules["system/" + mod.id], response);
               System.onModuleLoaded["system/" + mod.id].call(this, System.modules["system/" + mod.id], html);
               System.onModuleLoaded["system/" + mod.id] = null;
            }

            /*if (System.startAfterLoad === System.modules["system/" + mod.id].id) {
             System.modules["system/" + mod.id].start();
             }*/
         });
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
         this.app.moduleIdentifier = this.moduleIdentifier;
         this.app.id = "system";
         this.app.installModules = mods;
         this.app.init({}, {}, "");
         //this.activeModule = this.app;
      }
   };
}());