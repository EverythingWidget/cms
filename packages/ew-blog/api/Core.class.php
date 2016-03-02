<?php

namespace blog;

use Module;

/**
 * Description of Settings
 *
 * @author Eeliya
 */
class Core extends \ew\Module {

  protected $resource = "api";

  protected function install_assets() {
    \EWCore::register_app("settings", $this);
    include_once 'models/ew_settings.php';

    \EWCore::register_form("ew/ui/settings/general", "ew-admin-settings", ["title"   => "EW Admin",
        "content" => "",
        "url"     => "~admin/html/settings/settings-index.php"]);
  }

  protected function install_permissions() {
    $this->register_public_access([
        'api/read_settings',
        'api/check_for_updates'
    ]);

    $this->register_permission("settings", "User can view EW Admin general settings and configure them", [
        'api/save_settings',
        'api/do_update',
        'html/settings-index.php'
    ]);
  }

  public function get_title() {
    return "Core";
  }

  public function get_description() {
    return "Comman API of the blog package";
  }

}
