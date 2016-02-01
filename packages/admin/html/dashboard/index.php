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
    <div id="base-pane" class="container">      
      <div id="app-content" >

        <!--<div id="nav-bar" class="nav-bar">

        </div>-->
        <div id="navigation-menu" class="navigation-menu">
          <div id="apps-menu" class="apps-menu" >
             <!--<span type="button" id="apps" class="apps-menu-icon" ></span>-->
            <span id="app-title" class="apps-menu-title">tr{Apps}</span>
          </div>
          <div id="sections-menu" class="sections-menu">
            <!--<span class="sections-menu-title" id="sections-menu-title" >  
            </span>-->
            <ew-list id="sections-menu-list" class="sections-menu-list">
              <div class="sections-menu-item">
                <a class="sections-menu-item-link" href="{{id}}" >{{title}}</a>
              </div>
            </ew-list>
          </div>
        </div>


        <div id="app-bar" class="app-bar">
          <div id="sections-menu-title" class="app-bar-title">
            tr{Documents}
          </div>
          <div class="action-center">
            <?php
            if ($_SESSION['login'])
            {
              echo '<a class="ExitBtn" href="~admin/api/users-management/logout?url=' . EW_DIR_URL . '~admin/" ></a>';
            }
            ?>
          </div>  

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
