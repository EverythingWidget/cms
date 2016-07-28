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
    
  }

  protected function install_permissions() {
    $this->register_public_access([
        'api/create',
        'api/read',
        'api/update',
        'api/delete'
    ]);
  }

  public function create() {
    return ['create: not implemented'];
  }

  public function read($_input) {
    return [
        'message' => 'read: not implemented',
        'inputs'  => $_input
    ];
  }

  public function update() {
    return ['update: not implemented'];
  }

  public function delete() {
    return ['delete: not implemented'];
  }

}
