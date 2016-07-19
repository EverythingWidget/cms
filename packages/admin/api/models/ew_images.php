<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace admin;

/**
 * Description of ew_images
 *
 * @author Eeliya
 */
class ew_images extends \Illuminate\Database\Eloquent\Model{
  protected $table = 'ew_images';
  
  protected $fillable = [
      'content_id',
      'source',
      'alt_text'
  ];
  
  public static $RULES = [
      'source'    => [
          'required'
      ]
  ];

  public function __construct(array $attributes = []) {
    require_once 'ew_contents.php';
    parent::__construct($attributes);

    $this->timestamps = false;
  }

  public function content() {
    return $this->belongsTo('admin\ew_contents', 'content_id');
  }
}
