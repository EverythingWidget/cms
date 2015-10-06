/**
 * 
 * @type Object
 */
var System = System ||
        {
           moduleIdentifier: "app",
           modules: {},
           appPathfiledName: null,
           activityTree: [],
           onLoadQueue: [],
           activeModule: null,
           /*activeHashHandler: function () {
            },*/
           UI: {},
           MODULE_ABSTRACT:
                   {
                      inited: false,
                      moduleIdentifier: "app",
                      navigation: {},
                      params: {},
                      modules: {},
                      activeModule: null,
                      init: function ()
                      {
                         this.inited = true;
                         this.trigger("onInit");
                      },
                      start: function ()
                      {
                         this.trigger("onStart");
                         this.hashChanged(this.navigation, this.params);
                      },
                      dispose: function ()
                      {

                      },
                      /**
                       * 
                       * @param {String} id
                       * @param {Object} object
                       * @returns {System.MODULE_ABSTRACT}
                       */
                      module: function (id, object)
                      {
                         if (!object && this.modules[id])
                            return this.modules[id];
                         this.modules[id] = $.extend(true, {}, System.MODULE_ABSTRACT, object || {});
                         this.modules[id].id = id;
                         return this.modules[id];
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
                      trigger: function (event, args)
                      {
                         if (typeof (this[event]) === "function")
                         {
                            this[event].apply(this, args);
                         }
                      },
                      hashChanged: function (navigation, params)
                      {
                         var self = this;

                         var newNav = navigation;
                         this.hashHandler.call(this, navigation, params);

                         $.each(navigation, function (key, value)
                         {
                            var navHandler = self.hash[key];
                            // Call same level events handlers                            
                            if (navHandler)
                            {
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

                         if (this.moduleIdentifier && navigation[this.moduleIdentifier])
                         {
                            // Select activeModule according to moduleIdentifier
                            this.activeModule = this.modules[navigation[this.moduleIdentifier][0]];
                         }
                         else
                            this.activeModule = null;
                         if (this.activeModule)
                         {
                            // Remove first part of navigation in order to force activeModule to only react to events at its level and higher 
                            var modNav = navigation[this.moduleIdentifier].slice(1);
                            newNav = $.extend(true, {}, navigation);
                            newNav[this.moduleIdentifier] = modNav;

                            if (!this.activeModule.inited)
                            {
                               this.activeModule.init(newNav, this.params);
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
           registerApp: function (id, object)
           {
              this.modules[id] = $.extend(true, {}, System.MODULE_ABSTRACT, object);
           },
           /**
            * 
            * @param {String} id
            * @param {Object} object
            * @returns {System.ABSTRACT_MODULE}
            */
           module: function (id, object)
           {
              return this.main.module(id, object);
           },
           // Open app
           openApp: function (app, reload)
           {
              // Start the opening app process
              this.onLoadQueue.push(app);
              this.appLoaderService();
           },
           appPattern: /var app =\s*{([\s\S]*)}/gi,
           appLoaderService: function ()
           {
              var self = this;
              if (self.currentOnLoad)
                 return;
              if (self.onLoadQueue[0] && self.currentOnLoad != self.onLoadQueue[0])
              {
                 self.currentOnLoad = self.onLoadQueue[0];
                 console.log("Loading app: " + self.currentOnLoad.id);
                 var package = self.currentOnLoad.package;
                 var id = self.currentOnLoad.id;
                 var file = self.currentOnLoad.file;
                 var data = self.currentOnLoad.data;
                 if (self.onLoadApp(self.currentOnLoad))
                 {
                    $.get(package + '/' + id + '/' + file, data).done(function (response, status)
                    {
                       if (self.navHashes[id])
                          window.location.hash = self.navHashes[id];

                       //alert("app current nav hash: "+self.navHashes[id]);
                       var scripts = $(response).filter("script").detach();
                       var html = $(response);
                       $("body").append(scripts);
                       //var html = res;
                       //System.apps[id] = $.extend({}, System.module, self.apps[id]);
                       //System.activityTree.unshift(System.apps[id]);
                       var module = System.module(id);
                       self.onAppLoaded(module, html);
                       //
                       var modNav = self.main.navigation[module.moduleIdentifier].slice(1);
                       var newNav = $.extend(true, {}, self.main.navigation);
                       newNav[module.moduleIdentifier] = modNav;
                       this.activeModule = module;

                       if (!this.activeModule.inited)
                       {
                          //this.activeModule.inited = true;
                          this.activeModule.navigation = newNav;
                          this.activeModule.params = self.main.params;
                          this.activeModule.init.call(module, newNav, self.main.params);
                          //this.activeModule.hashChanged(newNav, self.main.params);
                       }
                       this.activeModule.trigger("onActive");
                       //
                       self.currentOnLoad = null;
                       self.onLoadQueue.shift();
                       self.appLoaderService();
                    });
                 }
                 else
                 {
                    //window.location.hash = '';
                    self.currentOnLoad = null;
                    self.onLoadQueue.shift();
                 }
              }
              setTimeout(function ()
              {
                 self.appLoaderService();
              }, 1);
           },
           /** This method will be called whenever System attempts to load an app
            * 
            * @param {Object} app
            * @returns {Boolean} True if the app should be loaded and false if the app may not be loaded
            */
           onLoadApp: function (app)
           {
              // Example: show a loading animation
              return true;
           },
           onAppLoaded: function (app, data)
           {
              // Example: add the content into the DOM
           },
           // Close App
           closeApp: function (appId)
           {
              if (this.onCloseApp(System.modules[appId]))
              {
                 System.modules[appId].blur();
                 var pos = this.activityTree.lastIndexOf(appId);
                 if (pos !== -1)
                    this.activityTree.splice(pos, 1);
                 System.modules[appId].dispose();
                 this.onAppClosed(System.modules[appId]);
              }
           },
           onCloseApp: function (app)
           {

           },
           onAppClosed: function ()
           {
              return true;
           },
           navHashes: {},
           hashChecker: null,
           /*hashChanged: function ()
            {
            var self = this;
            console.log([this.navigation, this.params]);
            var e = $.Event("hashchange");
            $.each(this.navigation, function (key, value)
            {
            
            var navHandler = null;
            if (navHandler = self.navs[key])
            {
            var args = [];
            args.push(value);
            for (var i = 0; i < value.length; ++i)
            {
            //i is always valid index in the arguments object
            args.push(value[i]);
            }
            
            //value.unshift(value);
            navHandler.apply(null, args);
            }
            });
            
            if (this.navigation[this.moduleIdentifier])
            this.activeModule = this.modules[this.navigation[this.moduleIdentifier][0]];
            if (this.activeModule)
            {
            this.activeModule.navigation = this.navigation;
            this.activeModule.params = this.params;
            this.activeModule.hashChanged();
            }
            
            this.hashHandler.call(e, this.navigation, this.params);
            },
            navs: {},*/
           /**
            * 
            * @param {String} id
            * @param {Function} handler
            */
           on: function (id, handler)
           {
              this.main.on.call(this.main, id, handler);
           },
           hashHandler: function (nav, params)
           {

           },
           navigation: {},
           params: {},
           start: function ()
           {
              var self = this;

              var detect = function ()
              {
                 if (self.main.oldHash !== window.location.hash || self.main.newHandler)
                 {
                    self.main.oldHash = window.location.hash;
                    self.main.newHandler = false;
                    var hashValue = window.location.hash;
                    hashValue = hashValue.replace(/^#\/?/igm, '');


                    /*if (self.main.activeModule)
                    {
                       self.navHashes[self.main.activeModule.id] = hashValue;
                       
                    }*/

                    /*self.main.navigation = {};
                     self.main.params = {};
                     hashValue.replace(/([^&]*)=([^&]*)/g, function (m, k, v)
                     {
                     self.main.navigation[k] = v.split("/").filter(Boolean);
                     self.main.params[k] = v;
                     });*/

                    var navigation = {};
                    var params = {};
                    hashValue.replace(/([^&]*)=([^&]*)/g, function (m, k, v)
                    {
                       navigation[k] = v.split("/").filter(Boolean);
                       params[k] = v;
                    });

                    self.main.hashChanged(navigation, params); // System
                 }
              };
              detect();
              //this.systemModule = $.extend({}, System.MODULE_ABSTRACT);
              clearInterval(this.hashChecker);
              this.hashChecker = setInterval(function ()
              {
                 detect();
              }, 50);
           },
           getHashParam: function (key, hashName)
           {

           },
           getHashNav: function (key, hashName)
           {
              return this.main.navigation[key];
           },
           // Set parameters for current app/nav if not specified
           setHashParameters: function (parameters, replace, clean)
           {

              this.lastHashParams = parameters;
              var hashValue = window.location.hash;

              // if the app found then set the params for app otherwise set the param for default app (main.mainModule)
              var app = (''+parameters[this.main.moduleIdentifier]).split('/')[0] || this.main.activeModule.id;
              var mI = this.main.moduleIdentifier;
              //if (this.modules[app])
              //mI = this.modules[app].moduleIdentifier;
              //alert("navHAsh: " + app + " > " + parameters[this.main.moduleIdentifier])
              if (app)
              {
                 if (!this.navHashes[app])
                 {
                    //this.navHashes[app] = mI + "=" + parameters[this.main.moduleIdentifier];
                    this.navHashes[app] = hashValue;
                 }
                 hashValue = this.navHashes[app];
              }

              if (hashValue.indexOf("#") !== -1)
              {
                 hashValue = hashValue.substring(1);
              }
              var pairs = hashValue.split("&");
              var newHash = "#";
              var and = false;
              hashValue.replace(/([^&]*)=([^&]*)/g, function (m, k, v)
              {
                 if (parameters[k] != null)
                 {
                    newHash += k + "=" + parameters[k];
                    newHash += '&';
                    and = true;
                    delete parameters[k];
                 }
                 else if (!parameters.hasOwnProperty(k) && !clean)
                 {
                    newHash += k + "=" + v;
                    newHash += '&';
                    and = true;
                 }
              });
              // New keys
              $.each(parameters, function (key, value)
              {
                 if (key && value)
                 {
                    newHash += key + "=" + value + "&";
                    and = true;
                 }
              });
              newHash = newHash.replace(/\&$/, '');
              // set newHash for corresponding hash name if it has been passed
             // if (app)
              //{
                 //this.navHashes[app] = newHash.replace(/\&$/, '');
                 //alert(customHashes[hashName].hash);
              //}
              // set url hash if no hash name specified
              //else
              if (app)
              {
                 this.navHashes[app] = newHash;
                 
              }
              if (replace)
              {
                 window.location.replace(('' + window.location).split('#')[0] + newHash);
              }
              else
                 window.location.hash = newHash.replace(/\&$/, '');
           },
           init: function ()
           {
              this.main = $.extend(true, {}, System.MODULE_ABSTRACT);
              this.main.moduleIdentifier = this.moduleIdentifier;
              this.main.id = "main";
           }

        };