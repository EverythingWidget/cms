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
    $feeder_name = $_REQUEST['_section_name'];
    $feeder_app = 'admin';
    $priority_with_url = $widget_parameters['priority-with-url'];
    $language = 'en';
    if ($_REQUEST['_language'])
      $language = $_REQUEST['_language'];

    if ($widget_parameters['feeder']) {
      if (is_string($widget_parameters['feeder'])) {
        $feeder = json_decode($widget_parameters['feeder'], TRUE);
      }
      else {
        $feeder = $widget_parameters['feeder'];
      }
      $feeder_id = $feeder['feederId'];
    }

    if ($priority_with_url == 'yes' && $_REQUEST['_file'] && $_REQUEST['_module_name']) {
      $is_feeder_app = WidgetsManagement::get_widget_feeder_by_url($_REQUEST['_module_name']);

      if ($is_feeder_app) {
        $feeder_id = $is_feeder_app->api_url;
        $feeder['id'] = $_REQUEST['_method_name'];

      }
    }

    ob_start();
    include 'render.php';
    return ob_get_clean();
  }

  public function get_feeder_type() {
    return null;
  }

//put your code here
}
