<?php

namespace core;

use EWCore;

/**
 * Description of SectionManagement
 *
 * @author Eeliya
 */
class Registry extends \ew\Module {

  protected $resource = "api";

  public function get_title() {
    return "EW Core";
  }

  protected function install_assets() {
    
  }

  protected function install_permissions() {
    $this->register_public_access([
        'api/read_activities',
        'api/read_items'
    ]);
  }

  public function read_activities($_response) {
    $_response->properties['system_version'] = $this->get_app()->get_app_version();

    return \EWCore::read_activities_as_array();
  }

  public function read_items($_response, $key) {
    $_response->properties['system_version'] = $this->get_app()->get_app_version();

    if (!isset($key)) {
      $key = '*';
    }

    return \EWCore::read_registry_as_array($key);
  }

}
