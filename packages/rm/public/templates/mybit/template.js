/* global AOS, $php */

(function () {
  window.addEventListener('load', Load);

  function Load() {
    var templateSettings = $php.$template_settings;

    AOS.init(templateSettings['aos-settings'] || {});
  }
})();
