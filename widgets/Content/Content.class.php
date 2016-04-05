<?php
namespace webroot;

/**
 * Description of Content
 *
 * @author Eeliya
 */
class Content implements Widget {

  public function get_configuration_form($widget_parameters = []) {
    ob_start();
    include 'form-config.php';
    return ob_get_clean();
  }

  public function get_description() {
    return 'Show an article, app page or select a page feeder';
  }

  public function get_title() {
    return 'Content';
  }

  public function render($widget_parameters, $widget_id, $style_id, $style_class) {
    ob_start();
    include 'render.php';
    return ob_get_clean();
  }

  public function get_feeder_type() {
    return null;
  }

//put your code here
}
