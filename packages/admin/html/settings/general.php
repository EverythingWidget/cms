<form id="settings-cards">

</form>

<script  type="text/javascript">

  (function () {
    function SettingsStateHandler(module) {
      var component = this;
      this.module = module;
      this.module.type = "app-section";
      this.module.component = this;

      this.module.onInit = function () {
        component.init();
      };

      this.module.onStart = function () {
        component.start();
      };
    }

    SettingsStateHandler.prototype.init = function () {

    };

    SettingsStateHandler.prototype.start = function () {
      var component = this;
      this.selectMediaAction = EW.addActionButton({
        text: "",
        handler: function () {
          component.saveAppSetings([
            "webroot"
          ]);
        },
        class: "btn-float btn-success icon-ok",
        parent: System.UI.components.appMainActions
      });

      this.loadAppsGeneralSettings(<?= json_encode(EWCore::read_registry("ew/ui/settings/general")) ?>);
    };

    SettingsStateHandler.prototype.loadAppsGeneralSettings = function (apps) {
      var settingsCard = $("#settings-cards");
      settingsCard.empty();

      for (var app in apps) {
        //console.log(apps[app]);
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

      $.post("~admin/api/settings/save-settings", {
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
