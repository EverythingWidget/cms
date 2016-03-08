<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace webroot;

/**
 * Description of Widget
 *
 * @author Eeliya
 */
interface Widget {
  
  public function get_title();
  public function get_description();
  public function get_feeder_type();
  public function get_configuration_form($widget_parameters = []);
  public function render($widget_parameters, $widget_id, $style_id, $style_class);
}
