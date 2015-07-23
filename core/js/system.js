
var System = System ||
        {
           apps: {},
           appPathfiledName: null,
           activityTree: [],
           onLoadQueue: [],
           activeHashHandler: function () {
           },
           appAbstract:
                   {
                      init: function ()
                      {

                      },
                      focus: function ()
                      {
                         System.activityTree.unshift(this);
                         System.activeHashHandler = this.hashHandler;
                      },
                      blur: function ()
                      {
                         System.activityTree.splice(0, 1);
                      },
                      dispose: function ()
                      {

                      },
                      hashHandler: function ()
                      {

                      }
                   },
           // Apps Management
           registerApp: function (id, object)
           {
              this.apps[id] = $.extend({}, System.appAbstract, object);
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
                       if (self.appHashes[id])
                          window.location.hash = self.appHashes[id];
                       var html = $(response).filter(":not(script)");
                       //var html = res;
                       System.apps[id] = $.extend({}, System.appAbstract, self.apps[id]);
                       self.onAppLoaded(self.apps[id], html);
                       //
                       System.apps[id].init();
                       System.apps[id].focus();
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
              if (this.onCloseApp(System.apps[appId]))
              {
                 System.apps[appId].blur();
                 var pos = this.activityTree.lastIndexOf(appId);
                 if (pos !== -1)
                    this.activityTree.splice(pos, 1);
                 System.apps[appId].dispose();
                 this.onAppClosed(System.apps[appId]);
              }
           },
           onCloseApp: function (app)
           {

           },
           onAppClosed: function ()
           {
              return true;
           },
           appHashes: [],
           hashChecker: null,
           startHashListener: function ()
           {
              var self = this;
              var detect = function ()
              {
                 if (this.oldHash !== window.location.hash || self.newHandler)
                 {
                    //if (EW.newHandler != true)
                    //{
                    //EW.Router.notifyRoutes();
                    //}
                    //EW.newHandler = false;
                    var hashValue = window.location.hash;
                    if (hashValue.indexOf("#") !== -1)
                    {
                       hashValue = hashValue.substring(1);
                    }
                    //var pairs = hashValue.split("&");
                    //var newHash = "#";
                    //var and = false;
                    var data = {};

                    hashValue.replace(/([^&]*)=([^&]*)/g, function (m, k, v)
                    {
                       data[k] = v;
                    });
                    //for (var i = 0; i < EW.urlHandlers.length; i++)
                    //{
                    self.hashHandler.call({}, data); // System
                    self.activeHashHandler.call({}, data); // App
                    // ---- // Nav

                    //}
                    this.oldHash = window.location.hash;
                 }
              };
              clearInterval(this.hashChecker);
              this.hashChecker = setInterval(function ()
              {
                 detect();
              }, 50);
           },
           setHashParameters: function (parameters, appId, clean)
           {
              this.lastHashParams = parameters;
              var hashValue = window.location.hash;
              if (appId)
              {
                 // create new hash listener if new hash name has been passed
                 if (!this.appHashes[appId])
                 {
                    //alert(hashName);
                    this.appHashes[appId] = new HashListener(appId);
                    hashValue = this.appHashes[appId].hash;
                 }
                 else
                 {
                    this.removeURLHandler("", appId);
                    hashValue = this.appHashes[appId].hash;
                 }
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
              if (appId)
              {
                 this.appHashes[appId].hash = newHash.replace(/\&$/, '');
                 //alert(customHashes[hashName].hash);
              }
              // set url hash if no hash name specified
              else
                 window.location.hash = newHash.replace(/\&$/, '');
           }

        };