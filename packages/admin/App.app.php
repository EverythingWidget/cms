<?php

namespace admin;

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
class App extends \ew\App {

  protected $name = "EW Admin";
  protected $description = "EverythingWidget administration panel";
  protected $version = "0.8";
  protected $type = "core_app";

  public function init() {
    
  }

  public function index() {
    return [
        'module' => 'dashboard',
        'file'   => 'index.php'
    ];
  }

}
