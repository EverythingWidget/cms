<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function script() {
  ob_start();
  ?>
  <script>
    (function () {
      System.state('settings', function (state) {
        state.type = 'app';
        state.onInit = function (nav) {
          var stateHandler = this;
          stateHandler.data.sections = <?= EWCore::read_registry_as_json('ew/ui/apps/settings/navs') ?>;
        };

        state.onStart = function () {
        };

        state.on('app', function (p, section) {
          if (!section) {
            System.UI.components.sectionsMenuList[0].value = '0';
            return;
          }

          this.data.tab = section;
          System.services.app_service.load_section(section);
        });

        return this;
      });
    })();

  </script>
  <?php
  return ob_get_clean();
}

echo admin\AppsManagement::create_section_main_form(["script" => script()]);

