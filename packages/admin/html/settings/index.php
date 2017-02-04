<?php
session_start();
?>
<script>

  System.state('settings', function (state) {
    var handler = this;
    handler.data = {};
    handler.state = state;

    handler.state.type = 'app';
    handler.state.onInit = function () {
      handler.state.data.sections = <?= EWCore::read_registry_as_json('ew/ui/apps/settings/navs') ?>;
    };

    handler.state.onStart = function () {
      handler.state.data.tab = null;
    };

    handler.state.on('app', System.utility.withHost(handler.state).behave(System.services.app_service.select_app_section));
  });

</script>

