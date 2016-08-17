<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ew_blog;

/**
 * Description of Comments
 *
 * @author Eeliya
 */
class Comments extends \ew\Module {

  protected $resource = "api";

  public function get_title() {
    return "EW Blog comments";
  }

  protected function install_assets() {
    $table_install = \EWCore::create_table('ew_blog_comments', [
                'id'           => 'BIGINT AUTO_INCREMENT PRIMARY KEY',
                'content_id'   => 'BIGINT NOT NULL',
                'parent_id'    => 'BIGINT NOT NULL',
                'name'         => 'VARCHAR(300) NULL',
                'email'        => 'VARCHAR(300) NULL',
                'commenter_id' => 'BIGINT NULL',
                'content'      => 'TEXT NULL',
                'visibility'   => 'VARCHAR(300)',
                'date_created' => 'DATETIME NULL',
                'date_updated' => 'DATETIME NULL'
    ]);

    $PDO = \EWCore::get_db_PDO();
    $create_table_statement = $PDO->prepare($table_install);
    if (!$create_table_statement->execute()) {
      echo \EWCore::log_error(500, '', $create_table_statement->errorInfo());
    }

    $commnets_feeder = new \ew\WidgetFeeder('comments', $this, 'list', 'comments_feeder');
    $commnets_feeder->title = 'Comments list';
    \webroot\WidgetsManagement::register_widget_feeder($commnets_feeder);

    require_once EW_PACKAGES_DIR . '/ew-blog/api/models/ew_blog_comments.php';
  }

  protected function install_permissions() {
    $this->register_public_access([
        'api/create',
        'api/read',
        'api/update',
        'api/delete',
        'api/options',
        'api/comments-feeder'
    ]);
  }

  public function create(\ew\APIResponse $_response, $_input) {
    $result = (new CommentsRepository())->create($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function read(\ew\APIResponse $_response, $_input, $_identifier) {
    $_input->id = $_identifier;

    $result = (new CommentsRepository())->read($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function update(\ew\APIResponse $_response, $_input, $_identifier) {
    $_input->id = $_identifier;

    $result = (new CommentsRepository())->update($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function delete(\ew\APIResponse $_response, $_input, $_identifier) {
    $_input->id = $_identifier;

    $result = (new CommentsRepository())->delete($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function options() {
    return [
        'name'        => 'EW Blog - Comments',
        'description' => 'Add comments feature to the contents',
        'version'     => '0.5.0'
    ];
  }

  public function comments_feeder($_response, $id, $params = [], $token = 0, $page_size = 30, $order_by = 'DESC', $_language = 'en') {
    $query = ew_blog_comments::select([
                'id',
                'name',
                'email',
                'content',
                'date_updated'
    ]);

    $query->where('content_id', '=', $id);

    $collection_size = $query->get()->count();

    $query->orderBy('date_updated', $order_by)
            ->take($page_size)
            ->skip($token);

    $comments = $query->get();

    $result = new \ew\Result;

    $result->total = $collection_size;
    $result->page_size = $comments->count();

    $comments_list = new \Illuminate\Database\Eloquent\Collection;

    foreach ($comments as $comment) {
      $comments_list->add([
          'id'             => $comment->id,
          'html'           => $comment->content,
          'content_fields' => [
              'name'    => [
                  'tag'     => 'p',
                  'content' => $comment->name
              ],
              'email'   => [
                  'tag'     => 'p',
                  'content' => $comment->email
              ],
              'content' => [
                  'tag'     => 'p',
                  'content' => $comment->content
              ],
              'date'    => [
                  'tag'     => 'p',
                  'content' => $comment->date_updated->toDateTimeString()
              ]
          ]
      ]);
    }

    $result->data = $comments_list;

    return \ew\APIResponse::standard_response($_response, $result);
  }

}
