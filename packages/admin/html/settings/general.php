<system-spirit animations="verticalShift" vertical-shift="card">
  <form id="settings-cards">

  </form>
</system-spirit>

<script  type="text/javascript">

  function SettingsStateHandler(state, scope) {
    var handler = this;
    scope = scope || Scope;

    state.onStart = function () {
      this.saveSettings = EW.addActionButton({
        text: '<i class="icon-check"></i>',
        handler: function () {
          saveAppSetings([
            "webroot"
          ]);
        },
        class: 'btn-float btn-success',
        parent: System.ui.components.appMainActions
      });

      this.refresh = EW.addActionButton({
        text: '<i class="icon-cw-1"></i>',
        handler: function () {
          loadAppsGeneralSettings(<?= json_encode(EWCore::read_registry('ew/ui/settings/general')) ?>);
        },
        class: 'btn-float priority-1 btn-primary',
        parent: System.ui.components.appMainActions
      });

      if (!handler.appsLoaded) {
        loadAppsGeneralSettings(<?= json_encode(EWCore::read_registry('ew/ui/settings/general')) ?>);
      }

      handler.appsLoaded = true;
    };

    function loadAppsGeneralSettings(apps) {
      var settingsCard = $('#settings-cards');
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
    }

    function saveAppSetings(apps) {
      var data = $('#settings-cards').serializeJSON();

      $.post('api/admin/settings/save-settings', {
        params: data
      }, function (response) {
        System.ui.components.body.EW().notify(response).show();
      });
    }
  }

  System.newStateHandler(Scope, SettingsStateHandler);
</script>
