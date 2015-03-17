<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function sidebar()
{
   ob_start();
   ?>
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
   <?php
   return ob_get_clean();
}

function script()
{
   ob_start();
   ?>
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
       $("body").EW().notify(data).show();
       }, "json");
       };
          
       var settings = new Settings();*/
      //settings.readConfig();
      $(document).ready(function () {
         if (!EW.getHashParameter("nav"))
            EW.setHashParameter("nav", "general");
      });
   </script>
   <?php
   return ob_get_clean();
}

EWCore::register_form("ew-app-main-form", "sidebar", ["content" => sidebar()]);
//EWCore::register_form("ew-app-main-form", "content", ["content" => content()]);
echo admin\AppsManagement::create_app_main_form(["script" => script()]);

