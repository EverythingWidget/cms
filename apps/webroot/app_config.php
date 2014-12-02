<?php ?>
<div class="col-xs-12">
  <label>Title</label>
  <input class="text-field" name="web-title" id="web-title">
</div>
<div class="col-xs-12">
  <label>Description</label>
  <textarea class="text-field" name="web-description" id="web-description"></textarea>
</div>
<div class="col-xs-12">
  <label>Keywords</label>
  <textarea class="text-field" name="web-keywords" id="web-keywords"></textarea>
  <label class="small">Seperate with comma (,)</label>
</div>

<script  type="text/javascript">
  var webroot = (function() {
    function webroot()
    {
      this.bSave = EW.addAction("Save Changes", this.saveConfig).addClass("btn-success").hide().comeIn(300);
    }

    webroot.prototype.readConfig = function()
    {
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/Settings/read_settings', function(data)
      {
        EW.setFormData("#settings-form", data);
      }, "json");
    };

    webroot.prototype.saveConfig = function()
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

    return new webroot();

  })();

  webroot.readConfig();
</script>