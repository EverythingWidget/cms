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
  
}

