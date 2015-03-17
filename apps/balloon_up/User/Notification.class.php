<?php
namespace balloon_up;
use Section;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Notification
 *
 * @author Eeliya
 */
class Notification extends Section
{

   public function get_title()
   {
      return "Notifications";
   }

   public function get_description()
   {
      return "Manage users notifications, send notifications to specific users";
   }

   public function read_notifications($user_id = null, $from_date, $till_date, $type = null)
   {
      
   }

   //put your code here
}
