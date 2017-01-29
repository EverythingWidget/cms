<script>
  var service = {};

  service.readGeneralSettings = function () {
    return $.get('api/admin/settings/read-settings');
  };

  service.saveSettings = function (data) {
    return $.post('api/admin/settings/save-settings', {
      params: data
    });
  };

  Scope.export = service;
</script>