<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ew_blog;

/**
 * Description of Comments
 *
 * @author Eeliya
 */
class Comments extends \ew\Module {

  protected $resource = "api";

  public function get_title() {
    return "EW Blog comments";
  }

  protected function install_assets() {
    $table_install = \EWCore::create_table('ew_blog_comments', [
                'id'           => 'BIGINT AUTO_INCREMENT PRIMARY KEY',
                'content_id'   => 'BIGINT NOT NULL',
                'parent_id'    => 'BIGINT NOT NULL',
                'content'      => 'TEXT NULL',
                'visibility'   => 'VARCHAR(300)',
                'date_created' => 'DATETIME NULL',
                'date_updated' => 'DATETIME NULL',
                'date_deleted' => 'DATETIME NULL'
    ]);

    $PDO = \EWCore::get_db_PDO();
    $create_table_statement = $PDO->prepare($table_install);
    if (!$create_table_statement->execute()) {
      echo \EWCore::log_error(500, '', $create_table_statement->errorInfo());
    }
  }

  protected function install_permissions() {
    $this->register_public_access([
        'api/create',
        'api/read',
        'api/update',
        'api/delete'
    ]);
  }

  public function create(\ew\APIResponse $_response, $_input) {
    $result = (new CommentsRepository())->create($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function read(\ew\APIResponse $_response, $_input) {
    $result = (new CommentsRepository())->read($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function update(\ew\APIResponse $_response, $_input) {
    $result = (new CommentsRepository())->update($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function delete(\ew\APIResponse $_response, $_input) {
    $result = (new CommentsRepository())->delete($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

}
