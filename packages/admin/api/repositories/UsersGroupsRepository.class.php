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
class UsersGroupsRepository implements \ew\CRUDRepository {

  public function __construct() {
    require_once '/../models/ew_users_groups.php';
  }

  public function create($input) {
    $result = new \ew\Result();
    $validation_result = \SimpleValidator\Validator::validate((array) $input, ew_users_groups::$RULES);

    if ($validation_result->isSuccess() !== true) {
      $result->error = 400;
      $result->message = $validation_result->getErrors();

      return $result;
    }

    if (!isset($input->type)) {
      $input->type = 'default';
    }

    $group = new ew_users_groups();
    $group->fill((array) $input);
    $datetime = new \DateTime();
    $group->date_created = $datetime->format('y-m-d H:i:s');
    $group->save();

    $result->data = $group;
    $result->message = 'users group has been created';

    return $result;
  }

  public function delete($input) {
    $result = new \ew\Result();

    $result->message = 'users group has been deleted';

    $group = ew_users_groups::find($input->id);

    if (!$group) {
      $result->error = 404;
      $result->message = 'users group not found';

      return $result;
    }

    if (!$group->delete()) {
      $result->error = 500;
      $result->message = 'users group has not been deleted';

      return $result;
    }

    $result->data = $group;

    return $result;
  }

  public function read($input) {
    if (isset($input->id)) {
      return $this->find_by_id($input->id);
    }

    return $this->all($input->page, $input->page_size);
  }

  public function update($input) {
    $result = new \ew\Result();
    $validation_result = \SimpleValidator\Validator::validate((array) $input, ew_users_groups::$RULES);

    if (!$validation_result->isSuccess()) {
      $result->error = 400;
      $result->message = $validation_result->getErrors();

      return $result;
    }

    if (!isset($input->type)) {
      $input->type = 'default';
    }

    $group = ew_users_groups::find($input->id);

    if (!$group) {
      $result->error = 404;
      $result->message = 'users group not found';

      return $result;
    }

    $group->fill((array) $input);
    $group->save();

    $result->data = $group;
    $result->message = 'users group has been updated';

    return $result;
  }

  public function all($page = 0, $page_size = 100) {
    if (is_null($page)) {
      $page = 0;
    }

    if (is_null($page_size)) {
      $page_size = 100;
    }

    $result = new \ew\Result();

    $result->total = ew_users_groups::count();
    $result->page_size = $page_size;
    $result->data = ew_users_groups::take($page_size)->skip($page * $page_size)->get();

    return $result;
  }

  public function find_by_id($id) {
    $result = new \ew\Result();

    $data = ew_users_groups::find($id);

    if (!$data) {
      $result->error = 404;
      $result->message = 'users group not found';

      return $result;
    }

    $result->data = $data;

    return $result;
  }

//put your code here
}
