<?php

namespace ew;

/**
 * Description of WidgetFeeder
 *
 * @author Eeliya
 */
class WidgetFeeder {

  public $id = "";
  public $url = "";
  public $module;
  public $method_name;
  public $feeder_type = 'widget';
  public $title = 'Widget';
  public $description = 'This is a widget';
  public $api_url;
  private $resourse_type = 'api';
  private $feeder_types = ['widget'];

  public function __construct($url, $module, $feeder_type, $method_name, $resource_type = 'api') {
    $this->url = (substr($url, -1) === "/") ? $url : "$url/";
    $this->module = $module;
    $this->feeder_type = $feeder_type;
    $this->feeder_types = array_map('trim', explode(',', $feeder_type));
    $this->method_name = $method_name;
    $this->resourse_type = $resource_type;
    $link = str_replace('_', '-', $module->get_app()->get_root()) . '/' . $resource_type . '/' . \EWCore::camelToHyphen($module->get_name() . '/' . $method_name);
    $this->id = $link;
    $this->api_url = $link;
  }

  public function is_type($type) {
    return in_array($type, $this->feeder_types);
  }

  public function set_title($title) {
    $this->title = $title;
  }

  public function get_title() {
    return $this->title;
  }

  public function set_api_url($api_url) {
    $this->api_url = $api_url;
  }

}
