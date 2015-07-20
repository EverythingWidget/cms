<?php
session_start();

function sidebar()
{
   ob_start();
   ?>
   <ul>        
      <li>      
         <a rel="ajax" data-default="true" data-ew-nav="uis-list" href="<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/uis-list.php">      
            tr{Layouts}   
         </a>     
      </li>   
      <li>       
         <a rel="ajax" data-ew-nav="pages-uis" href="<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/pages-uis.php">        
            tr{Layouts and Contents}        
         </a>        
      </li>    
      <li>         
         <a rel="ajax" data-ew-nav="widgets" href="<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/widgets.php">         
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
      var oldSection;
      function WidgetsManagement()
      {
         this.sections = $('#sections').html();
         this.oldRow;
      }
      WidgetsManagement.prototype.selectRow = function (obj) {
         $(widgetsManagement.oldRow).removeClass("selected");
         $(obj).addClass("selected");
         widgetsManagement.oldRow = obj;
      };
      WidgetsManagement.prototype.uisList = function () {
         EW.lock('#main-content');
         $.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/uis-list.php', function (data) {
            $('#main-content').html(data);
         });
      };
      WidgetsManagement.prototype.panelsList = function () {
         EW.lock('#main-content');
         $.post('sections/WidgetsManagement/PanelsList.php', function (data) {
            $('#main-content').html(data);
         });
      };
      WidgetsManagement.prototype.widgetsTypesList = function () {
         EW.lock('#main-content');
         var t = EW.createTable({name: "widgets-types-list", headers: {Name: {}, Description: {}}, columns: ["name", "description"], rowCount: true, url: "<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/get_widgets_types", pageSize: 30});
         $('#main-content').html(t.container);
      };
      WidgetsManagement.prototype.widgetsList = function () {
         EW.lock('#main-content');
         $.post('sections/WidgetsManagement/WidgetsList.php', function (data) {
            $('#main-content').html(data);
         });
      };
      WidgetsManagement.prototype.pagesUISList = function () {
         EW.lock('#main-content');
         $.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/pages-uis.php', function (data) {
            $('#main-content').html(data);
         });
      };

      WidgetsManagement.prototype.onBackToWM = function () {
      };

      WidgetsManagement.prototype.backToWM = function () {
         EW.setHashParameter('section', null);
      }

      WidgetsManagement.prototype.showWidgetsManagement = function ()
      {
         widgetsManagement.bBack.animate({width: "0px"}, 300);
         $('#component-title').html("Widgets");
         //$('#component-content').css({display: "none"});      
         ////$('#component-content').html(widgetsManagement.sections);      
         ////$('#component-content').fadeIn(500);      
         widgetsManagement.onBackToWM();
         widgetsManagement.onBackToWM = function ()
         {
         };
         widgetsManagement.onCommand = function ()
         {
         };
      };
      WidgetsManagement.prototype.onCommand = function () {
      };
      var widgetsManagement;
      $(document).ready(function () {
         widgetsManagement = new WidgetsManagement();
      });
   </script>
   <?php
   return ob_get_clean();
}

EWCore::register_form("ew-section-main-form", "sidebar", ["content" => sidebar()]);
//EWCore::register_form("ew-section-main-form", "content", ["content" => content()]);
echo admin\AppsManagement::create_section_main_form(["script" => script()]);
