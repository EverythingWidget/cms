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

         /**
          * Users management component
          * 
          * @param {System.MODULE_ABSTRACT} module a instance of system module
          */
         function UsersManagementComponent(module) {
            var component = this;
            this.module = module;
            this.module.type = "app";
            this.data = {};

            this.module.onInit = function () {
               component.init();
            };

            this.module.onStart = function () {
               component.start();
            };
         }

         UsersManagementComponent.prototype.init = function () {
            var component = this;
            
            this.module.on("app", function (path, section) {
               if (!section /*|| section === component.data.tab*/) {
                  System.UI.components.sectionsMenuList[0].value = '0';
                  return;
               }
               
               component.data.tab = section;
               EW.loadSection(section);
            });
         };

         UsersManagementComponent.prototype.start = function () {
            this.data.tab = null;
            this.module.data.sections = [
               {
                  title: "tr{Users}",
                  id: "users-management/users",
                  url: "~admin/html/users-management/users.php"
               },
               {
                  title: "tr{Users Groups}",
                  id: "users-management/users-groups",
                  url: "~admin/html/users-management/users-groups.php"
               }
            ];

            System.UI.components.sectionsMenuList[0].setAttribute("data", this.module.data.sections);
         };

         System.module("users-management", function () {
            new UsersManagementComponent(this);
         });
         
      }(System));
   </script>
   <?php
   return ob_get_clean();
}

//\EWCore::register_form("ew-section-main-form", "sidebar", ["content" => sidebar()]);
//EWCore::register_form("ew-section-main-form", "content", ["content" => content()]);
echo admin\AppsManagement::create_section_main_form(["script" => script()]);
