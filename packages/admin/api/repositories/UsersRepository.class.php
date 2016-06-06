<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace admin;

/**
 * Description of UsersGroupsRepository
 *
 * @author Eeliya
 */
class UsersRepository implements \ew\CRUDRepository {

  public function __construct() {
    require_once '/../models/ew_users.php';
    ;
  }

  public function create($input) {
    
  }

  public function delete($input) {
    
  }

  public function read($input) {
    if (isset($input->id)) {
      return $this->find_by_id($input->id);
    }

    return $this->all($input->page, $input->page_size);
  }

  public function update($input) {
    
  }

  public function all($page = 0, $page_size = 100) {
    if (!isset($page_size)) {
      $page_size = 100;
    }

    $result = new \stdClass;

    $result->total = ew_users::count();
    $result->page_size = $page_size;
    $result->data = ew_users::take($page_size)->skip($page * $page_size)->get();

    return $result;
  }

  public function find_by_id($id) {
    $result = new \stdClass;
    
    $result->data = ew_users::find($id);

    return $result;
  }

//put your code here
}
