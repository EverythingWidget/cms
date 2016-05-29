<?php

namespace admin;

/**
 * Description of ContentsRepository
 *
 * @author Eeliya
 */
class ContentsRepository implements \ew\CRUDRepository {
  
  public function __construct() {
    require_once '../models/ew_contents.php';;
  }

  public function create($input) {
    $result = new \stdClass;
    $result->error = 503;
    $result->message = 'ContentsRepository: REST create functionality is not implemented';

    return $result;
  }

  public function delete($input) {
    $result = new \stdClass;
    $result->error = 503;
    $result->message = 'ContentsRepository: REST delete functionality is not implemented';

    return $result;
  }

  /**
   * 
   * @param type $input
   * @return mixed
   */
  public function read($input) {
    if (isset($input->id)) {
      return $this->find_by_id($input->id, $input->_language);
    }

    return $this->all($input->page, $input->page_size);
  }

  public function update($input) {
    $result = new \stdClass;
    $result->error = 503;
    $result->message = 'ContentsRepository: REST update functionality is not implemented';

    return $result;
  }

  // ------ //

  private function get_content_labels($content_id, $key = '%') {
    if (preg_match('/\$content\.(\w*)/', $content_id))
      return [];
    if (!$key)
      $key = '%';
    $labels = \ew_contents_labels::where('content_id', '=', $content_id)->where('key', 'LIKE', $key)->get();
    return $labels->toArray();
  }

  private function parse_labels($labels, $data) {
    return array_map(function ($label) use ($data) {
      if (preg_match('/{@fields\/(.*)}/', $label['value'], $match) === 1) {
        if (isset($data['content_fields'][$match[1]])) {
          $label['value'] = $data['content_fields'][$match[1]]['content'];
        }
      }

      return $label;
    }, $labels);
  }

  public function all($page = 0, $page_size = 100) {
    if (is_null($page)) {
      $page = 0;
    }
    if (is_null($page_size)) {
      $page_size = 100;
    }

    $contents = ew_contents::orderBy('title')->take($page_size)->skip($page * $page_size)->get(['*',
        \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")]);

    $data = array_map(function($e) {
      $e["content_fields"] = json_decode($e["content_fields"], true);
      return $e;
    }, $contents->toArray());

    $result = new \stdClass;

    $result->total = ew_contents::all()->count();
    $result->size = $page_size;
    $result->data = $data;

    return $result;
  }

  public function find_by_id($id, $language = 'en') {
    $result = new \stdClass;

    if (!isset($id)) {
      $result->error = 400;
      $result->message = 'tr{Content Id is requird}';
      return $result;
    }

    $content = ew_contents::find($id, ['*',
                \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")]);

    if (isset($content)) {
      $content->content_fields = json_decode($content->content_fields, true);
      $labels = $this->get_content_labels($id);
      $content->labels = $labels;
      $content->labels = $this->parse_labels($labels, $content);

      $result->data = $content->toArray();

      return $result;
    }

    $result->error = 404;
    $result->message = 'Content not found';

    return $result;
  }

}
