<?php

namespace ew\blog;

use Module;

/**
 * Description of Settings
 *
 * @author Eeliya
 */
class Core extends \ew\Module {

  protected $resource = "api";

  protected function install_assets() {

  }

  protected function install_permissions() {
    $this->register_public_access([
        'home/index.php'
    ]);

  }

  public function get_title() {
    return "Core";
  }

  public function get_description() {
    return "Comman API of the blog package";
  }

}
