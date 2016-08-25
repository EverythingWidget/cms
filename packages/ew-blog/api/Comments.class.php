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


    \EWCore::register_ui_element('apps/ew-blog/navs', 'comments', [
        'id'    => 'ew-blog/comments',
        'title' => 'Comments',
        'url'   => 'html/ew-blog/comments/component.php'
    ]);

    require_once EW_PACKAGES_DIR . '/ew-blog/api/models/ew_blog_comments.php';
    require_once EW_PACKAGES_DIR . '/ew-blog/api/models/ew_blog_posts.php';
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
    // check if content_id is commentable
    $result = new \ew\Result();
    if (!is_numeric($_input->content_id)) {
      $result->error = 400;
      $result->message = 'content_id must be an integer';

      return \ew\APIResponse::standard_response($_response, $result);
    }

    $comments = 0;
    $repository = new PostsRepository();
    $content_id = $_input->content_id;

    //$original_post = $post = $repository->find_with_content_id($content_id);

    while ($comments === 0) {
      $post = $repository->find_with_content_id($content_id);

      // if parent content is not a post, then ignore it and assume commenting is disabled
      if (!$post->data) {
        $post->error = 400;
        $post->message = 'commenting is disabled on this post, no parent post';

        return \ew\APIResponse::standard_response($_response, $post);
      }

      if ($post->data->content->parent_id === 0) {
        break;
      }

      $content_id = $post->data->content->parent_id;
      $comments = $post->data->comments;
    }

    if ($post->data->comments === -1) {
      $post->error = 400;
      $post->message = 'commenting is disabled on this post';

      return \ew\APIResponse::standard_response($_response, $post);
    }

    $comment = (new CommentsRepository())->create($_input);
    return \ew\APIResponse::standard_response($_response, $comment);
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

  public function comments_feeder($_response, $id, $token = 0, $page_size = 30, $order_by = 'DESC') {
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
