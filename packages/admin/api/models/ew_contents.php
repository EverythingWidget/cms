<?php

namespace admin;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ew_settings
 *
 * @author Eeliya
 */
class ew_contents extends \Illuminate\Database\Eloquent\Model
{

   protected $fillable = array(
       'id',
       'author_id',
       'type',
       'title',
       'slug',
       'keywords',
       'description',
       'parent_id',
       'featured_image',
       'content',
       'date_modified');
   public static $rules = [
       'title' => array(
           'required'
       ),
       'type' => array(
           'required'
       ),
       'parent_id' => array(
           'integer'
   )];

   public function __construct(array $attributes = array())
   {
      parent::__construct($attributes);
      $this->timestamps = false;
      //static::$validator->customErrors($errors_array)
   }

   public function ew_contents_labels()
   {
      return $this->hasMany('ew_contents_labels', 'content_id');
   }

}
