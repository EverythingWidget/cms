<?php ?>
<div class="row">
   <div class="col-xs-12">
      <input class="text-field" name="webroot/web-title" id="web-title" data-label="tr{Website Tite}">
   </div>
</div>
<div class="row">
   <div class="col-xs-12">
      <textarea class="text-field" name="webroot/web-description" id="web-description" data-label="tr{Website Description}"></textarea>
   </div>
</div>
<div class="row">
   <div class="col-xs-12">
      <textarea class="text-field" name="webroot/web-keywords" id="web-keywords" data-label="tr{Website Keywords}"></textarea>
      <label class="small">Seperate with comma (,)</label>
   </div>
</div>

<script  type="text/javascript">
   var webroot = (function () {
      function webroot()
      {
         this.bSave = EW.addAction("Save Changes", this.saveConfig).addClass("btn-success").hide().comeIn(300);
      }

      webroot.prototype.readConfig = function ()
      {
         $.post('<?php echo EW_ROOT_URL; ?>app-admin/Settings/read_settings',{app:"webroot"}, function (data)
         {
            EW.setFormData("#settings-form", data);
         }, "json");
      };

      webroot.prototype.saveConfig = function ()
      {

         d = $("#settings-form").serializeJSON();
         $.post('<?php echo EW_ROOT_URL; ?>app-admin/Settings/save_settings', {
            params: d
         },
         function (data)
         {
            $("body").EW().notify(data);
         }, "json");
      };

      return new webroot();

   })();

   webroot.readConfig();
</script>