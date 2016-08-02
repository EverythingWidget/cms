<form id="settings-cards">

</form>

<script  type="text/javascript">

  (function () {
    function SettingsStateHandler(state) {
      var component = this;
      this.state = state;
      this.state.type = "app-section";
      this.state.component = this;

      this.state.onInit = function () {
        component.init();
      };

      this.state.onStart = function () {
        component.start();
      };
    }

    SettingsStateHandler.prototype.init = function () {

    };

    SettingsStateHandler.prototype.start = function () {
      var handler = this;

      this.saveSettings = EW.addActionButton({
        text: "",
        handler: function () {
          handler.saveAppSetings([
            "webroot"
          ]);
        },
        class: "btn-float btn-success icon-ok",
        parent: System.UI.components.appMainActions
      });

      this.refresh = EW.addActionButton({
        text: 'R',
        handler: function () {
          handler.loadAppsGeneralSettings(<?= json_encode(EWCore::read_registry("ew/ui/settings/general")) ?>);
        },
        class: "btn-float priority-1 btn-primary icon-refresh",
        parent: System.UI.components.appMainActions
      });

      if (!handler.appsLoaded) {
        handler.loadAppsGeneralSettings(<?= json_encode(EWCore::read_registry("ew/ui/settings/general")) ?>);
      }

      handler.appsLoaded = true;
    };

    SettingsStateHandler.prototype.loadAppsGeneralSettings = function (apps) {
      var settingsCard = $("#settings-cards");
      settingsCard.empty();

      for (var app in apps) {
        if (!apps[app].url) {
          continue;
        }

        $.get(apps[app].url, function (response) {
          settingsCard.append(response);

          EW.setFormData("#settings-cards",<?= json_encode(EWCore::call_api("admin/api/settings/read-settings")['data']) ?>);
        });
      }
    };

    SettingsStateHandler.prototype.saveAppSetings = function (apps) {
      var data = $("#settings-cards").serializeJSON();

      $.post("api/admin/settings/save-settings", {
        params: data
      }, function (response) {
        System.UI.components.body.EW().notify(response).show();
      });
    };


    System.state("settings/general", function (state) {
      new SettingsStateHandler(state);
    });
  })();
</script>
