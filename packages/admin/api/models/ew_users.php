<?php

namespace admin;

/**
 * Description of ew_settings
 *
 * @author Eeliya
 */
class ew_users extends \Illuminate\Database\Eloquent\Model {

  protected $fillable = [
      'email',
      'password',
      'first_name',
      'last_name',
      'type',
      'group_id',
      'permission',
      'verfication_code',
      'verified',
      'disable'
      ];
  
  public static $RULES = [
      'email'     => [
          'required',
          'email'
      ],
      'password' => [
          'required'
  ]];

  public function __construct(array $attributes = []) {
    parent::__construct($attributes);
    $this->timestamps = false;
  }

  public function group() {
    return $this->belongsTo('ew_users_groups', 'group_id');
  }

}
