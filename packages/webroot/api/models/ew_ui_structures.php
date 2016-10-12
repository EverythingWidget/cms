<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace webroot;

/**
 * Description of ew_ui_structures
 *
 * @author Eeliya
 */
class ew_ui_structures extends \Illuminate\Database\Eloquent\Model {

  protected $table = 'ew_ui_structures';
  protected $fillable = [
      'name',
      'template',
      'template_settings',
      'preview_url',
      'structure'
  ];
  protected $casts = [
      'template_settings' => 'object',
      'structure'         => 'array'
  ];
  public static $RULES = [
      'name' => [
          'required'
      ]
  ];

  public function __construct(array $attributes = []) {
    parent::__construct($attributes);
    $this->timestamps = false;
  }

}
