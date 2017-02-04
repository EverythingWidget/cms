<?php

namespace admin;

use Module;
use EWCore;

/**
 * Description of SectionManagement
 *
 * @author Eeliya
 */
class AppsManagement extends \ew\Module {

  protected $resource = "api";

  public function get_title() {
    return "Apps";
  }

  protected function install_permissions() {
    $this->register_permission("see-apps", "User can see the apps settings", [
        "api/get_app_sections",
        "api/get_apps"
    ]);
  }

  public function get_apps($type = "all") {
    $path = EW_PACKAGES_DIR . '/';

    $apps_dirs = opendir($path);
    $apps = [];
    if (!isset($type))
      $type = "all";

    /* while ($app_dir = readdir($apps_dirs))
      {
      if (strpos($app_dir, '.') === 0)
      continue;

      $app_dir_content = opendir($path . $app_dir);

      while ($file = readdir($app_dir_content))
      {

      if (strpos($file, '.') === 0)
      continue;
      //$i = strpos($file, '.ini');

      if ($file === 'config.ini')
      {
      $apps[] = parse_ini_file($path . $app_dir . '/' . $file);
      }
      }
      } */
    while ($app_dir = readdir($apps_dirs)) {
      if (strpos($app_dir, '.') === 0)
        continue;

      if (!is_dir($path . $app_dir))
        continue;

      $app_dir_content = opendir($path . $app_dir);

      while ($file = readdir($app_dir_content)) {

        if (strpos($file, '.') === 0)
          continue;
        //$i = strpos($file, '.ini');

        if (strpos($file, ".app.php") != 0) {
          require_once EW_PACKAGES_DIR . "/" . $app_dir . "/" . $file;
          $app_class_name = $app_dir . "\\" . substr($file, 0, strpos($file, "."));
          $app_object = new $app_class_name();
          //echo $app_object->get_type() . "\n\r";

          if ($type === "all") {
            $apps[] = $app_object->get_app_details();
          }
          else if ($app_object->get_type() == $type) {
            $apps[] = $app_object->get_app_details();
          }
        }
      }
    }
    return json_encode($apps);
  }

  public function get_description() {
    return "Your app's Control Panel";
  }
}
