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
class Settings extends \ew\Module {

  protected $resource = "api";

  protected function install_assets() {
    \EWCore::register_form("ew/ui/settings/general", "webroot-settings", [
        "title"   => "Webroot",
        "content" => "",
        "url"     => "html/webroot/settings/index.php"
    ]);

    //$this->add_listener("admin/api/settings/save-settings", "testy");
  }

  public function testy($__input) {
    //print_r($__input);
  }

  public function get_title() {
    return 'Settings';
  }

  protected function install_permissions() {
    $this->register_permission("settings", "User can view webroot general settings and configure them", [
        'html/index.php']);
  }

}
