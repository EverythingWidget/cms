/**
 * 
 * @type @exp;System
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
           MODULE_ABSTRACT:
                   {
                      moduleIdentifier: "app",
                      navigation: {},
                      params: {},
                      modules: {},
                      activeModule: null,
                      init: function ()
                      {

                      },
                      input: function (data)
                      {

                      },
                      output: function ()
                      {
                      },
                      focus: function ()
                      {
                         //System.activityTree.unshift(this);
                         System.activeHashHandler = this.hashHandler;
                      },
                      blur: function ()
                      {
                         //System.activityTree.splice(0, 1);
                      },
                      dispose: function ()
                      {

                      },
                      module: function (id, object)
                      {
                         this.modules[id] = $.extend(true, {}, System.MODULE_ABSTRACT, object || {});
                         this.modules[id].name = id;
                         return this.modules[id];
                      },
                      hash: {},
                      on: function (id, handler)
                      {
                         this.hash[id] = handler;
                         //System.main.newHandler = true;
                         if (this.navigation[id])
                         {
                            var args = [];
                            args.push(this.navigation[id]);
                            for (var i = 0; i < this.navigation[id].length; ++i)
                            {
                               args.push(this.navigation[id][i]);
                            }
                            handler.apply(null, args);
                         }
                      },
                      hashChanged: function (navigation, params)
                      {
                         var self = this;

                         var newNav = navigation;
                         this.hashHandler.call(this, navigation, params);

                         $.each(navigation, function (key, value)
                         {
                            var navHandler = null;
                            if (navHandler = self.hash[key])
                            {

                               var args = [];
                               args.push(value);
                               for (var i = 0; i < value.length; ++i)
                               {
                                  //i is always valid index in the arguments object
                                  args.push(value[i]);
                               }
                               //value.unshift(value);
                               //alert(self.name + " " + JSON.stringify(value) + " " + JSON.stringify(self.navigation[key]))
                               //if (JSON.stringify(value) !== JSON.stringify(self.navigation[key]))
                               navHandler.apply(null, args);
                            }
                         });

                         console.log(this.name, this.modules, this.moduleIdentifier)

                         if (this.moduleIdentifier && navigation[this.moduleIdentifier])
                         {
                            this.activeModule = this.modules[navigation[this.moduleIdentifier][0]];
                            //console.log("ac " + this.moduleIdentifier + " " + this.navigation[this.moduleIdentifier][0])
                         }
                         else
                            this.activeModule = null;
                         if (this.activeModule)
                         {
                            if (!this.activeModule.inited)
                            {
                               this.activeModule.inited = true;
                               this.activeModule.init.call(this.activeModule);
                            }

                            var modNav = navigation[this.moduleIdentifier].slice(1);
                            newNav = $.extend(true, {}, navigation);
                            newNav[this.moduleIdentifier] = modNav;

                            //if (JSON.stringify(this.activeModule.navigation) !== JSON.stringify(newNav) || JSON.stringify(this.activeModule.params) !== JSON.stringify(this.params))
                            //{
                            //console.log(JSON.stringify(this.activeModule.navigation), JSON.stringify(newNav))
                            //this.activeModule.navigation = newNav;
                            //this.activeModule.params = this.params;
                            this.navigation = navigation;
                            this.params = params;
                            this.activeModule.hashChanged(newNav, this.params);

                            //}
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
                       var scripts = $(response).filter("script").detach();
                       var html = $(response);
                       $("body").append(scripts);
                       //var html = res;
                       //System.apps[id] = $.extend({}, System.module, self.apps[id]);
                       //System.activityTree.unshift(System.apps[id]);
                       self.onAppLoaded(self.modules[id], html);
                       //
                       System.modules[id].init();
                       System.modules[id].focus();
                       //
                       self.currentOnLoad = null;
                       self.onLoadQueue.shift();
                       self.appLoaderService();
                    });
                 }
                 else
                 {
                    window.location.hash = '';
                    self.currentOnLoad = null;
                    self.onLoadQueue.shift();
                 }
              }
              setTimeout(function ()
              {
                 self.appLoaderService();
              }, 1);
           },
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
              return this.navigation[key];
           },
           // Set parameters for current app/nav if not specified
           setHashParameters: function (parameters, appId, clean)
           {
              this.lastHashParams = parameters;
              var hashValue = window.location.hash;
              var app = parameters[this.main.moduleIdentifier] || this.params[this.main.moduleIdentifier];
              var mI = this.main.moduleIdentifier;
              //if (this.modules[app])
              //mI = this.modules[app].moduleIdentifier;
              if (app)
              {
                 if (!this.navHashes[app])
                    this.navHashes[app] = mI + "=" + app;
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
              // set newHash for corresponding hash name if it has been passed
              if (app)
              {
                 this.navHashes[app] = newHash.replace(/\&$/, '');
                 //alert(customHashes[hashName].hash);
              }
              // set url hash if no hash name specified
              //else
              window.location.hash = newHash.replace(/\&$/, '');
           },
           init: function ()
           {
              this.main = $.extend(true, {}, System.MODULE_ABSTRACT);
              this.main.moduleIdentifier = this.moduleIdentifier;
              this.main.name = "main";
           }

        };