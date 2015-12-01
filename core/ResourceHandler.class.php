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
class ResourceHandler
{

  private $app;

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function process($app, $package, $resource_type, $section_name, $method_name, $parameters)
  {
    return $this->handle($app, $package, $resource_type, $section_name, $method_name, $parameters);
  }

  //put your code here
  protected function handle($parent, $package, $resource_type, $section_name, $method_name, $parameters)
  {
    
  }

}
