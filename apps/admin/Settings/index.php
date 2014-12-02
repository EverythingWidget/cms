<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="sidebar" class="side-bar">
   <div class="row">

      <ul>
         <li>
            <a rel="ajax" data-ew-nav="general" href="<?php echo EW_ROOT_URL; ?>app-admin/Settings/general.php">General</a>
         </li>     

         <li>
            <a rel="ajax" data-ew-nav="apps-plugins" href="<?php echo EW_ROOT_URL; ?>app-admin/Settings/apps-plugins.php">Apps & Plugins</a>
         </li>     

         <li>
            <a rel="ajax" data-ew-nav="preference" href="<?php echo EW_ROOT_URL; ?>app-admin/Settings/perference.php">Preference</a>
         </li>     
      </ul>
   </div>
</div>
<div id="main-content" class="col-xs-12" role="main">

</div>
<script  type="text/javascript">


   /*Settings.prototype.readConfig = function ()
   {
      /*$.post('<?php echo EW_ROOT_URL; ?>app-admin/Settings/read_settings', function(data)
       {
       EW.setFormData("#settings-form", data);
       }, "json");
   };

   Settings.prototype.saveConfig = function ()
   {

      /*d = $("#settings-form").serializeJSON();
       $.post('<?php echo EW_ROOT_URL; ?>app-admin/Settings/save_settings', {
       params: d
       },
       function(data)
       {
       $("body").EW().notify(data);
       }, "json");
   };

   var settings = new Settings();*/
   //settings.readConfig();
   if (!EW.getHashParameter("nav"))
      EW.setHashParameter("nav", "general");
</script>