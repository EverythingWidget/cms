<?php


namespace ew;

use SimpleValidator\Validator;
/**
 * Description of UsersGroupsRepository
 *
 * @author Eeliya
 */
class SimpleRepository implements CRUDRepository {

  protected $path_to_model = '';
  protected $model_name = '';
  protected $name = 'table';

  public function __construct() {
    //require_once EW_PACKAGES_DIR . '/ew-blog/api/models/ew_blog_posts.php';

    require_once EW_PACKAGES_DIR . $this->path_to_model;
  }

  public function create($input) {
    $result = new Result();
    $class_name = $this->model_name;
    $validation_result = Validator::validate((array) $input, $class_name::$RULES);

    if ($validation_result->isSuccess() !== true) {
      $result->error = 400;
      $result->message = $validation_result->getErrors();

      return $result;
    }

    if (!isset($input->type)) {
      $input->type = 'default';
    }

    $comment = new $class_name();
    $comment->fill((array) $input);

    try {
      $comment->save();
    }
    catch (\PDOException $exception) {
      $result->error = 201;
      $error_code = $exception->errorInfo[1];

      if ($error_code === 1062) {
        $result->message = $this->name . ' is duplicate';
        $result->message_code = 'duplicate';
      }

      return $result;
    }

    $result->data = $comment;
    $result->message = $this->name . ' has been created';

    return $result;
  }

  public function delete($input) {
    $result = new Result();
    $class_name = $this->model_name;

    $result->message = $this->name . ' has been deleted';

    $group = $class_name::find($input->id);

    if (!$group) {
      $result->error = 404;
      $result->message = $this->name . ' not found';

      return $result;
    }

    if (!$group->delete()) {
      $result->error = 500;
      $result->message = $this->name . ' has not been deleted';

      return $result;
    }

    $result->data = $group;

    return $result;
  }

  /**
   * 
   * @param \stdClass $input
   * @return Result
   */
  public function read($input) {
    if (isset($input->id)) {
      return $this->find_by_id($input->id);
    }

    return $this->all($input->page, $input->start, $input->page_size, $input->filter);
  }

  /**
   * 
   * @param \stdClass $input
   * @return Result
   */
  public function update($input) {
    $result = new Result();
    $class_name = $this->model_name;

    $validation_result = Validator::validate((array) $input, $class_name::$RULES);

    if (!$validation_result->isSuccess()) {
      $result->error = 400;
      $result->message = $validation_result->getErrors();

      return $result;
    }

    $comment = $class_name::find($input->id);

    if (!$comment) {
      $result->error = 404;
      $result->message = $this->name . ' not found: ' . $input->id;

      return $result;
    }

    $comment->fill((array) $input);
    $comment->save();

    $result->data = $comment;
    $result->message = $this->name . ' has been updated';

    return $result;
  }

  public function all($page = 0, $start = 0, $page_size = 100, $filter = null) {
    if (is_null($page)) {
      $page = 0;
    }

    if (is_null($page_size)) {
      $page_size = 100;
    }

    $class_name = $this->model_name;

    $query = $class_name::select();

    DBUtility::filter($query, $filter);

    $result = new Result();
//die($query->toSql());
    $result->total = $query->get()->count();
    $result->page = intval($page);
    $result->start = intval($start);
    $result->page_size = intval($page_size);

    //if ($start) {
    $result->data = $query->take($page_size)->skip($start)->get();
//      die($query->toSql());
    //}
    //else {
    //  $result->data = $query->take($page_size)->skip($page * $page_size)->get();
    //}

    return $result;
  }

  public function find_by_id($id) {
    $result = new Result();
    $class_name = $this->model_name;

    $data = $class_name::find($id);

    if (!$data) {
      $result->error = 404;
      $result->message = $this->name . ' not found: ' . $id;

      return $result;
    }

    $result->data = $data;

    return $result;
  }

  /**
   * 
   * @param {Array} $params
   * @return \Illuminate\Database\Query\Builder
   */
  public function new_select($params) {
    $class_name = $this->model_name;
    return $class_name::select($params);
  }

//put your code here
}
