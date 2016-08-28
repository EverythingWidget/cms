<?php
session_start();

echo admin\AppsManagement::create_section_main_form();
?>
<script>
  System.entity('services/media_chooser', {
    selectItem: function (item) {
      if (item.type === 'image')
        return item;

      if (item.type === 'audio')
        return {
          type: 'text',
          text: item.path
        };

      return null;
    }
  });

  function ContentManagementStateHandler(state) {
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

  ContentManagementStateHandler.prototype.init = function () {
    var component = this;
    this.state.data.sections = <?= EWCore::read_registry_as_json('ew/ui/apps/contents/navs') ?>;

    // Pre install sub state handlers
    //this.state.installModules = this.state.data.sections;

    /* This code can be simplified more
     * this.on("app", function (full, section) {
     System.ui.behaviors.selectAppSection(this, full, section);
     });*/

    // Simplified version of above snippet
    this.state.on('app', System.ui.behave(System.services.app_service.select_app_section, component));
  };

  ContentManagementStateHandler.prototype.start = function () {
    this.data.tab = null;
  };

  System.state("content-management", function (state) {
    new ContentManagementStateHandler(state);
  });

</script>
