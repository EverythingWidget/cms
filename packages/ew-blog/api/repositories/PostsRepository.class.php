<?php

namespace ew_blog;

/**
 * Description of PostsRepository
 *
 * @author Eeliya
 */
class PostsRepository extends \ew\SimpleRepository {

  protected $path_to_model = '/ew-blog/api/models/ew_blog_posts.php';
  protected $model_name = 'ew_blog\ew_blog_posts';
  protected $name = 'post';

  /**
   * 
   * @param type $content_id
   * @return \ew\Result
   */
  public function find_with_content_id($content_id) {
    $result = new \ew\Result();
    $class_name = $this->model_name;

    $result->data = $class_name::with('content')->where('content_id', $content_id)->get()->first();

    return $result;
  }

}
