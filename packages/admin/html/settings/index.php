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
         <a rel="ajax" data-default="true" data-ew-nav="general" href="<?php echo EW_ROOT_URL; ?>admin/Settings/general.php">General</a>
      </li>     
      <li>
         <a rel="ajax" data-ew-nav="apps-plugins" href="<?php echo EW_ROOT_URL; ?>admin/Settings/apps-plugins.php">Apps & Plugins</a>
      </li>     
      <li>
         <a rel="ajax" data-ew-nav="preference" href="<?php echo EW_ROOT_URL; ?>admin/Settings/perference.php">Preference</a>
      </li>     
   </ul>
   <?php
   return ob_get_clean();
}

function script()
{
   ob_start();
   ?>
   <script  >
      (function ()
      {
         System.module("Settings", {
            init: function ()
            {

            }
         });
      }());
   </script>
   <?php
   return ob_get_clean();
}

EWCore::register_form("ew-section-main-form", "sidebar", ["content" => sidebar()]);
//EWCore::register_form("ew-section-main-form", "content", ["content" => content()]);
echo admin\AppsManagement::create_section_main_form(["script" => script()]);

