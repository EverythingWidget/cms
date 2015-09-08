
var System = System ||
        {
           modules: {},
           appPathfiledName: null,
           activityTree: [],
           onLoadQueue: [],
           activeModule: null,
           /*activeHashHandler: function () {
            },*/
           MODULE_ABSTRACT:
                   {
                      navigation: {},
                      params: {},
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
                      hashChanged: function ()
                      {
                         var e = $.Event("hashchange");
                         this.hashHandler.call(e, this.navigation, this.params);
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
              this.modules[id] = $.extend({}, System.MODULE_ABSTRACT, object);
           },
           module: function (id, object)
           {
              this.modules[id] = $.extend({}, System.MODULE_ABSTRACT, object);
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
           hashChanged: function ()
           {
              console.log(this.navigation);
              console.log(this.params);
              var e = $.Event("hashchange");
              //console.log("---->"+this.navigation.app[0]);
              if (this.navigation.app)
                 this.activeModule = this.modules[this.navigation.app[0]];
              if (this.activeModule)
              {
                 this.activeModule.navigation = this.navigation;
                 this.activeModule.params = this.params;
                 this.activeModule.hashChanged();
              }
              //this.activeModule.hashHandler(this.navigation, this.params);
              this.hashHandler.call(e, this.navigation, this.params);
              //if (this.activeModule)
              //this.activeModule.hashHandler(this.navigation, this.params);
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
                 if (self.oldHash !== window.location.hash || self.newHandler)
                 {
                    self.oldHash = window.location.hash;
                    self.newHandler = false;
                    var hashValue = window.location.hash;
                    if (hashValue.indexOf("#") !== -1)
                    {
                       hashValue = hashValue.substring(1);
                       /*var nav = hashValue.match(/#([^&]*)&?/);
                        hashValue = hashValue.replace(/#([^&]*)&?/, "");
                        
                        if (nav[0])
                        navigation = nav[1].split("/").filter(Boolean);*/
                       //hashValue = hashValue.substring(1);
                    }
                    self.navigation = {};
                    self.params = {};
                    hashValue.replace(/([^&]*)=([^&]*)/g, function (m, k, v)
                    {
                       self.navigation[k] = v.split("/").filter(Boolean);
                       self.params[k] = v;
                    });
                    self.hashChanged(); // System
                    //self.oldHash = window.location.hash;
                 }
              };
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
              var app = parameters.app || this.params.app;
              //alert(app)              
              if (app)
              {
                 if (!this.navHashes[app])
                    this.navHashes[app] = "app=" + app;
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
           }

        };