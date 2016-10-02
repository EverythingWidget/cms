<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace webroot;

/**
 * Description of Commetns
 *
 * @author Eeliya
 */
class Comments implements Widget {

  public function get_configuration_form($widget_parameters = []) {
    ob_start();
    include 'form-config.php';
    return ob_get_clean();
  }

  public function get_description() {
    return 'Add commetns widget to your layout';
  }

  public function get_title() {
    return 'Commetns';
  }

  public function render($widget_parameters, $widget_id, $style_id, $style_class) {
    $content_id = $_REQUEST['_method_name'];
    
    if (boolval($widget_parameters['default_post'])) {
      $content_id = $_REQUEST['_method_name'];
    }

    $is_allowed = \EWCore::call_api('ew-blog/api/comments/is-commenting-allowed', [
                'id' => $_REQUEST['_method_name']
            ])['data']['is_allowed'];

    ob_start();
    include 'render.php';
    return ob_get_clean();
  }

  public function get_feeder_type() {
    return null;
  }

//put your code here
}
