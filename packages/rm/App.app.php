<?php

namespace rm;

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

   protected $name = "Resource Manager";
   protected $description = "Manage all your internal and external resources";
   protected $version = "0.1";

   public function index()
   {
      return [
          'module' => 'home',
          'file' => 'index.php'
      ];
   }
}
