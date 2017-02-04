<?php
session_start();
?>

<script>
  (function () {

    function WidgetsManagementStateHandler(state) {
      var component = this;
      this.state = state;
      this.state.type = "app";
      this.data = {};

      this.state.onInit = function () {
        component.init();
      };

      this.state.onStart = function () {
        component.start();
      };
    }


    WidgetsManagementStateHandler.prototype.init = function () {
      this.state.data.sections = <?= EWCore::read_registry_as_json('ew/ui/apps/widgets/navs') ?>;

      this.state.data.installModules = this.state.data.sections;

      this.state.on('app', System.utility.withHost(this).behave(System.services.app_service.select_app_section));
    };

    WidgetsManagementStateHandler.prototype.start = function () {
      this.data.tab = null;
    };

    System.state("widgets-management", function (state) {
      new WidgetsManagementStateHandler(state);
    });

  })();
</script>
