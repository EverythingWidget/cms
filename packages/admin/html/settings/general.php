<system-spirit animations="verticalShift" vertical-shift="card">
  <form id="settings-cards">

  </form>
</system-spirit>

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
        text: '<i class="icon-check"></i>',
        handler: function () {
          handler.saveAppSetings([
            "webroot"
          ]);
        },
        class: "btn-float btn-success",
        parent: System.UI.components.appMainActions
      });

      this.refresh = EW.addActionButton({
        text: '<i class="icon-cw-1"></i>',
        handler: function () {
          handler.loadAppsGeneralSettings(<?= json_encode(EWCore::read_registry("ew/ui/settings/general")) ?>);
        },
        class: "btn-float priority-1 btn-primary",
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
      $.get('api/admin/settings/read-settings', function (response) {
        success(response.data);
      });

      function success(data) {
        for (var app in apps) {
          if (!apps[app].url) {
            continue;
          }

          $.get(apps[app].url, function (response) {
            settingsCard.append(response);
            EW.setFormData('#settings-cards', data);
          });
        }
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
