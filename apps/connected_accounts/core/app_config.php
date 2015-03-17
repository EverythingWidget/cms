<?php ?>
<div class="col-xs-12">
  <input data-label="App ID" class="text-field" name="facebook-app-id" id="facebook-app-id"/>
</div>
<div class="col-xs-12 mar-bot">
  <input data-label="App Secret" class="text-field" name="facebook-app-secret" id="facebook-app-secret"/>
</div>

<script  type="text/javascript">
  var facebook = (function() {
    function facebook()
    {
      this.bSave = EW.addAction("Save Changes", this.saveConfig).addClass("btn-success").hide().comeIn(300);
    }

    facebook.prototype.readConfig = function()
    {
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/Settings/read_settings', function(data)
      {
        EW.setFormData("#settings-form", data);
      }, "json");
    };

    facebook.prototype.saveConfig = function()
    {

      d = $("#settings-form").serializeJSON();
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/Settings/save_settings', {
        params: d
      },
      function(data)
      {
        $("body").EW().notify(data).show();
      }, "json");
    };

    return new facebook();

  })();

  facebook.readConfig();
</script>
