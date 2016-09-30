<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ew_blog;

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
      'comments',
      'date_published',
      'post_order',
      'user_id'
  ];
  public static $rules = [];
  public static $RULES = [
      'content_id' => [
          'required'
      ]
  ];
  protected $casts = [
      'comments' => 'integer'
  ];

  public function __construct(array $attributes = []) {
    require_once EW_PACKAGES_DIR . '/admin/api/models/ew_contents.php';

    parent::__construct($attributes);
    $this->timestamps = false;
  }

  public function content() {
    return $this->belongsTo('admin\ew_contents', 'content_id', 'id');
  }

}
