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

  public $data = [];
  public $properties = [];
  public $downloadable = false;

  public function __construct($url) {
    $this->properties = [
        'status_code' => 200,
        'url' => $url
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

  public function add_link($key, $value) {
    if (!in_array('links', $this->properties)) {
      $this->properties['links'] = [];
    }

    $this->properties['links'][$key] = $value;
  }

  public function to_json() {
    return json_encode($this->to_array());
  }

  function result_filter($var) {
    return ($var !== NULL && $var !== FALSE && $var !== '' && $var !== []);
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

    return array_merge(array_filter($this->properties, [$this, 'result_filter']), [
        'data' => $this->data
    ]);
  }

  public function to_file() {
    return $this->data;
  }

  public function as_download($data) {
    $this->downloadable = true;
    $this->set_data($data['data']);
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Length: " . strlen($data['data']) . ";");
    header("Content-Disposition: attachment; filename=\"{$data['name']}\"");
    header("Content-Type: {$data['contentType']}");

    return $this;
  }

  public function jsonSerialize() {
    $cloned = clone $this;
    $cloned->data = '@data';
    return $cloned->to_array();
  }

  /** Creates a standard response out of <var>$_response</var> with passed <var>$result</var>
   *
   * @param APIResponse $_response The response object
   * @param Result $result The repository result
   * @return array
   */
  public static function standard_response(APIResponse $_response, $result) {
    $_response->properties['message'] = $result->message;
    $_response->properties['message_code'] = $result->message_code;


    if ($result->error) {
      $_response->set_status_code($result->error);

      //return $result->data;
      return $result->reason;
    }

    foreach ($result as $key => $value) {
//           print "$key => $value\n";
      if ($key === 'data') {
        continue;
      }

      $_response->properties[$key] = $value;
    }

//    if (is_array($_response->data)) {
//      $_response->properties['response_type'] = 'list';
//    } else {
//      $_response->properties['response_type'] = 'object';
//    }
//    die($_response->properties['total']);

//    $_response->properties['total'] = $result->total;
//    $_response->properties['page_size'] = $result->page_size;

    return $result->data->toArray();
  }

}
