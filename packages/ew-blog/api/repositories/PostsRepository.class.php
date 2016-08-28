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
  
  public function all($page = 0, $page_size = 100, $filter = null) {
    if (is_null($page)) {
      $page = 0;
    }

    if (is_null($page_size)) {
      $page_size = 100;
    }

    $class_name = $this->model_name;

    $query = $class_name::with('content')->select();

    \ew\DBUtility::filter($query, $filter);

    $result = new \ew\Result();

    $result->total = $class_name::count();
    $result->page_size = intval($page_size);
    $result->data = $query->take($page_size)->skip($page * $page_size)->get();

    return $result;
  }

}
