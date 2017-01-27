<?php

namespace ew;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResourceHandler
 *
 * @author Eeliya
 */
abstract class ResourceHandler {

  private $app;
  private $resource_handler_parameters = [];
  private $handler_class;

  public function __construct($app) {
    $this->app = $app;
    $this->handler_class = (new \ReflectionClass($this))->getShortName();
  }

  public function process($app, $package, $resource_type, $section_name, $method_name, $parameters) {
    $this->resource_handler_parameters = $parameters;
    return $this->handle($app, $package, $resource_type, $section_name, $method_name, $parameters);
  }

  protected abstract function handle($parent, $package, $resource_type, $section_name, $method_name, $parameters);

  protected function get_parameter($key) {
    return $this->resource_handler_parameters["_" . $this->handler_class . "_" . $key];
  }

}
