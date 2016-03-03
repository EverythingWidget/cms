<?php

namespace ew_blog;

use Module;
use EWCore;

/**
 * Description of SectionManagement
 *
 * @author Eeliya
 */
class Core extends \ew\Module {

  protected $resource = "api";

  public function get_title() {
    return "Apps";
  }

  protected function install_assets() {
    
    $table_install = EWCore::create_table('ew_blog_subscribers', [
        'id'=>'BIGINT(20) AUTO_INCREMENT PRIMARY KEY',
        'email'=>'VARCHAR(200) NOT NULL',
        'options'=>'TEXT NOT NULL',
        'date_created' => 'DATETIME NOT NULL'
    ]);

    $pdo = EWCore::get_db_PDO();    
    $stm = $pdo->prepare($table_install);
    if(!$stm->execute()){
      echo json_encode(EWCore::log_error(500,'',$stm->errorInfo()));
    }
  }

  protected function install_permissions() {
    $this->register_public_access(array(
        "api/get_app_sections",
        "api/get_apps"
    ));
  }

  public function get_app_sections($appDir) {
    $app_class_name = $appDir . '\\App';
    if (class_exists($app_class_name)) {
      // Create an instance of section with its parent App
      $obj = new $app_class_name;
      return json_encode($obj->get_app_api_modules());
    }
  }

  public function get_apps($type = "all") {
    $path = EW_PACKAGES_DIR . '/';

    $apps_dirs = opendir($path);
    $apps = array();
    if (!isset($type))
      $type = "all";

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

}
