/* global xtag */

(function () {
  var Field = {
    lifecycle: {
      created: function () {
        var element = this;
        var input = this.querySelectorAll('input, textarea, select');
        if (input.length > 1) {
          console.warn('Only one input field is allowed inside system-field', this);
        }

        element.xtag._input = this.querySelectorAll('input, textarea, select')[0];

        var setEmptiness = function () {
          if (element.xtag._input.value || element.xtag._input.type === 'file') {
            element.removeAttribute('empty');
          } else {
            element.setAttribute('empty', '');
          }
        };

        if (element.xtag._input) {
          setEmptiness();

          element.xtag._input.addEventListener('focus', function () {
            element.setAttribute('focus', '');
            setEmptiness();
          });

          element.xtag._input.addEventListener('blur', function () {
            element.removeAttribute('focus');
          });

          element.xtag._input.onchange = function (e) {
            setEmptiness();
          };

          element.xtag._input.addEventListener('input', function (e) {
            setEmptiness();
          });

          element.xtag.observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
              if (mutation.attributeName === 'value') {
                setEmptiness();
              }
            });
          });

          element.xtag.observer.observe(this.xtag._input, {attributes: true});
        }
      },
      inserted: function () {
      },
      removed: function () {
        this.xtag.observer.disconnect();
      }
    },
    accessors: {
    },
    events: {
    }
  };

  xtag.register('system-field', Field);
})();