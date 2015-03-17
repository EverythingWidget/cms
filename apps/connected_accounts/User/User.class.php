<?php

namespace connected_accounts;

use Section;
use EWCore;
use Settings;
use Facebook;
use UsersManagement;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class User extends Section
{

  public function init_plugin()
  {
    //EWCore::register_form("ew-login-form", "facebook-login", "Facebook", $this->get_login_form());
    //EWCore::register_action("ew-user-action-logout", "facebook.User.logout", "logout", $this);
  }

  public function logout()
  {
    $app_info = json_decode(Settings::read_settings(), TRUE);

    require_once(EW_APPS_DIR . "/facebook/facebook.php");

    $config = array(
        'appId' => $app_info["facebook-app-id"],
        'secret' => $app_info["facebook-app-secret"],
        'fileUpload' => true, // optional
        'allowSignedRequest' => false, // optional, but should be set to false for non-canvas apps
    );

    $facebook = new Facebook($config);
    $user_id = $facebook->getUser();

    if ($user_id)
    {
      try
      {
        $params = array('next' => EW_ROOT_URL . "admin/");
        //header("Location: " . );
      } catch (FacebookApiException $e)
      {
        
      }
    }
    else
    {
      
    }
    return json_encode(array("status" => "success", "redirectUrl" => $facebook->getLogoutUrl($params)));
  }

  public function login()
  {
    //print_r(EWCore::read_registry("ew-login-form"));
    $app_info = json_decode(Settings::read_settings(), TRUE);

    require_once(EW_APPS_DIR . "/facebook/facebook.php");

    $config = array(
        'appId' => $app_info["facebook-app-id"],
        'secret' => $app_info["facebook-app-secret"],
        'fileUpload' => true, // optional
        'allowSignedRequest' => false, // optional, but should be set to false for non-canvas apps
    );
//echo "asf";
    $facebook = new Facebook($config);
    $user_id = $facebook->getUser();

    if ($user_id)
    {
      // We have a user ID, so probably a logged in user.
      // If not, we'll get an exception, which we handle below.
      try
      {

        $user_profile = $facebook->api('/me', 'GET');
        $user_info = json_decode(UsersManagement::add_user_skip($user_profile['email'], $user_profile['first_name'], $user_profile['last_name'], UsersManagement::random_password()), TRUE);
        UsersManagement::login($user_info["email"], $user_info["password"]);
        //print_r($user_info)
        //echo "{$user_profile['email']} {$user_profile['first_name']} {$user_profile['last_name']}";
        //print_r($user_profile);
      } catch (FacebookApiException $e)
      {
        // If the user is logged out, you can have a 
        // user ID even though the access token is invalid.
        // In this case, we'll get an exception, so we'll
        // just ask the user to login again here.
        /* $login_url = $facebook->getLoginUrl();
          echo 'Please <a href="' . $login_url . '">login.</a>';
          error_log($e->getType());
          error_log($e->getMessage()); */
        return json_encode(array("status" => "unsuccess"));
      }
    }
    else
    {

      // No user, print a link for the user to login
      /* $login_url = $facebook->getLoginUrl();
        echo 'Please <a href="' . $login_url . '">login.</a>'; */
      return json_encode(array("status" => "unsuccess"));
    }
    return json_encode(array("status" => "success"));
  }

  private function get_login_form()
  {
    ob_start();
    ?>
    <div id="fb-root"></div>
    <div class="col-xs-12 mar-top mar-bot">
      <button type="button" name="sign-in" id="facebook-sign-in-btn" class="button green" style="margin:0px auto;display:block;float:none;background-color:#005fb3;" >Sign In with <b>Facebook</b></button>
    </div>  
    <script>
      function FacebookLogin()
      {
        $("#facebook-sign-in-btn").on("click", this.loginWithFacebook);
      }

      FacebookLogin.prototype.handleLogin = function(response)
      {
        $("#facebook-sign-in-btn").prop("disabled", true);
        $("#facebook-sign-in-btn").text("Please Wait...");
        // alert(response);
        if (response.authResponse)
        {
          $.post("<?php echo EW_ROOT_URL; ?>app-facebook/User/login", function(data) {
            if (data.status === "success")
              window.location.reload();
            else
            {
              $("#facebook-sign-in-btn").prop("disabled", false);
              $("#facebook-sign-in-btn").html("Sign In with <b>Facebook</b>");
            }
          }, "json");

        }
      };

      FacebookLogin.prototype.loginWithFacebook = function()
      {

        FB.login(this.handleLogin, {
          scope: 'email'
        });
      };

      var lwfb = new FacebookLogin();
      window.fbAsyncInit = function() {
        FB.init({
          appId: '1421962681386900',
          status: true, // check login status
          cookie: true, // enable cookies to allow the server to access the session
          xfbml: true  // parse XFBML
        });

        // Here we subscribe to the auth.authResponseChange JavaScript event. This event is fired
        // for any authentication related change, such as login, logout or session refresh. This means that
        // whenever someone who was previously logged out tries to log in again, the correct case below 
        // will be handled. 
        FB.Event.subscribe('auth.authResponseChange', function(response) {
          // Here we specify what we do with the response anytime this event occurs. 
          if (response.status === 'connected') {
            // The response object is returned with a status field that lets the app know the current
            // login status of the person. In this case, we're handling the situation where they 
            // have logged in to the app.
            lwfb.handleLogin(response);
          } else if (response.status === 'not_authorized') {
            // In this case, the person is logged into Facebook, but not into the app, so we call
            // FB.login() to prompt them to do so. 
            // In real-life usage, you wouldn't want to immediately prompt someone to login 
            // like this, for two reasons:
            // (1) JavaScript created popup windows are blocked by most browsers unless they 
            // result from direct interaction from people using the app (such as a mouse click)
            // (2) it is a bad experience to be continually prompted to login upon page load.
            lwfb.loginWithFacebook();
          } else {
            // In this case, the person is not logged into Facebook, so we call the login() 
            // function to prompt them to do so. Note that at this stage there is no indication
            // of whether they are logged into the app. If they aren't then they'll see the Login
            // dialog right after they log in to Facebook. 
            // The same caveats as above apply to the FB.login() call here.
            lwfb.loginWithFacebook();
          }
        });
      };

      // Load the SDK asynchronously
      (function(d) {
        var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
        if (d.getElementById(id)) {
          return;
        }
        js = d.createElement('script');
        js.id = id;
        js.async = true;
        js.src = "//connect.facebook.net/en_US/all.js";
        ref.parentNode.insertBefore(js, ref);
      }(document));

    </script>    
    <?php
    return ob_get_clean();
  }

  /* public function get_title()
    {
    parent::get_title();
    } */
}
