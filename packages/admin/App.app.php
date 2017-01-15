<?php

namespace admin;

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
