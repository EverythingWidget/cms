<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ew_blog;

use ew\DBUtility;

/**
 * Description of Subscribers
 *
 * @author Eeliya
 */
class Subscribers extends \ew\Module {

  protected $resource = 'api';

  public function get_title() {
    return 'EW Blog Subscribers';
  }

  protected function install_assets() {
    if (!in_array('ew_blog_subscribers', \EWCore::$DEFINED_TABLES)) {
      $table_install = DBUtility::create_table('ew_blog_subscribers', [
                  'id'           => 'BIGINT AUTO_INCREMENT PRIMARY KEY',
                  'email'        => 'VARCHAR(255) NOT NULL UNIQUE',
                  'options'      => 'TEXT NULL',
                  'date_created' => 'DATETIME NULL'
      ]);

      $pdo = \EWCore::get_db_PDO();
      $stm = $pdo->prepare($table_install);
      if (!$stm->execute()) {
        echo \EWCore::log_error(500, '', $stm->errorInfo());
      }
    }

    \EWCore::register_ui_element('apps/ew-blog/navs', 'subscribers', [
        'id'    => 'ew-blog/subscribers',
        'title' => 'Subscribers',
        'url'   => 'html/ew-blog/subscribers/component.php'
    ]);
  }

  protected function install_permissions() {
    $this->register_permission('see-subscribers', 'User can see subscribers', [
        'api/read'
    ]);

    $this->register_permission('manipulate-subscribers', 'User can add, edit and delete subscribers', [
        'api/update',
        'api/delete'
    ]);

    $this->register_public_access([
        'api/options',
        'api/create'
    ]);
  }

  public function create(\ew\APIResponse $_response, $_input) {
    $result = (new SubscribersRepository())->create($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function read(\ew\APIResponse $_response, $_input) {


    $result = (new SubscribersRepository())->read($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function update(\ew\APIResponse $_response, $_input) {


    $result = (new SubscribersRepository())->update($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function delete(\ew\APIResponse $_response, $_input) {


    $result = (new SubscribersRepository())->delete($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function options() {
    return [
        'name'        => 'EW Blog - Subscribers',
        'description' => 'Add subscribe feature to the contents',
        'version'     => '0.5.0'
    ];
  }

}
