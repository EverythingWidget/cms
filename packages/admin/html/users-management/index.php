<?php
session_start();

function sidebar()
{
   ob_start();
   ?>
   <ul>  
      <li>   
         <a rel="ajax" data-default="true" data-ew-nav="users" href="<?php echo EW_ROOT_URL; ?>~admin/users-management/users.php">tr{Users}</a> 
      </li>     
      <li>      
         <a rel="ajax" data-ew-nav="users_groups" href="<?php echo EW_ROOT_URL; ?>~admin/users-management/users-groups.php">tr{Users Groups}</a>       
      </li>    
   </ul>   
   <?php
   return ob_get_clean();
}

function script()
{
   ob_start();
   ?>
   <script >
      (function (System) {

         System.module("users-management", function () {
            this.type = "app";
            this.onInit = function () {
            };

            this.onStart = function () {
               this.data.tab = null;
            };

            this.on("app", function (p, section) {
               if (!section || section === this.data.tab)
                  return;
               this.data.tab = section;
               EW.appNav.setCurrentTab($("a[data-ew-nav='" + section + "']"));
            });

            return this;
         });
      }(System));
   </script>
   <?php
   return ob_get_clean();
}

\EWCore::register_form("ew-section-main-form", "sidebar", ["content" => sidebar()]);
//EWCore::register_form("ew-section-main-form", "content", ["content" => content()]);
echo admin\AppsManagement::create_section_main_form(["script" => script()]);
