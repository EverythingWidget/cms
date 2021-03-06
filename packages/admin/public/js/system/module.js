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