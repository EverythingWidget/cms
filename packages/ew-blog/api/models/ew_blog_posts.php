<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ew_blog_posts
 *
 * @author Eeliya
 */
class ew_blog_posts extends \Illuminate\Database\Eloquent\Model {

  protected $table = 'ew_blog_posts';
  protected $fillable = [
      'content_id',
      'visibility',
      'post_status',
      'draft',
      'date_published',
      'post_order',
      'user_id'
  ];
  public static $rules = [];
  protected $casts = [];

}
