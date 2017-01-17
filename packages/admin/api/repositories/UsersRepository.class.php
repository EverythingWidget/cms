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
class UsersRepository implements \ew\CRUDRepository
{

  public function __construct()
  {
    require_once EW_PACKAGES_DIR . '/admin/api/models/ew_users.php';
  }

  public function create($input)
  {
    $result = new \ew\Result();
    $validation_result = \SimpleValidator\Validator::validate((array)$input, ew_users::$RULES);

    if ($validation_result->isSuccess() !== true) {
      $result->error = 400;
      $result->message = $validation_result->getErrors();

      return $result;
    }

    if (!isset($input->group_id)) {
      $input->group_id = 1;
    }

    $user = new ew_users();
    $user->fill((array)$input);
    $user->password = static::generate_hash($input->password);
    $datetime = new \DateTime();
    $user->date_created = $datetime->format('y-m-d H:i:s');
    if (!$user->save()) {
      $result->error = 500;
      $result->message = 'user has not been created';

      return $result;
    }

    $result->message = 'user has been created';
    $result->data = $user;

    return $result;
  }

  public function read($input)
  {
    if (isset($input->id)) {
      return $this->find_by_id($input->id);
    }

    return $this->all($input->page, $input->page_size);
  }

  public function update($input)
  {
    $result = new \ew\Result();

    $user = ew_users::find($input->id);
    if (!empty($input->password)) {

      if (!isset($input->new_password)) {
        $result->error = 400;
        $result->message = 'New password can not be empty';
        return $result;
      }

      if (!UsersManagement::verify_hash($input->password, $user->password)) {
        $result->error = 400;
        $result->message = 'Old password is wrong';
        return $result;
      }

      if ($input->new_password !== $input->new_password_repeat) {
        $result->error = 400;
        $result->message = 'New password and its repeat are not equal';
        return $result;
      }
    } else {
      $input->password = 'UNSET_THIS';
    }

    $validation_result = \SimpleValidator\Validator::validate((array)$input, ew_users::$RULES);

    if ($input->password === 'UNSET_THIS') {
      unset($input->password);
    } else {
      $input->password = static::generate_hash($input->new_password);
    }

    if (!$validation_result->isSuccess()) {
      $result->error = 400;
      $result->message = $validation_result->getErrors();

      return $result;
    }

    $user = ew_users::find($input->id);

    if (!$user) {
      $result->error = 404;
      $result->message = 'user not found';

      return $result;
    }

    $user->fill((array)$input);
    $user->save();

    $result->data = $user;
    $result->message = 'user has been updated';

    return $result;
  }

  public function delete($input)
  {
    $result = new \ew\Result();

    $result->message = 'user has been deleted';

    $user = ew_users::find($input->id);

    if (!$user) {
      $result->error = 404;
      $result->message = 'user not found';

      return $result;
    }

    if (!$user->delete()) {
      $result->error = 500;
      $result->message = 'user has not been deleted';

      return $result;
    }

    $result->data = $user;

    return $result;
  }

  // ------ //

  public function all($page = 0, $page_size = 100)
  {
    if (!isset($page_size)) {
      $page_size = 100;
    }

    $result = new \ew\Result();

    $result->total = ew_users::count();
    $result->page_size = $page_size;
    $result->data = ew_users::with('group')->take($page_size)->skip($page * $page_size)->get();

    return $result;
  }

  public function find_by_id($id)
  {
    $result = new \ew\Result();

    $data = ew_users::with('group')->find($id);

    if (!$data) {
      $result->error = 404;
      $result->message = 'user not found';

      return $result;
    }

    $result->data = $data;

    return $result;
  }

  public static function generate_hash($password)
  {
    $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
    $salt = sprintf("$2a$%02d$", 10) . $salt;
    return crypt($password, $salt);
  }

}
