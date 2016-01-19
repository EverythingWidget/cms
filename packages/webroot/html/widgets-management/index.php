<?php
session_start();

function sidebar()
{
   ob_start();
   ?>
   <ul>        
      <li>      
         <a rel="ajax" data-default="true" data-ew-nav="uis-list" href="<?php echo EW_ROOT_URL; ?>~webroot/widgets-management/uis-list.php">      
            tr{Layouts}   
         </a>     
      </li>   
      <li>       
         <a rel="ajax" data-ew-nav="pages-uis" href="<?php echo EW_ROOT_URL; ?>~webroot/widgets-management/pages-uis.php">        
            tr{Layouts and Contents}        
         </a>        
      </li>    
      <li>         
         <a rel="ajax" data-ew-nav="widgets" href="<?php echo EW_ROOT_URL; ?>~webroot/widgets-management/widgets.php">         
            tr{Widgets Types}   
         </a>      
      </li>   
   </ul> 
   <?php
   return ob_get_clean();
}

function script()
{
   ob_start();
   ?>
   <script>
      (function (System) {

         function WidgetsManagementComponent(module) {
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


         WidgetsManagementComponent.prototype.init = function () {

            this.module.data.sections = [
               {
                  title: "tr{Layouts}",
                  id: "widgets-management/uis-list",
                  url: "~webroot/html/widgets-management/uis-list.php"
               },
               {
                  title: "tr{Layouts and Contents}",
                  id: "widgets-management/pages-uis",
                  url: "~webroot/html/widgets-management/pages-uis.php"
               }
            ];

            this.module.data.installModules = this.module.data.sections;

            this.module.on("app", function (p, section) {

               if (!section /*|| section === this.data.tab*/) {
                  System.UI.components.sectionsMenuList[0].value = '0';
                  return;
               }

               this.data.tab = section;
               EW.loadSection(section);
            });
         };

         WidgetsManagementComponent.prototype.start = function () {
            this.data.tab = null;


   //            System.UI.components.sectionsMenuList[0].setAttribute("data", this.module.data.sections);
         };

         System.module("widgets-management", function () {
            new WidgetsManagementComponent(this);
         });

      })(System);
   </script>
   <?php
   return ob_get_clean();
}

//EWCore::register_form("ew-section-main-form", "sidebar", ["content" => sidebar()]);
//EWCore::register_form("ew-section-main-form", "content", ["content" => content()]);
echo admin\AppsManagement::create_section_main_form(["script" => script()]);
