<?php
session_start();

if ($_POST['username'] && !$_SESSION['login'])
{
   if (admin\UsersManagement::login())
   {
      //return;
      // User has been logged in succefully
   }
   else
   {
      unset($_POST['username']);
   }
}

if (isset($_SESSION['login']))
{
   //echo $_SESSION['login'];
   header('Location: index.php');
   return;
}
/* $MYSQLI = get_db_connection();
  $username = $MYSQLI->real_escape_string($_POST["UserName"]);
  $password = $MYSQLI->real_escape_string($_POST["Password"]); */
//if(!isset($_POST['UserName']) )



$tabs = EWCore::read_registry("ew-login-form");
?>
<!DOCTYPE html>
<html style="height:100%;">
   <head>
      <title>
         EW CPanel
      </title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="shortcut icon" href="styles/favicon.ico">  
      <link href="<?php echo EW_ROOT_URL ?>templates/default/template.css" rel="stylesheet" type="text/css">
      <link type="text/css" href="<?php echo EW_ROOT_URL ?>core/css/custom-theme/jquery-ui-1.8.21.custom.css" rel="Stylesheet" />	
      <link type="text/css" href="<?php echo EW_ROOT_URL ?>core/css/bootstrap.css" rel="stylesheet" type="text/css">       
      <script src="<?php echo EW_ROOT_URL ?>core/js/jquery/jquery-1.10.2.min.js"  type="text/javascript">
      </script>        
      <script type="text/javascript" src="<?php echo EW_ROOT_URL ?>core/js/jquery/jquery-ui-1.10.3.custom.min.js"></script>
      <script src="<?php echo EW_ROOT_URL ?>core/js/ewscript.js"  type="text/javascript">
      </script>
      <script src="<?php echo EW_ROOT_URL ?>core/js/bootstrap.js"  type="text/javascript">
      </script>
   </head>
   <body style="overflow-y:auto;height:100%;min-height:550px;position:relative;">
      <div class="row" style="padding-top:20px;min-height:500px;overflow: hidden;">
         <form style="background-color:#fff;border:1px solid #aaa;overflow: hidden;box-shadow: 0px 2px 5px #ccc;z-index:1;padding-left:30px;padding-right:30px;" class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4"  action="<?php echo $_SERVER['HTTP_REFERER'] ?>" method="POST">
            <div class="row">
               <div class="col-xs-12 margin-bottom">
                  <h1 style="text-align:center;margin-bottom:0px;">
                     EW CPanel
                  </h1>
                  <label style="text-align:center;">
                     version 0.9
                  </label>
               </div>
               <div class="col-xs-12 margin-bottom">
                  <label >
                     User Name:
                  </label>
                  <input class="text-field"  name="username" >
               </div>
               <div class="col-xs-12 margin-bottom">
                  <label>
                     Password:
                  </label>
                  <input class="text-field"  type="password" name="password" >
               </div>
            </div>
            <div class="row mar-bot">
               <div class="col-xs-12" >
                  <button type="submit" name="sign-in" class="btn btn-success" style="float:right;" >Sign In</button>
               </div>
            </div>
            <div class="row">
               <div class="col-xs-12" style="border-top:1px solid #ddd;">
                  <div class="row">
                     <?php
                     // Load registered login forms 
                     foreach ($tabs as $id => $tab)
                     {
                        echo "<div class='tab-pane' id='{$id}'>{$tab["content"]}</div>";
                     }
                     ?>

                  </div>
               </div>
            </div>
         </form>
      </div>
      <div class="footer-pane">
         <img src="<?php echo EW_ROOT_URL ?>templates/default/EW_LOGO.png" alt="EW Logo" style="margin: 0px auto;display:block;">
         <div class="row">
            <h1 style="text-align:center;">
               Everything Widget CMS
            </h1>
         </div>
      </div>

   </body>
</html>
