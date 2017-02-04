<?php
session_start();
?>
<script>
  (function () {

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

      this.module.onInit = function () {
        component.init();
      };

      this.module.onStart = function () {
        component.start();
      };
    }

    UsersManagementComponent.prototype.init = function () {
      var component = this;
      this.module.data.sections = <?= EWCore::read_registry_as_json('ew/ui/apps/users/navs') ?>;

      this.module.installModules = this.module.data.sections;

      this.module.on('app', System.ui.behave(System.services.app_service.select_app_section, component));
    };

    UsersManagementComponent.prototype.start = function () {
      this.data.tab = null;
    };

    System.state("users-management", function (state) {
      new UsersManagementComponent(state);
    });

  })();
</script>