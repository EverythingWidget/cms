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
   
   protected function install_assets() {
     \EWCore::register_object(\webroot\App::$HOME_PAGE_JS_PLUGINS, 'google-analytics',[
        'path'=>'webroot/html/home/google-analytics.php'
    ]);
    
    \EWCore::register_object(\webroot\App::$HOME_PAGE_JS_PLUGINS, 'google-amp',[
        'path'=>'webroot/html/home/google-amp.php'
    ]);
    
    \EWCore::register_object(\webroot\App::$HOME_PAGE_JS_PLUGINS, 'ew-widgets-data',[
        'path'=>'webroot/html/home/ew-widgets-data.php'
    ]);
   }

   protected function install_permissions()
   {
      $this->register_permission("hompage", "User can view the home page", [
          'html/index.php']);
   }

}
