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