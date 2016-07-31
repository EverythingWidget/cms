<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ew_blog;

/**
 * Description of UsersGroupsRepository
 *
 * @author Eeliya
 */
class CommentsRepository implements \ew\CRUDRepository {

  public function __construct() {
    require_once EW_PACKAGES_DIR . '/ew-blog/api/models/ew_blog_comments.php';
  }

  public function create($input) {
    $result = new \ew\Result();
    $validation_result = \SimpleValidator\Validator::validate((array) $input, ew_blog_comments::$RULES);

    if ($validation_result->isSuccess() !== true) {
      $result->error = 400;
      $result->message = $validation_result->getErrors();

      return $result;
    }

    if (!isset($input->type)) {
      $input->type = 'default';
    }

    $comment = new ew_blog_comments();
    $comment->fill((array) $input);
    $comment->save();

    $result->data = $comment;
    $result->message = 'comment has been created';

    return $result;
  }

  public function delete($input) {
    $result = new \ew\Result();

    $result->message = 'comment has been deleted';

    $group = ew_blog_comments::find($input->id);

    if (!$group) {
      $result->error = 404;
      $result->message = 'comment not found';

      return $result;
    }

    if (!$group->delete()) {
      $result->error = 500;
      $result->message = 'comment has not been deleted';

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
    $validation_result = \SimpleValidator\Validator::validate((array) $input, ew_blog_comments::$RULES);

    if (!$validation_result->isSuccess()) {
      $result->error = 400;
      $result->message = $validation_result->getErrors();

      return $result;
    }

    $comment = ew_blog_comments::find($input->id);

    if (!$comment) {
      $result->error = 404;
      $result->message = 'comment not found: ' . $input->id;

      return $result;
    }

    $comment->fill((array) $input);
    $comment->save();

    $result->data = $comment;
    $result->message = 'comment has been updated';

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

    $result->total = ew_blog_comments::count();
    $result->page_size = $page_size;
    $result->data = ew_blog_comments::take($page_size)->skip($page * $page_size)->get();

    return $result;
  }

  public function find_by_id($id) {
    $result = new \ew\Result();

    $data = ew_blog_comments::find($id);

    if (!$data) {
      $result->error = 404;
      $result->message = 'comment not found: ' . $id;

      return $result;
    }

    $result->data = $data;

    return $result;
  }

//put your code here
}
