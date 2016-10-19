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

        this.xtag._input = this.querySelectorAll('input, textarea, select')[0];

        var setEmptiness = function () {
          if (element.xtag._input.value || element.xtag._input.type === 'file') {
            element.removeAttribute('empty');
          } else {
            element.setAttribute('empty', '');
          }
        };

        if (this.xtag._input) {
          setEmptiness();

          this.xtag._input.addEventListener('focus', function () {
            element.setAttribute('focus', '');
            setEmptiness();
          });

          this.xtag._input.addEventListener('blur', function () {
            element.removeAttribute('focus');
          });

          this.xtag._input.onchange = function (e) {
            if (this.value) {
              element.removeAttribute('empty');
            } else {
              element.setAttribute('empty', '');
            }
          };

          this.xtag._input.addEventListener('input', function (e) {
            setEmptiness();
          });
        }
      },
      inserted: function () {
        xtag.fireEvent(this.xtag._input,'change');
      },
      removed: function () {
      }
    },
    accessors: {
    },
    events: {
    }
  };

  xtag.register('system-field', Field);
})();