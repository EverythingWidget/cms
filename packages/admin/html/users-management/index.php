<?php
session_start();

echo admin\AppsManagement::create_section_main_form();
?>
<script>
  (function () {
var tt = Scope.import('html/admin/content-management/test-file.php');
    console.log('users-management->', tt);
    /**
     * Users management component
     * 
     * @param {System.MODULE_ABSTRACT} module a instance of system module
     */
    function UsersManagementComponent(module) {
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

    UsersManagementComponent.prototype.init = function () {
      var component = this;
      this.module.data.sections = <?= EWCore::read_registry_as_json('ew/ui/apps/users/navs') ?>;

      this.module.installModules = this.module.data.sections;

      this.module.on('app', System.ui.behave(System.ui.behaviors.selectAppSection, component));
    };

    UsersManagementComponent.prototype.start = function () {
      this.data.tab = null;
    };

    System.state("users-management", function (state) {
      new UsersManagementComponent(state);
    });

  })();
</script>