<?php

namespace ew\blog;


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

   protected $name = "Blog";
   protected $description = "Show contents to the users";
   protected $version = "0.9";   
   
   public function index()
   {
      return [
          'module' => 'home',
          'file'   => 'index.php'
      ];
   }

}
