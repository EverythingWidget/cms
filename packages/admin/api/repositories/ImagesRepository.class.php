<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace admin;

/**
 * Description of ImagesRepository
 *
 * @author Eeliya
 */
class ImagesRepository implements \ew\CRUDRepository {

  public function __construct() {
    require_once EW_PACKAGES_DIR . '/admin/api/models/ew_images.php';
  }

  public function create($input) {
    $result = new \ew\Result();
    $validation_result = \SimpleValidator\Validator::validate((array) $input, ew_images::$RULES);

    if ($validation_result->isSuccess() !== true) {
      $result->error = 400;
      $result->message = $validation_result->getErrors();

      return $result;
    }

    if (!isset($input->group_id)) {
      $input->group_id = 1;
    }

    $image = new ew_images();
    $image->fill((array) $input);

    if (!$image->save()) {
      $result->error = 500;
      $result->message = 'image has not been created';

      return $result;
    }

    $result->message = 'image has been created';
    $result->data = $image;

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
    $input->password = 'password may not be updated';
    $validation_result = \SimpleValidator\Validator::validate((array) $input, ew_images::$RULES);
    unset($input->password);

    if (!$validation_result->isSuccess()) {
      $result->error = 400;
      $result->message = $validation_result->getErrors();

      return $result;
    }

    $image = ew_images::find($input->id);

    if (!$image) {
      $result->error = 404;
      $result->message = 'image not found';

      return $result;
    }

    $image->fill((array) $input);
    $image->save();

    $result->data = $image;
    $result->message = 'image has been updated';

    return $result;
  }

  public function delete($input) {
    $result = new \ew\Result();

    $result->message = 'image has been deleted';

    $image = ew_images::find($input->id);

    if (!$image) {
      $result->error = 404;
      $result->message = 'image not found';

      return $result;
    }

    if (!$image->delete()) {
      $result->error = 500;
      $result->message = 'image has not been deleted';

      return $result;
    }

    $result->data = $image;

    return $result;
  }

}
