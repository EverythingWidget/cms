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
         modules: {},
         activeModule: null,
         init: function (navigations, params)
         {
            this.inited = true;
            this.navigation = navigations;
            this.params = params;
            this.trigger("onInit");
         },
         start: function ()
         {
            this.started = true;
            this.trigger("onStart");
            this.hashChanged(this.navigation, this.params);
            var index = System.notYetStarted.indexOf(this.id);
            if (index > -1) {
               System.notYetStarted.splice(index, 1);
            }
         },
         dispose: function ()
         {

         },
         /**
          * 
          * @param {String} id
          * @param {Object} object
          * @returns {sys.MODULE_ABSTRACT}
          */
         module: function (id, object)
         {
            var module;
            id = this.id + '.' + id;
            if (!object && this.modules[id])
               return this.modules[id];
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
            module.init(newNav, this.params);
            System.notYetStarted.push(id);

            return module;
         },
         hash: {},
         data: {},
         /**
          * 
          * @param {String} id
          * @param {Function} handler
          * @returns {undefined}
          */
         on: function (id, handler)
         {
            this.hash[id] = handler;
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
         hashChanged: function (navigation, params) {
            var self = this;
            var newNav = navigation;
            this.hashHandler.call(this, navigation, params);
            $.each(navigation, function (key, value) {
               var navHandler = self.hash[key];
               // Call same level events handlers                            
               if (navHandler) {
                  var args = [];
                  args.push(value);
                  for (var i = 0; i < value.length; ++i)
                  {
                     //i is always valid index in the arguments object
                     args.push(value[i]);
                  }
                  navHandler.apply(self, args);
               }
            });
            this.navigation = navigation;
            this.params = params;
            //alert(navigation[this.moduleIdentifier][0]+"----");

            if (this.moduleIdentifier && navigation[this.moduleIdentifier])
            {
               // Select activeModule according to moduleIdentifier
               this.activeModule = this.modules["system." + navigation[this.moduleIdentifier][0]];
            } else
               this.activeModule = null;
            //console.log(this.id, this.activeModule, this.modules);

            if (this.activeModule)
            {
               // Remove first part of navigation in order to force activeModule to only react to events at its level and higher 
               var modNav = navigation[this.moduleIdentifier].slice(1);
               newNav = $.extend(true, {}, navigation);
               newNav[this.moduleIdentifier] = modNav;
               if (!this.activeModule.started) {
                  //alert("system." + navigation[this.moduleIdentifier][0])
                  return;
               }

               // Call module level events handlers
               this.activeModule.hashChanged(newNav, this.params);
            }
         },
         hashHandler: function (nav, params)
         {
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
         if (self.onLoadQueue[0] && self.currentOnLoad != self.onLoadQueue[0])
         {
            self.currentOnLoad = self.onLoadQueue[0];
            //console.log("Loading app: " + self.currentOnLoad.id);
            var package = self.currentOnLoad.package;
            var id = self.currentOnLoad.id;
            var file = self.currentOnLoad.file;
            var data = self.currentOnLoad.data;
            if (self.onLoadApp(self.currentOnLoad))
            {
               self.loadingAppXHR = self.load(package + '/' + id + '/' + file, data).done(function (response, status) {
                  if (self.navHashes["system." + id])
                     window.location.hash = self.navHashes["system." + id];
                  //alert("app current nav hash: "+self.navHashes[id]);
                  var html = $(response);
                  var scripts = html.filter("script").detach();

                  $("body").append(scripts);
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
      navHashes: {},
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
            if (self.app.oldHash !== window.location.hash || self.app.newHandler) {
               self.app.oldHash = window.location.hash;
               self.app.newHandler = false;
               var hashValue = window.location.hash;
               hashValue = hashValue.replace(/^#\/?/igm, '');

               var navigation = {};
               var params = {};
               hashValue.replace(/([^&]*)=([^&]*)/g, function (m, k, v) {
                  navigation[k] = v.split("/").filter(Boolean);
                  params[k] = v;
               });
               self.app.hashChanged(navigation, params); // System
            }
         };
         detect();
         //this.systemModule = $.extend({}, System.MODULE_ABSTRACT);
         clearInterval(this.hashChecker);
         this.hashChecker = setInterval(function () {
            detect();
         }, 20);
      },
      getHashParam: function (key, hashName) {
      },
      getHashNav: function (key, hashName) {
         return this.app.navigation[key] || [];
      },
      // Set parameters for current app/nav if not specified
      setHashParameters: function (parameters, replace, clean) {
         this.lastHashParams = parameters;
         var hashValue = window.location.hash;
         // if the app found then set the params for app otherwise set the param for default app (main.mainModule)
         var app = ('' + parameters[this.app.moduleIdentifier]).split('/')[0] || this.app.activeModule.id;
         var mI = this.app.moduleIdentifier;
         //if (this.modules[app])
         //mI = this.modules[app].moduleIdentifier;
         //alert("navHAsh: " + app + " > " + parameters[this.main.moduleIdentifier])
         if (app) {
            if (!this.navHashes[app]) {
               //this.navHashes[app] = mI + "=" + parameters[this.main.moduleIdentifier];
               this.navHashes[app] = hashValue;
            }
            hashValue = this.navHashes[app];
         }

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

         if (app) {
            this.navHashes[app] = newHash;
         }

         if (replace) {
            window.location.replace(('' + window.location).split('#')[0] + newHash);
         } else {
            window.location.hash = newHash.replace(/\&$/, '');
         }
      },
      load: function (href, onDone) {
         var _this = this,
                 id = new Date().valueOf();
         while (id === this.oldRequestCreationId) {
            id = new Date().valueOf();
         }

         var request = $.get(href, function (response) {
            //console.log(href);
            if ("function" === typeof (onDone)) {
               onDone.call(this, response);
            }

            if (request.creationId) {
               delete _this.activeRequests[request.creationId];
            }
         });

         request.creationId = id;
         this.oldRequestCreationId = id;
         this.activeRequests[id] = request;
         return request;
      },
      abortAllReqests: function () {
         for (var request in this.activeRequests) {
            this.activeRequests[request].abort();
            console.log("aborted", this.activeRequests[request]);
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
      }
   };
}());