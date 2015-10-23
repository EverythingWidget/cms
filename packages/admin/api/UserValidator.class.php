<?php

namespace admin;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserValidator
 *
 * @author Eeliya
 */
class UserValidator extends \ew\PreProcessor
{

   public function get_users_list($module, $method_name, $verb, $input)
   {
      //print_r($input);
      //return json_encode($input);
      return true;
      //return \EWCore::log_error(400, "You can not use this module for now: $verb");
   }

   //put your code here
}
