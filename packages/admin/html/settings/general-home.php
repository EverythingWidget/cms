<system-spirit animations="verticalShift" vertical-shift="card">
  <form id="settings-cards">

  </form>
</system-spirit>

<script type="text/javascript">
  var service = Scope.import('html/admin/settings/service.php');

  Scope.export = SettingsStateHandler;

  System.newStateHandler(Scope, SettingsStateHandler);

  function SettingsStateHandler(state, scope) {
    var handler = this;
    scope = scope || Scope;

    state.onInit = function () {
      loadAppsGeneralSettings(<?= json_encode(EWCore::read_registry('ew/ui/settings/general')) ?>);
    };

    state.onStart = function () {
      this.saveSettings = EW.addActionButton({
        text: '<i class="icon-check"></i>',
        handler: function () {
          saveAppSettings([
            'webroot'
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
    };

    function loadAppsGeneralSettings(apps) {
      var settingsCard = Scope.html.find('#settings-cards');
      settingsCard.empty();

      service.readGeneralSettings().success(success);

      function success(response) {
        for (var app in apps) {
          if (!apps[app].url) {
            continue;
          }

          System.loadModule({url: apps[app].url, scope: Scope}, function (module) {
            settingsCard.append(module.html);
            EW.setFormData(settingsCard, response.data);
          });
        }
      }
    }

    function saveAppSettings(apps) {
      var data = $('#settings-cards').serializeJSON();

      service.saveSettings(data).success(function (response) {
        System.ui.components.body.EW().notify(response).show();
      });
    }
  }
</script>
