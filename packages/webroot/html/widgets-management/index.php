<?php
session_start();

function script() {
  ob_start();
  ?>
  <script>
    (function () {

      function WidgetsManagementComponent(module) {
        var component = this;
        this.module = module;
        this.module.type = "app";
        this.data = {};

        this.module.bind('init', function () {
          component.init();
        });

        this.module.bind('start', function () {
          component.start();
        });
      }


      WidgetsManagementComponent.prototype.init = function () {
        this.module.data.sections = <?= EWCore::read_registry_as_json('ew/ui/apps/widgets/navs') ?>;        

        this.module.data.installModules = this.module.data.sections;

        this.module.on('app', System.ui.behaviorProxy(this, 'selectAppSection'));
      };

      WidgetsManagementComponent.prototype.start = function () {
        this.data.tab = null;
      };

      System.state("widgets-management", function (state) {
        new WidgetsManagementComponent(state);
      });

    })();
  </script>
  <?php
  return ob_get_clean();
}

//EWCore::register_form("ew-section-main-form", "sidebar", ["content" => sidebar()]);
//EWCore::register_form("ew-section-main-form", "content", ["content" => content()]);
echo admin\AppsManagement::create_section_main_form(["script" => script()]);
