<?php

namespace ew_blog;

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

  protected $name = "Blog";
  protected $description = "EverythingWidget administration panel";
  protected $version = "0.8";

  protected function init() {
    \EWCore::register_ui_element('apps', 'ew-blog', [
        "title"       => $this->name,
        "id"          => 'ew-blog',
        "url"         => 'html/ew-blog/core/index.php',
        "description" => $this->description
    ]);
  }

}
