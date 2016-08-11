<?php

namespace ew_blog;

/**
 * Description of CommentsRepository
 *
 * @author Eeliya
 */
class CommentsRepository extends \ew\SimpleRepository {
  
  protected $path_to_model = '/ew-blog/api/models/ew_blog_comments.php';
  protected $model_name = 'ew_blog\ew_blog_comments';
  protected $name = 'comment';
}
