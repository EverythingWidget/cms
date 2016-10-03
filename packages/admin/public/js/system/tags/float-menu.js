/* global System, xtag */

(function () {
  var FloatMenu = {
    lifecycle: {
      created: function () {
        var _this = this;
        this.xtag.indicator = document.createElement("div");
        this.xtag.indicator.className = "ew-float-menu-indicator";
        this.xtag.indicator.style.position = "absolute";
        _this.xtag.actionsContainer = _this.querySelector('[actions]');

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