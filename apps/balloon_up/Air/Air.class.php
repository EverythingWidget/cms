<?php
namespace balloon_up;
use Section;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Air
 *
 * @author Eeliya
 */
class Air extends Section
{

   public function get_title()
   {
      return "Air";
   }

   public function get_description()
   {
      return "Airs management";
   }

   public function new_air($name, $description, $tags, $visibility)
   {
      
   }

   public function edit_air($air_id, $name, $description, $tags, $visibility)
   {
      
   }
   
   public function delete_air($air_id)
   {
      
   }
   
   public function follow_air($air_id)
   {
      
   }
   
   public function unfollow_air($air_id)
   {
      
   }

   public function get_users_airs($user_id = NULL)
   {
      
   }

   //put your code here
}
