<?php
namespace balloon_up;
use Section;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author Eeliya
 */
class User extends Section
{

   public function get_title()
   {
      return "User";
   }

   public function get_description()
   {
      return "Users management";
   }
   
   public function sign_up($first_name, $last_name, $email, $password, $confirm_password)
   {
      
   }
   
   public function update_profile($tag_line, $about_me, $gender, $birthday, $interests)
   {
      
   }

   //put your code here
}
