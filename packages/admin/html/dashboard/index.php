<?php
session_start();

if (!isset($_SESSION['login']))
{
   include "Login.php";
   return;
}
?> 
<!DOCTYPE html>
<html>
   <head>
      <title>
         EW Admin
      </title>
      <?php include 'header.php'; ?>
   </head>
   <body class="Admin <?php echo EWCore::get_language_dir($_REQUEST["_language"]); ?>" >

      <div id="components-pane" class="col-xs-12" >
         <ul class="component row">           
         </ul>
      </div>

      <div id="base-pane" class="container">      
         <div id="app-content" >
            <!--<div id="nav-bar" class="nav-bar">

            </div>-->
            <a id="apps-menu" class="apps-menu" data-ew-nav="" href="./~admin/#app=Home">
               <span type="button" id="apps" class="apps-menu-icon" ></span>
               <span id="app-title" class="apps-menu-title">tr{Apps}</span>
            </a>
            <div id="app-bar" class="app-bar">

               <div  class="col-xs-2 col-sm-2 col-md-2 col-lg-1 pull-right">
                  <?php
                  if ($_SESSION['login'])
                  {
                     echo '<a class="ExitBtn" href="~admin/api/users-management/logout?url=' . EW_DIR_URL . '~admin/" ></a>';
                  }
                  ?>
               </div>  
               <button class="btn btn-current-tab" id="side-bar-btn" >  
               </button>   
               <div class="action-pane" >
                  <div id="action-bar-items" class="actions-bar action-bar-items" style="display:block;float:none;">
                  </div>
               </div>
            </div>
            <div id="home-pane" class="home-pane" >
            </div>
            <?php
//echo ($compPage);
            ?>
         </div>
      </div>

      <div id="notifications-panel"></div>   

      <?php include 'footer.php'; ?>      
   </body>
</html>
