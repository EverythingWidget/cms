<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ew_contents_label
 *
 * @author Eeliya
 */
class ew_contents_labels extends \Illuminate\Database\Eloquent\Model {

  protected $fillable = ['content_id', 'key', 'value'];
  protected $casts = [
      'content_fields' => 'array',
  ];

  public function __construct(array $attributes = []) {
    parent::__construct($attributes);
    $this->timestamps = false;
  }

  //put your code here
}
