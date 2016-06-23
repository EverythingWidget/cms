<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace webroot;

/**
 * Description of LayoutsRepository
 *
 * @author Eeliya
 */
class LayoutsRepository implements \ew\CRUDRepository {

  public function __construct() {
    require_once EW_PACKAGES_DIR . '/webroot/api/models/ew_ui_structures.php';
  }

  public function create($input) {
    
  }

  public function delete($input) {
    
  }

  public function read($input) {
    if (isset($input->id)) {
      return $this->find_by_id($input->id);
    }

    return $this->all();
  }

  public function update($input) {
    
  }

  // ------ //

  public function all($page = 0, $page_size = 100) {
    if (is_null($page)) {
      $page = 0;
    }
    if (is_null($page_size)) {
      $page_size = 100;
    }

    $data = ew_ui_structures::take($page_size)->skip($page * $page_size)->get();

    $result = new \ew\Result();

    $result->total = ew_ui_structures::all()->count();
    $result->size = $page_size;
    $result->data = $data;

    return $result;
  }

  public function find_by_id($id) {
    $result = new \ew\Result();
    $layout = ew_ui_structures::find($id);

    $default_uis = WidgetsManagement::get_path_uis("@DEFAULT");
    $home_uis = WidgetsManagement::get_path_uis("@HOME_PAGE");

    if (!$layout) {
      $result->error = 404;
      $result->message = 'layout_not_found';

      return $result;
    }

    if ($default_uis["id"] == $id) {
      $layout["uis-default"] = true;
    }

    if ($home_uis["id"] == $id) {
      $layout["uis-home-page"] = true;
    }

    $result->data = $layout;

    return $result;
  }

}
