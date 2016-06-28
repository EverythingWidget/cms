<?php

namespace core;

/**
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
        'api/activities-read',
        'api/permissions-read',
        'api/items-read',
        'api/read'
    ]);
  }

  public function activities_read($_response) {
    $_response->properties['system_version'] = $this->get_app()->get_app_version();

    return \EWCore::read_activities_as_array();
  }

  public function permissions_read($_response) {
    return \EWCore::read_permissions_as_array();
  }

  public function items_read($_response, $key) {
    $_response->properties['system_version'] = $this->get_app()->get_app_version();

    if (!isset($key)) {
      $key = '*';
    }

    return \EWCore::read_registry_as_array($key);
  }

  public function read() {
    return [
        'title'       => $this->get_title(),
        'description' => $this->get_description()
    ];
  }

}
