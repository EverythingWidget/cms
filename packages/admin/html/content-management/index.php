<?php
session_start();

echo admin\AppsManagement::create_section_main_form();
?>
<script>
  (function () {

    function ContentManagementComponent(module) {
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

    ContentManagementComponent.prototype.init = function () {
      var component = this;
      this.module.data.sections = <?= EWCore::read_registry_as_json('ew/ui/apps/contents/navs') ?>;

      this.module.installModules = this.module.data.sections;

      /* This code can be simplified more
       * this.on("app", function (full, section) {
       System.ui.behaviors.selectAppSection(this, full, section);
       });*/

      // Simplified version of above snippet
      this.module.on('app', System.ui.behaviorProxy(component, 'selectAppSection'));
    };

    ContentManagementComponent.prototype.start = function () {
      this.data.tab = null;
    };

    System.state("content-management", function () {
      new ContentManagementComponent(this);
    });
  })();
</script>
