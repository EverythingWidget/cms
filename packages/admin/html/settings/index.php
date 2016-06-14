<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function sidebar() {
  ob_start();
  ?>
  <ul>
    <li>
      <a rel="ajax" data-default="true" data-ew-nav="general" href="~admin/settings/general.php">General</a>
    </li>     
    <li>
      <a rel="ajax" data-ew-nav="apps-plugins" href="~admin/settings/apps-plugins.php">Apps & Plugins</a>
    </li>     
    <li>
      <a rel="ajax" data-ew-nav="preference" href="~admin/settings/perference.php">Preference</a>
    </li>     
  </ul>
  <?php
  return ob_get_clean();
}

function script() {
  ob_start();
  ?>
  <script  >
    (function () {
      var tt = Scope.import('html/admin/content-management/test-file.php');
      console.log('settings->', tt);

      System.state("settings", function (state) {
        state.type = "app";
        state.onInit = function (nav) {

          this.data.sections = [
            {
              title: "tr{General}",
              id: "settings/general",
              url: "~admin/html/settings/general.php"
            },
            {
              title: "tr{Preference}",
              id: "settings/preference",
              url: "~admin/html/settings/preference.php"
            }
          ];

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

//EWCore::register_form("ew-section-main-form", "sidebar", ["content" => sidebar()]);
//EWCore::register_form("ew-section-main-form", "content", ["content" => content()]);
echo admin\AppsManagement::create_section_main_form(["script" => script()]);

