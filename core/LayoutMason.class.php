<?php

namespace ew;

/**
 * Description of WidgetFeeder
 *
 * @author Eeliya
 */
abstract class LayoutMason {

  private $data_keys = [];
  private $title = 'Layout Mason';

  public abstract function __construct();

  public abstract function get_html($data);

  public function get_title() {
    return $this->title;
  }

  protected function set_data_kays($keys) {
    $this->data_keys = $keys;
  }

  public function get_data_keys() {
    return $this->data_keys;
  }

}
