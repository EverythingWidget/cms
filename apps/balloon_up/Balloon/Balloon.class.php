<?php
namespace balloon_up;
use Section;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Balloon
 *
 * @author Eeliya
 */
class Balloon extends Section
{

   public function get_title()
   {
      return "Balloons";
   }

   public function get_description()
   {
      return "Manage balloons";
   }
   
   public function init_plugin()
   {
      \EWCore::registare_widget_feeder("page", "balloon-poster-form", EW_ROOT_DIR . "apps/balloon_up/Balloon/balloon-poster-form.php");
   }

   /**
    * Post a new balloon on the given $air_id
    * 
    * @param int $air_id
    * @param string $balloon_type
    * @param type $message
    */
   public function post_balloon($air_id, $balloon_type, $message, $visibility)
   {
      
   }

   public function like_balloon($balloon_id)
   {
      
   }

   public function dislike_balloon($balloon_id)
   {
      
   }

   /**
    *  Get the list of user's air's balloons
    * 
    * @param int $air_id
    */
   public function get_balloons($air_id = NULL, $from_date = null, $till_date = null, $sort = NULL)
   {
      
   }

   /**
    * Return the balloon with the given id  
    * 
    * @param type $balloon_id
    */
   public function get_balloon($balloon_id)
   {
      
   }

   //put your code here
}
