<?php
namespace admin;


/**
 * Description of ew_settings
 *
 * @author Eeliya
 */
class ew_contents extends \Illuminate\Database\Eloquent\Model {

  protected $table = 'ew_contents';
  protected $fillable = [
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
      'date_modified'
  ];
  public static $rules = [
      'title'     => [
          'required'
      ],
      'type'      => [
          'required'
      ],
      'parent_id' => [
          'integer'
  ]];
  
  protected $casts = [
        'content_fields' => 'array',
    ];

  public function __construct(array $attributes = []) {
    parent::__construct($attributes);
    $this->timestamps = false;
  }

  public function ew_contents_labels() {
    return $this->hasMany('ew_contents_labels', 'content_id');
  }

}
