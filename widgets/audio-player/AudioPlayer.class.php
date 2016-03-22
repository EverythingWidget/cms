<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace webroot;

/**
 * Description of GoogleMap
 *
 * @author Eeliya
 */
class AudioPlayer implements Widget {

  public function get_configuration_form($widget_parameters = []) {
    ob_start();
    include 'form-config.php';
    return ob_get_clean();
  }

  public function get_description() {
    return 'Add html audio player to your layout';
  }

  public function get_title() {
    return 'Audio Player';
  }

  public function render($widget_parameters, $widget_id, $style_id, $style_class) {
    WidgetsManagement::add_html_script([
        'include' => 'rm/public/js/audiojs/audio.min.js'
    ]);
    ob_start();
    include 'render.php';
    return ob_get_clean();
  }

  public function get_feeder_type() {
    return null;
  }

//put your code here
}
