<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ew_blog;

use ew\DBUtility;

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
    if (!in_array('ew_blog_comments', \EWCore::$DEFINED_TABLES)) {
      $table_install = DBUtility::create_table('ew_blog_comments', [
          'id' => 'BIGINT AUTO_INCREMENT PRIMARY KEY',
          'content_id' => 'BIGINT NOT NULL',
          'parent_id' => 'BIGINT NOT NULL',
          'name' => 'VARCHAR(300) NULL',
          'email' => 'VARCHAR(300) NULL',
          'commenter_id' => 'BIGINT NULL',
          'content' => 'TEXT NULL',
          'visibility' => 'VARCHAR(300) DEFAULT "not confirmed"',
          'date_created' => 'DATETIME NULL',
          'date_updated' => 'DATETIME NULL'
      ]);

      $PDO = \EWCore::get_db_PDO();
      $create_table_statement = $PDO->prepare($table_install);
      if (!$create_table_statement->execute()) {
        echo \EWCore::log_error(500, '', $create_table_statement->errorInfo());
      }
    }

    \EWCore::register_ui_element('apps/ew-blog/navs', 'comments', [
        'id' => 'ew-blog/comments',
        'title' => 'Comments',
        'url' => 'html/ew-blog/comments/component.php'
    ]);

    require_once EW_PACKAGES_DIR . '/ew-blog/api/models/ew_blog_posts.php';
  }

  protected function install_feeders() {
    $commnets_feeder = new \ew\WidgetFeeder('comments', $this, 'list', 'feeder');
    $commnets_feeder->title = 'Comments list';
    \webroot\WidgetsManagement::register_widget_feeder($commnets_feeder);
  }

  protected function install_permissions() {
    $this->register_public_access([
        'api/create',
        'api/read',
        'api/update',
        'api/delete',
        'api/confirm-update',
        'api/options',
        'api/confirm-capcha',
        'api/feeder',
        'api/is-allowed'
    ]);
  }

  public function create(\ew\APIResponse $_response, $_input) {
    // check if content_id is commentable
    $result = new \ew\Result();
    if (!is_numeric($_input->content_id)) {
      $result->error = 400;
      $result->message = 'content_id must be an integer';
      $_response->set_result($result);

      return $_response;
    }

    $comments_status = 0;
    $repository = new PostsRepository();
    $content_id = $_input->content_id;

    //$original_post = $post = $repository->find_with_content_id($content_id);

    while ($comments_status === 0) {
      $post = $repository->find_with_content_id($content_id);
      $comments = intval($post->data->comments);

      // if parent content is not a post, then ignore it and assume commenting is disabled
      if (!$post->data) {
        $post->error = 400;
        $post->message = 'commenting is disabled on this post, no parent post';
        $_response->set_result($post);

        return $_response;
      }

      if (isset($comments) && $comments !== 0) {
        break;
      }

      if ($post->data->content->parent_id === 0) {
        $default_comments_feature = \EWCore::call_api('admin/api/settings/read-settings', [
            'app_name' => 'ew-blog/comments-feature'
        ])['data']['ew-blog/comments-feature'];

        if (isset($default_comments_feature)) {
          $post->data->comments = intval($default_comments_feature);
        } else {
          $post->data->comments = 1;
        }

        break;
      }

      $content_id = $post->data->content->parent_id;
      $comments_status = $comments;
    }

    if ($comments_status === -1) {
      $post->error = 400;
      $post->message = 'commenting is disabled on this post';
      $_response->set_result($post);

      return $_response;
    }

    if (!$_input->visibility) {
      $_input->visibility = 'not confirmed';
    }

    $comment = (new CommentsRepository())->create($_input);
    $_response->set_result($comment);

    return $_response;
  }

  public function read(\ew\APIResponse $_response, $_input) {
    $result = (new CommentsRepository())->read($_input);
    $_response->set_result($result);

    return $_response;
  }

  public function update(\ew\APIResponse $_response, $_input) {
    $result = (new CommentsRepository())->update($_input);
    $_response->set_result($result);

    return $_response;
  }

  public function delete(\ew\APIResponse $_response, $_input) {
    $result = (new CommentsRepository())->delete($_input);
    $_response->set_result($result);

    return $_response;
  }

  public function confirm_update($_response, $id) {
    $result = (new CommentsRepository())->confirm($id);
    $_response->set_result($result);

    return $_response;
  }

  public function options() {
    return [
        'name' => 'EW Blog - Comments',
        'description' => 'Add comments feature to the contents',
        'version' => '0.5.0'
    ];
  }

  public function confirm_capcha($_input) {
    $url = 'https://www.google.com/recaptcha/api/siteverify';

    $secret = \EWCore::call_api('admin/api/settings/read-settings', [
        'app_name' => 'webroot/google/recaptcha/secret-key'
    ])['data']['webroot/google/recaptcha/secret-key'];

    $response = file_get_contents("$url?secret=" . $secret . "&response=" . $_input->response . "&remoteip=" . $_SERVER['REMOTE_ADDR']);

    return json_decode($response, true);
  }

  public function is_allowed($_response, $id) {
    $comment_status = 0;
    $posts_repository = new PostsRepository();

    while ($comment_status === 0) {
      $post = $posts_repository->find_with_content_id($id);
      $comments = intval($post->data->comments);

      if (isset($comments) && $comments !== 0) {
        $comment_status = $post->data->comments;
        break;
      }

      if (!$post->data || $post->data->content->parent_id === 0) {
        $default_comments_feature = \EWCore::call_api('admin/api/settings/read-settings', [
            'app_name' => 'ew-blog/comments-feature'
        ])['data']['ew-blog/comments-feature'];

        if (isset($default_comments_feature)) {
          $comment_status = intval($default_comments_feature);
        } else {
          $comment_status = 1;
        }

        break;
      }

      $id = $post->data->content->parent_id;
      $comment_status = $comments;
    }

    if ($comment_status === -1) {
      return ['is_allowed' => false];
    } else {
      return ['is_allowed' => true];
    }
  }

  public function feeder($_response, $id, $page = 0, $page_size = 30, $order_by = 'DESC') {
    $comment_status = 0;
    $visibility = null;
    $repository = new CommentsRepository();
    $posts_repository = new PostsRepository();

    $original_id = $id;

    while ($comment_status === 0) {
      $post = $posts_repository->find_with_content_id($id);
      $comments = intval($post->data->comments);

      if (!$post->data) {
        break;
      }

      if (isset($comments) && $comments !== 0) {
        $comment_status = $comments;
        break;
      }

      if ($post->data->content->parent_id === 0) {
        $default_comments_feature = \EWCore::call_api('admin/api/settings/read-settings', [
            'app_name' => 'ew-blog/comments-feature'
        ])['data']['ew-blog/comments-feature'];

        if (isset($default_comments_feature)) {
          $comment_status = intval($default_comments_feature);
        } else {
          $comment_status = 1;
        }

        break;
      }

      $id = $post->data->content->parent_id;
      $comment_status = $comments;
    }

    if ($comment_status === 1 || $comment_status === -1) {
      $visibility = 'confirmed';
    }

    $query = $repository->new_select([
        'id',
        'name',
        'email',
        'content',
        'date_updated'
    ]);

    $query->where('content_id', '=', $original_id);

    if ($visibility) {
      $query->where('visibility', $visibility);
    }

    $query->orderBy('date_created', 'DESC');

    $collection_size = $query->get()->count();

    $query->orderBy('date_updated', $order_by)->take($page_size)->skip($page);

    $comments = $query->get();

    $result = new \ew\Result;

    $result->total = $collection_size;
    $result->page = intval($page);
    $result->page_size = $comments->count();

    $comments_list = new \Illuminate\Database\Eloquent\Collection;

    foreach ($comments as $comment) {
      $comments_list->add([
          'id' => $comment->id,
          'html' => $comment->content,
          'content_fields' => [
              'name' => [
                  'tag' => 'p',
                  'content' => $comment->name
              ],
              'email' => [
                  'tag' => 'p',
                  'content' => $comment->email
              ],
              'content' => [
                  'tag' => 'p',
                  'content' => nl2br($comment->content)
              ],
              'date' => [
                  'tag' => 'p',
                  'content' => $comment->date_updated->toDateTimeString()
              ]
          ]
      ]);
    }

    $result->data = $comments_list;
    $_response->set_result($result);

    return $_response;
  }

}
