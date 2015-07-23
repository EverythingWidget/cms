<?php
session_start();

function sidebar()
{
   ob_start();
   ?>
   <ul>  
      <li>   
         <a rel="ajax" data-default="true" data-ew-nav="users" href="<?php echo EW_ROOT_URL; ?>app-admin/UsersManagement/users.php">tr{Users}</a> 
      </li>     
      <li>      
         <a rel="ajax" data-ew-nav="users_groups" href="<?php echo EW_ROOT_URL; ?>app-admin/UsersManagement/users-groups.php">tr{Users Groups}</a>       
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
      (function () {
         var app =
                 {
                    listUsers: function (query, pageNumber)
                    {
                       $.post("UsersManagement/UsersList.php", {cmd: "UsersList", query: query, pageNumber: pageNumber}, function (data)
                       {
                          $("#main-content").html(data);
                       });
                    }
                 };
         /*moduleAdmin.controller('Sidebar', function ($scope)
          {
          
          });
          moduleAdmin.controller('MainContent', function ($scope)
          {
          
          });*/
         /*function UsersManagement()
          {
          }
          UsersManagement.prototype.listUsers = function (query, pageNumber)
          {
          $.post("UsersManagement/UsersList.php", {cmd: "UsersList", query: query, pageNumber: pageNumber}, function (data)
          {
          $("#main-content").html(data);
          });
          //loadPage('UsersManagement/UsersList.php', 'UsersList', 'cmd=UsersList');          
          };*/
         System.registerApp("UsersManagement", app);
      }());
      /* var userManagement;
       ////userManagement.listUsers();   
       $(document).ready(function ()
       {
       userManagement = new UserManagement();
       });*/
   </script>
   <?php
   return ob_get_clean();
}

EWCore::register_form("ew-section-main-form", "sidebar", ["content" => sidebar()]);
//EWCore::register_form("ew-section-main-form", "content", ["content" => content()]);
echo admin\AppsManagement::create_section_main_form(["script" => script()]);
