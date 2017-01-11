<?php
session_start();
$message = '';
if ($_POST['username'] && !$_SESSION['login']) {
  if (admin\UsersManagement::login($_POST["username"], $_POST["password"])) {
    // User has been logged in succefully
  }
  else {
    if ($_POST['username'] && $_POST['password']) {
      $message = "tr{Wrong user name or password}";
    }
    unset($_POST['username']);
  }
}

if (isset($_SESSION['login'])) {
  //echo $_SESSION['login'];
  header('Location: ' . EW_DIR_URL . '/html/admin/');
  return;
}

$tabs = \EWCore::read_registry("ew-login-form");
?>
<!DOCTYPE html>
<html style="height:100%;">
  <head>
    <title>
      EW Admin Login
    </title>
    <?php include 'header.php'; ?>
    <style>
      .footer-pane {
        position: absolute;
        bottom: 0;
        width: 100%;
        min-height: 52px;
        padding: 0;
      }

      .footer-pane img {
        border: none;
      }
    </style>
  </head>
  <body id="base-pane" class="container">
    <div class="card card-medium z-index-1" >
      <form action="<?= $_SERVER['HTTP_REFERER'] ?>" method="POST">
        <div class="card-header">
          <h1>
            EW CPanel
          </h1>          
        </div>

        <div class="card-content">
          <h3>
            version 0.9.2
          </h3>
          <system-field class="field">
            <label >
              Username
            </label>
            <input class="text-field"  name="username" >
          </system-field>

          <system-field class="field">
            <label>
              Password
            </label>
            <input class="text-field"  type="password" name="password" >
          </system-field>

          <div class="form-block">
            <p style="text-align: center; color: #990000; font-weight: 500;"><?= $message ?></p>
          </div>    

          <div class="card-control-bar">
            <button type="submit" name="sign-in" class="btn btn-success" style="float:right;" >Sign In</button>
          </div>
        </div>

        <div class="row">
          <div class="col-xs-12" style="">
            <div class="row">
              <?php
// Load registered login forms 
              if (isset($tabs)) {
                foreach ($tabs as $id => $tab) {
                  echo "<div class='tab-pane' id='$id'>{$tab["content"]}</div>";
                }
              }
              ?>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="footer-pane">
      <img src="public/admin/css/images/EW_LOGO.png" alt="EW Logo" style="margin: 0px auto;display:block;">
      <div class="row">
        <h1 style="text-align:center;">
          Everything Widget CMS
        </h1>
      </div>
    </div>

  </body>
</html>
