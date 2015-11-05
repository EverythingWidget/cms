<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace webroot;

/**
 * Description of Home
 *
 * @author Eeliya
 */
class Home extends \ew\Module
{

   protected $resource = "api";
   
   public function get_title()
   {
      return 'Home Page';
   }

   protected function install_permissions()
   {
      $this->register_permission("hompage", "User can view the home page", [
          'html/index.php']);
   }

}
