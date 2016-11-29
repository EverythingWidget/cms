<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ew_blog;

/**
 * Description of ew_blog_subscribers
 *
 * @author Eeliya
 */
class ew_blog_subscribers {

  protected $table = 'ew_blog_subscribers';
  protected $fillable = [
      'email',
      'options',
      'date_created'
  ];
  public static $rules = [];
  public static $RULES = [
      'email' => [
          'required'
      ]
  ];
  protected $casts = [
      'options' => 'object'
  ];

  const CREATED_AT = 'date_created';
  const UPDATED_AT = null;
}
