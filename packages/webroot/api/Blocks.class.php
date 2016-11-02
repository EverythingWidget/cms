<?php

namespace webroot;

/**
 *
 * @author Eeliya
 */
class Blocks extends \ew\Module {

  protected $resource = "api";
  private $app_blocks = [];

  protected function install_assets() {
//    $table_install = \EWCore::create_table('ew_layout_blocks', [
//                'id'           => 'BIGINT AUTO_INCREMENT PRIMARY KEY',
//                'name'         => 'VARCHAR(300) NULL',
//                'structure'    => 'BLOB NULL',
//                'date_created' => 'DATETIME NULL',
//                'date_updated' => 'DATETIME NULL'
//    ]);
//
//    $PDO = \EWCore::get_db_PDO();
//    $create_table_statement = $PDO->prepare($table_install);
//    if (!$create_table_statement->execute()) {
//      echo \EWCore::log_error(500, '', $create_table_statement->errorInfo());
//    }
  }

  public function get_title() {
    return 'Blocks';
  }

  protected function install_permissions() {
    
  }

  public function read($_response) {
//    $path = EW_ROOT_DIR . '/blocks/';
//
//    $blocks_dir = opendir($path);
//    while ($block_dir = readdir($blocks_dir)) {
//      if (strpos($block_dir, '.') === 0)
//        continue;
//    }
//
//
//    if (file_exists(EW_WIDGETS_DIR . '/' . $widget_type . "/$widge_class.class.php")) {
//      require_once EW_WIDGETS_DIR . '/' . $widget_type . "/$widge_class.class.php";
//      $widget_class_name = "webroot\\$widge_class";
//      $widget_class_instance = (new $widget_class_name());
//      $widget_title = $widget_class_instance->get_title();
//      $widget_content_raw = $widget_class_instance->render($widget_parameters, $widget_id, $style_id, $style_class);
//      $widget_content = str_replace(['{$widget_id}', '$widget_id_js'], [$widget_id, str_replace('-', '_', $widget_id)], $widget_content_raw);
//    }
  }

}
