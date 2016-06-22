<?php

namespace ew;

/**
 * Description of Result
 *
 * @author Eeliya
 */
class Result extends \stdClass {

  public $error;

  /**
   *
   * @var \Illuminate\Database\Eloquent\Collection A collection containing the repository result. Default is null
   */
  public $data;

  /**
   *
   * @var string 
   */
  public $message;

  public function __construct() {
    $this->data = null;
  }

}
