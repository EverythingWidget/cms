<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ew;

/**
 * Description of APIResponse
 *
 * @author Eeliya
 */
class APIResponse implements \JsonSerializable {

  //put your code here

  private $type = null;
  public $data = [];
  public $properties = [];
  private $links = [];

  public function __construct() {
    $this->properties = [
        'status_code' => 200
        ];
  }

  public function set_status_code($code) {
    $this->properties['status_code'] = $code;
    http_response_code($code);
  }

  public function set_type($type) {
    $this->properties['status_code'] = $type;
  }

  public function set_data($data) {
    $this->data = $data;
  }

  public function set_meta($meta) {
    $this->properties['meta'] = $meta;
  }
  
  public function add_link($key,$value) {
    if(!in_array('links', $this->properties)) {
      $this->properties['links'] = [];
    }
    
    $this->properties['links'][$key] = $value;
  }

  public function to_json() {
    return json_encode($this->to_array());
  }

  public function to_array() {
    $type = null;
//    if (!is_null($this->data)) {
//      $type = array_keys($this->data) === range(0, count($this->data) - 1) ? 'list' : 'item';
//    }
//
//    if (!is_null($this->type)) {
//      $type = $this->type;
//    }

    return array_merge(array_filter($this->properties), [
        'data' => $this->data
    ]);
  }

  public function jsonSerialize() {
    $cloned = clone $this;
    $cloned->data = '@data';
    return $cloned->to_array();
  }

}
