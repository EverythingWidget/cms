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
         <a rel="ajax" data-default="true" data-ew-nav="general" href="~admin/settings/general.php">General</a>
      </li>     
      <li>
         <a rel="ajax" data-ew-nav="apps-plugins" href="~admin/settings/apps-plugins.php">Apps & Plugins</a>
      </li>     
      <li>
         <a rel="ajax" data-ew-nav="preference" href="~admin/settings/perference.php">Preference</a>
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
      (function (System) {


         System.module("settings", function () {
            this.type = "app";
            this.onInit = function (nav) {
               this.data.sections = <?= EWCore::call("admin/api/apps-management/get-apps")?>

            };

            this.onStart = function () {
            };

            this.on("app", function (p, section) {
               if (!section || section === this.data.tab) {
                  return;
               }
               this.data.tab = section;

               //EW.appNav.setCurrentTab($("a[data-ew-nav='" + section + "']"));
            });

            return this;
         });
      }(System));
   </script>
   <?php
   return ob_get_clean();
}

//EWCore::register_form("ew-section-main-form", "sidebar", ["content" => sidebar()]);
//EWCore::register_form("ew-section-main-form", "content", ["content" => content()]);
echo admin\AppsManagement::create_section_main_form(["script" => script()]);

