<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ew;

/**
 * Description of UsersGroupsRepository
 *
 * @author Eeliya
 */
class SimpleRepository implements \ew\CRUDRepository {

  protected $path_to_model = '';
  protected $model_name = '';
  protected $name = 'table';

  public function __construct() {
    //require_once EW_PACKAGES_DIR . '/ew-blog/api/models/ew_blog_posts.php';

    require_once EW_PACKAGES_DIR . $this->path_to_model;
  }

  public function create($input) {
    $result = new \ew\Result();
    $class_name = $this->model_name;
    $validation_result = \SimpleValidator\Validator::validate((array) $input, $class_name::$RULES);

    if ($validation_result->isSuccess() !== true) {
      $result->error = 400;
      $result->message = $validation_result->getErrors();

      return $result;
    }

    if (!isset($input->type)) {
      $input->type = 'default';
    }

    $comment = new $class_name();
    $comment->fill((array) $input);
    $comment->save();

    $result->data = $comment;
    $result->message = $this->name . ' has been created';

    return $result;
  }

  public function delete($input) {
    $result = new \ew\Result();
    $class_name = $this->model_name;

    $result->message = $this->name . ' has been deleted';

    $group = $class_name::find($input->id);

    if (!$group) {
      $result->error = 404;
      $result->message = $this->name . ' not found';

      return $result;
    }

    if (!$group->delete()) {
      $result->error = 500;
      $result->message = $this->name . ' has not been deleted';

      return $result;
    }

    $result->data = $group;

    return $result;
  }

  public function read($input) {
    if (isset($input->id)) {
      return $this->find_by_id($input->id);
    }

    return $this->all($input->page, $input->page_size, $input->filter);
  }

  public function update($input) {
    $result = new \ew\Result();
    $class_name = $this->model_name;

    $validation_result = \SimpleValidator\Validator::validate((array) $input, $class_name::$RULES);

    if (!$validation_result->isSuccess()) {
      $result->error = 400;
      $result->message = $validation_result->getErrors();

      return $result;
    }

    $comment = $class_name::find($input->id);

    if (!$comment) {
      $result->error = 404;
      $result->message = $this->name . ' not found: ' . $input->id;

      return $result;
    }

    $comment->fill((array) $input);
    $comment->save();

    $result->data = $comment;
    $result->message = $this->name . ' has been updated';

    return $result;
  }

  public function all($page = 0, $page_size = 100, $filter = null) {
    if (is_null($page)) {
      $page = 0;
    }

    if (is_null($page_size)) {
      $page_size = 100;
    }

    $class_name = $this->model_name;

    $query = $class_name::select();

    \ew\DBUtility::filter($query, $filter);

    $result = new \ew\Result();

    $result->total = $class_name::count();
    $result->page_size = $page_size;
    $result->data = $query->take($page_size)->skip($page * $page_size)->get();

    return $result;
  }

  public function find_by_id($id) {
    $result = new \ew\Result();
    $class_name = $this->model_name;

    $data = $class_name::find($id);

    if (!$data) {
      $result->error = 404;
      $result->message = $this->name . ' not found: ' . $id;

      return $result;
    }

    $result->data = $data;

    return $result;
  }

  public function new_select($params) {
    $class_name = $this->model_name;
    return $class_name::select($params);
  }

//put your code here
}
