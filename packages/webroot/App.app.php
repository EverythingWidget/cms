<?php

namespace webroot;


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of admin
 *
 * @author Eeliya
 */
class App extends \ew\App
{

   protected $name = "Webroot";
   protected $description = "Show contents to the users";
   protected $version = "0.9";   
   
   public function index()
   {
      $path = $this->get_path('html/index.php');
      include $path;
   }

}
