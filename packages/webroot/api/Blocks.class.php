<?php

namespace webroot;

/**
 *
 * @author Eeliya
 */
class Blocks extends \ew\Module {

  protected $resource = 'api';

  protected function install_assets() {
    if (!in_array('ew_layout_blocks', \EWCore::$DEFINED_TABLES)) {
      $table_install = \EWCore::create_table('ew_layout_blocks', [
                  'id'           => 'BIGINT AUTO_INCREMENT PRIMARY KEY',
                  'name'         => 'VARCHAR(300) NULL',
                  'structure'    => 'BLOB NULL',
                  'date_created' => 'DATETIME NULL',
                  'date_updated' => 'DATETIME NULL'
      ]);

      $PDO = \EWCore::get_db_PDO();
      $create_table_statement = $PDO->prepare($table_install);
      if (!$create_table_statement->execute()) {
        echo \EWCore::log_error(500, '', $create_table_statement->errorInfo());
      }
    }
  }

  public function get_title() {
    return 'Blocks';
  }

  protected function install_permissions() {
    $this->register_permission('view', 'User can view the blocks section', [
        'api/read',
        'html/blocks-tabs/component.php',
        'html/block-form/component.php'
    ]);

    $this->register_permission('manipulate', 'User can add, edit and remove a block', [
        'api/create',
        'api/update',
        'api/delete',
        'html/blocks-tabs/component.php',
        'html/block-form/component.php'
    ]);

    $this->register_public_access([
        'api/options'
    ]);
  }

  public function create(\ew\APIResponse $_response, $_input) {
    $result = (new LayoutBlocksRepository())->create($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function read(\ew\APIResponse $_response, $_input, $_identifier) {
    $_input->id = $_identifier;

    $result = (new LayoutBlocksRepository())->read($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function update(\ew\APIResponse $_response, $_input, $_identifier) {
    $_input->id = $_identifier;

    $result = (new LayoutBlocksRepository())->update($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function delete(\ew\APIResponse $_response, $_input, $_identifier) {
    $_input->id = $_identifier;

    $result = (new LayoutBlocksRepository())->delete($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function options() {
    return [
        'name'        => 'Webroot - Layout Blocks',
        'description' => 'This module provides layout block feature for webroot layouts.',
        'version'     => '0.5.0'
    ];
  }

}
