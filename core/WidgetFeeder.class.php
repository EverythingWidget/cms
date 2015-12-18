<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ew;

/**
 * Description of WidgetFeeder
 *
 * @author Eeliya
 */
class WidgetFeeder
{

  public $id = "";
  public $module;
  private $resourse_type = "api";
  public $widget_type = "widget";
  public $method_name;
  public $title = "Widget";
  public $description = "This is a widget";
  public $api_url;

  public function __construct($module, $widget_type, $method_name, $resource_type = "api")
  {
    $this->module = $module;
    $this->widget_type = $widget_type;
    $this->method_name = $method_name;
    $this->resourse_type = $resource_type;
    $this->id = $module->get_app()->get_root() . '/' . $resource_type . '/' . \EWCore::camelToHyphen($module->get_name() . '/' . $method_name);
    $this->api_url = $this->id;
  }

}
