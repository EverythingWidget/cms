<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace admin;

/**
 * Description of Dashboard
 *
 * @author Eeliya
 */
class Dashboard extends \ew\Module
{

   protected $resource = "api";
   
   public function get_title()
   {
      return "Dashboard";
   }
   protected function install_permissions()
   {
      $this->register_permission("dashboard", "User can view the admin dashboard", [
          'html/index.php']);
   }

}
