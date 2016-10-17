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
class LayoutsRepository extends \ew\SimpleRepository {

  protected $path_to_model = '/webroot/api/models/ew_ui_structures.php';
  protected $model_name = 'webroot\ew_ui_structures';
  protected $name = 'ew_ui_structures';

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

    if ($default_uis['uis_id'] == $id) {
      $layout['uis-default'] = true;
    }

    if ($home_uis['uis_id'] == $id) {
      $layout['uis-home-page'] = true;
    }

    $result->data = $layout;

    return $result;
  }

}
