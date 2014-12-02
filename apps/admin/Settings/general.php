<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$app_config = EWCore::get_app_config("admin");
?>
<form name="general-form" id="general-form" class="row">
   <div class="col-lg-4 col-md-6 margin-bottom" >
      <div class="box box-white">
         <div class="col-xs-12">
            <h2>Administration Configuration</h2>
         </div>
         <div class="row mar-bot">
            <div class="col-xs-12">
               <select  id="language" name="language" value=""  >
                  <option value="0">---</option>
                  <?php
                  /* $categories = new Categories();
                    $cl = json_decode($categories->get_categories_list(), true);
                    $cl = $cl["result"];
                    //print_r($cl);
                    foreach ($cl as $category)
                    {
                    echo "<option value='{$category["id"]}' >{$category["name"]}</option>";
                    } */
                  ?>
               </select>

            </div>
         </div>
      </div>
   </div>
   <div class="row-separator hidden-lg" >
   </div>
   <div class="col-lg-4 col-md-6 margin-bottom">
      <div class="box box-white">
         <div class="col-xs-12">
            <h2>Web Site Properties</h2>
         </div>     
         <div class="row mar-bot">
            <div class="col-xs-12" >
               <input class="text-field" data-label="UIS Name" name="name" id="name">
            </div>
         </div>
      </div>

   </div>
</form>
<script  type="text/javascript">
   function General()
   {
      this.bSave = EW.addAction("tr{Save Changes}", this.saveConfig).addClass("btn-success");
      EW.setFormData("#general-form", <?php echo ($app_config) ? $app_config : "{}"; ?>);
   }
   General.prototype.saveConfig = function ()
   {

      /*d = $("#settings-form").serializeJSON();
       $.post('<?php echo EW_ROOT_URL; ?>app-admin/Settings/save_settings', {
       params: d
       },
       function(data)
       {
       $("body").EW().notify(data);
       }, "json");*/
   };
   var general = new General();
</script>