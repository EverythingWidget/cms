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
class ew_layout_blocks extends \Illuminate\Database\Eloquent\Model {

  protected $table = 'ew_layout_blocks';
  protected $fillable = [
      'name',
      'structure'
  ];
  protected $casts = [
      'structure' => 'object'
  ];
  public static $RULES = [
      'name' => [
          'required'
      ]
  ];
}
