<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$app_config = EWCore::read_settings("ew");
?>
<form name="general-form" id="general-form" class="row">
   <div class="col-lg-6 col-md-6 col-lg-offset-3 col-md-offset-3 margin-bottom" >
      <div class="box box-white z-index-1">
         <div class="col-xs-12">
            <h2>General Configuration</h2>
         </div>
         <div class="row mar-bot">
            <div class="col-xs-12">
               <div class="col-xs-12">
                  <select id="language" name="language" data-label="tr{Add a language}">
                     <option value="en">English</option>
                     <option value="es">Spanish</option>
                     <option value="de">German</option>
                     <option value="ru">Russian</option>
                     <option value="cmn">Mandarin</option>
                     <option value="ar">Arabic</option>
                     <option value="fa">فارسی</option>
                  </select>
               </div>
            </div>
         </div>
      </div>
   </div>
</form>
<script  type="text/javascript">
   function General()
   {
      this.bSave = EW.addAction("tr{Save Changes}", this.saveConfig).addClass("btn-success");
      EW.setFormData("#general-form", <?php echo $app_config; ?>);
   }
   General.prototype.saveConfig = function ()
   {

      d = $("#general-form").serializeJSON();
      $.post('<?php echo EW_ROOT_URL; ?>admin/api/EWCore/save_settings', {
         params: d
      },
      function (data)
      {
         $("body").EW().notify(data).show();
      }, "json");
   };
   var general = new General();
</script>
