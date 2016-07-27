<?php

namespace core;

/**
 *
 * @author Eeliya
 */
class Helper extends \ew\Module {

  protected $resource = "api";

  public function get_title() {
    return 'EW Developer Helper';
  }

  protected function install_assets() {
    
  }

  protected function install_permissions() {
    $this->register_public_access([
        'api/request',
        'api/read'
    ]);
  }

  public function request($_input) {
    return $_input;
  }

  public function read() {
    return [
        'title'       => $this->get_title(),
        'description' => $this->get_description()
    ];
  }

}
