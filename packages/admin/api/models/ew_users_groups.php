<?php

namespace admin;

/**
 * Description of ew_users_groups
 *
 * @author Eeliya
 */
class ew_users_groups extends \Illuminate\Database\Eloquent\Model {

  protected $table = 'ew_users_groups';
  protected $fillable = [
      'title',
      'description',
      'type',
      'permission',
  ];


  public static $RULES = [
  ];

  public function __construct(array $attributes = []) {
    parent::__construct($attributes);
    $this->timestamps = false;
  }

  public function users() {
    return $this->hasMany('ew_users', 'group_id');
  }
}
