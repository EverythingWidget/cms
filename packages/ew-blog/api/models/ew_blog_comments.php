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
class ew_blog_comments extends \Illuminate\Database\Eloquent\Model {

  protected $table = 'ew_blog_comments';
  protected $fillable = [
      'content_id',
      'parent_id',
      'name',
      'email',
      'commenter_id',
      'content',
      'visibility'
  ];
  public static $rules = [];
  protected $casts = [
      'parent_id'    => 'integer',
      'commenter_id' => 'integer'
  ];

  const CREATED_AT = 'date_created';
  const UPDATED_AT = 'date_updated';

  public static $RULES = [
      'content_id' => [
          'required'
      ]
  ];

  public function ewContent() {
    return $this->belongsTo('admin\ew_contents', 'content_id', 'id');
  }

}
