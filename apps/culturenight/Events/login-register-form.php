<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* function login_form_script()
  {
  ob_start();
  ?>
  <script>

  </script>
  <?php
  return ob_get_clean();
  }

  WidgetsManagement::add_html_script(null, login_form_script()); */

admin\WidgetsManagement::set_widget_style_class("login-form");
?>
<div class="row header">
   <div class="col-xs-12 ">
      <h1 id="signInTitle">SIGN IN</h1>
      <h1 id="signUpTitle" style="display:none;">SIGN UP</h1>
   </div>
</div>
<form id="sign-up-in">
   <div class="row" id="firstNameRow" style="display:none;">
      <div class="col-xs-12">
         <input type="text" class="text-field" id="first_name"  name="first_name" data-label="First Name" data-validate="r"/>
      </div>
   </div>
   <div class="row" id="lastNameRow" style="display:none;">
      <div class="col-xs-12">
         <input type="text" class="text-field" id=last_name name="last_name" data-label="Last Name" data-validate="r"/>
      </div>
   </div>
   <div class="row" id="eMailRow">    
      <div class="col-xs-12">
         <input type="text" class="text-field" id="email" name="email" data-label="E-Mail" data-validate="r,email"/>
      </div>
   </div>
   <div class="row" id="passwordRow">
      <div class="col-xs-12">
         <input type="password" class="text-field" id="password" name="password" data-label="Password" data-validate="r,[8]"/>
      </div>
   </div>
   <div class="row" id="confirmPasswordRow" style="display:none;">
      <div class="col-xs-12">
         <input type="password" class="text-field" id="confirmPassword" name="confirmPassword" data-label="Confirm Password" data-validate="r,[8],eq[password]"/>
      </div>
   </div>
   <div class="row footer">
      <div class="col-sm-12 col-xs-12 ">         
         <div id="signinUpResult" class="sign-in-up-result" ></div>
         <button type="button" id="signIn" class="btn btn-sign-in">SIGN IN</button>
         <button type="button" id="signUpForm" class="btn btn-sign-up-form">I Don't Have an Account</button>
         <button type="button" id="signUp" class="btn btn-sign-up" style="display:none;">SIGN UP</button>
         <button type="button" id="signInForm" class="btn btn-sign-up-form" style="display:none;">I Already Have an Account</button>

      </div>
   </div>
</form>
<script>
   $(document).ready(function() {
      var SignInForm = (function() {
         function SignInForm()
         {
            $(".login-form").css({minHeight: $(".login-form").height()});
            $("#signUpForm").click(this.signUpForm);
            $("#signInForm").click(this.signInForm);
            $("#signUp").click($.proxy(this.signUp, this));
            $("#signinUpResult").hide();

         }
         SignInForm.prototype.signUpForm = function()
         {
            $("#signInTitle").hide();
            $("#signUpTitle").stop().fadeIn(600);
            $("#signInForm").html("I Already Have an Account");
            var curHeight = $(".login-form").height();
            $(".login-form .row:not(.header)").hide();
            $(".login-form .row").stop().fadeIn(300);
            $("#signUp").stop().fadeIn(300);
            $("#signInForm").stop().fadeIn(300);
            $("#signIn").hide();
            $("#signUpForm").hide();
            var autoHeight = $(".login-form").css('min-height', '0px').height();
            //alert(autoHeight);
            $(".login-form").css('height', curHeight).animate({minHeight: 388, height: autoHeight}, 300, function() {
               $(".login-form").css('height', "auto");
            });
         };

         SignInForm.prototype.signInForm = function()
         {

            $("#signInTitle").stop().fadeIn(600);
            $("#signUpTitle").hide();
            $("#signinUpResult").hide();
            var curHeight = $(".login-form").height();
            $(".login-form").css("min-height", curHeight);
            //alert(curHeight);
            $(".login-form .row:not(.header)").hide();
            $(".login-form #eMailRow, .login-form #passwordRow, .login-form .row.footer").stop().fadeIn(300);
            $("#signUp").hide();
            $("#signInForm").hide();
            $("#signIn").stop().fadeIn(300);
            $("#signUpForm").stop().fadeIn(300);
            $(".login-form").animate({opacity: 1}, 301, function() {
               var autoHeight = $(".login-form").css({height: 'auto', minHeight: "0px"}).height();
               $(".login-form").css("min-height", curHeight).animate({minHeight: autoHeight}, 300, function() {
               });
            });
         };

         SignInForm.prototype.signIn = function()
         {

         };

         SignInForm.prototype.signUp = function()
         {
            if (!$("#sign-up-in").EW().validate())
            {
               return;
            }
            $("#signUp").hide();
            $("#signInForm").hide();
            $("#signinUpResult").html("Loading...").fadeIn(200);
            var params = $.parseJSON($("#sign-up-in").serializeJSON());
            $.post("<?php echo EW_ROOT_URL ?>app-admin/UsersManagement/sign_up", params, function(data) {


               if (data.status == "duplicate")
               {
                  $("#signInForm").html("Sign In With This Email");
                  $("#signinUpResult").html(data.error_message);
                  $("#signInForm").fadeIn(200);
               }
                if (data.status == "success")
               {
                  $("#signInForm").html("Sign In Now");
                  $("#signinUpResult").html("<h1 style='float:none'>Congratulation</h1>You have been registered succesfully, we will send you an email to verify your account.");
                  $("#signInForm").fadeIn(200);
               }
            }, "json");
         };
         return new SignInForm();
      })();
   });
</script>