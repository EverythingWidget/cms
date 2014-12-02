<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

global $EW;
if ($_REQUEST["appDir"])
{
   $ac = json_decode($EW->get_app_config($_REQUEST["appDir"]), true);
   $class = $_REQUEST["appDir"];
   $config = "<label>Nothing to configure</label>";
   $config_file_path = EW_APPS_DIR . "/" . $class . "/core/app_config.php";
   if (!file_exists($config_file_path))
   {
      $config_file_path = EW_APPS_DIR . "/" . $class . "/app_config.php";
   }
   
   if (file_exists($config_file_path))
   {
      ob_start();
      include $config_file_path;
      $config = ob_get_clean();
   }
}
?>
<form name="settings-form" id="settings-form" class="row">
   <div class="col-lg-8 col-md-6 col-xs-12" >
      <div class="box box-white">
         <div class="col-xs-12">
            <h2><span class="header-label">App Settings</span><?php echo $ac["name"] ?></h2>
         </div>
         <!--<input type="hidden" name="appName" value="<?php echo $class ?>"/>-->
         <?php echo $config ?>
      </div>
   </div>
   <div class="col-lg-4 col-md-6 col-xs-12 pull-right" >
      <div class="box box-white">
         <div class="col-xs-12">
            <h2>App Properties</h2>
         </div>

         <div class="col-xs-12 mar-bot">
            <label>App Name</label>
            <label class="value" name="appName" id="appName" ><?php echo $ac["name"] ?></label>
         </div>
         <div class="col-xs-12 mar-bot">
            <label>App Root Directory</label>
            <label class="value" name="appDir" id="appDir"><?php echo $ac["root"] ?></label>
         </div>

         <div class="col-xs-12">
            <label>Version</label>
            <label class="value" id="appVersion"><?php echo $ac["version"] ?></label>
         </div>
      </div>
   </div>
</form>