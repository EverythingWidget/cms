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

  private $status_code = 200;
  private $type = null;
  private $data = [];
  public $properties = [];
  private $meta = [];

  public function __construct() {
    //$this->properties = new \stdClass();
  }

  public function set_status_code($code) {
    $this->status_code = $code;
    http_response_code($code);
  }

  public function set_type($type) {
    $this->type = $type;
  }

  public function set_data($data) {
    $this->data = $data;
  }

  public function set_meta($meta) {
    $this->meta = $meta;
  }

  public function to_json() {
    return json_encode($this->to_array());
  }

  public function to_array() {
    $type = null;
    if (!is_null($this->data)) {
      $type = array_keys($this->data) === range(0, count($this->data) - 1) ? 'list' : 'item';
    }

    if (!is_null($this->type)) {
      $type = $this->type;
    }

    return array_merge([
        'status_code' => $this->status_code,
        'type'        => $type,
        'properties'  => $this->properties,
        'data'        => $this->data
            ], $this->meta);
  }

  public function jsonSerialize() {
    $cloned = clone $this;
    $cloned->data = '@data';
    return $cloned->to_array();
  }

}
