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

  public function confirm($comment_id) {
    $result = new \ew\Result();
    $class_name = $this->model_name;

    $comment = $class_name::find($comment_id);

    if (!$comment) {
      $result->error = 404;
      $result->message = $this->name . ' not found: ' . $comment_id;

      return $result;
    }

    $comment->fill([
        'visibility' => 'confirmed'
    ]);
    $comment->save();

    $result->data = $comment;
    $result->message = $this->name . ' has been confirmed';

    return $result;
  }

}
