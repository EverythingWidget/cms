<?php

namespace ew_blog;

/**
 * Description of Comments
 *
 * @author Eeliya
 */
class Posts extends \ew\Module {

  protected $resource = 'api';

  public function get_title() {
    return "EW Blog Comments";
  }

  protected function install_assets() {
    $table_install = \EWCore::create_table('ew_blog_posts', [
                'id'             => 'BIGINT AUTO_INCREMENT PRIMARY KEY',
                'content_id'     => 'VARCHAR(200) NOT NULL',
                'visibility'     => 'VARCHAR(300) NOT NULL',
                'post_status'    => 'TINYINT(1) NULL',
                'draft'          => 'BOOLEAN',
                'comments'       => 'TINYINT(1) NOT NULL DEFAULT 0',
                'date_published' => 'DATETIME NULL',
                'post_order'     => 'SMALLINT DEFAULT 0',
                'user_id'        => 'BIGINT(20) NOT NULL'
    ]);

    $pdo = \EWCore::get_db_PDO();
    $stm = $pdo->prepare($table_install);
    if (!$stm->execute()) {
      echo \EWCore::log_error(500, '', $stm->errorInfo());
    }

    \EWCore::register_ui_element('apps/ew-blog/navs', 'posts', [
        'id'    => 'ew-blog/posts',
        'title' => 'Posts',
        'url'   => 'html/ew-blog/posts/component.php'
    ]);
  }

  protected function install_permissions() {
    $this->register_permission('see-comments', 'User can see comments', [
        'api/read'
    ]);

    $this->register_permission('manipulate-comments', 'User can add, edit and delete comments', [
        'api/create',
        'api/update',
        'api/delete'
    ]);

    $this->register_public_access([
        'api/options',
        'api/read',
        'api/included-contents-read',
        'api/included-contents-2-read'
    ]);
  }

  public function create(\ew\APIResponse $_response, $_input) {
    $result = (new PostsRepository())->create($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function read(\ew\APIResponse $_response, $_input, $_identifier) {
    $_input->id = $_identifier;

    $result = (new PostsRepository())->read($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function update(\ew\APIResponse $_response, $_input, $_identifier) {
    $_input->id = $_identifier;

    $result = (new PostsRepository())->update($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function delete(\ew\APIResponse $_response, $_input, $_identifier) {
    $_input->id = $_identifier;

    $result = (new PostsRepository())->delete($_input);

    return \ew\APIResponse::standard_response($_response, $result);
  }

//  public function included_contents_read(\ew\APIResponse $_response, $_input, $_identifier) {
//    $_input->id = $_identifier;
//    $_input->filter = [
//        'include' => ['content'],
//        'order'   => ['ew_contents.date_modified']
//    ];
//
//    $result = (new PostsRepository())->read($_input);
//
//    return \ew\APIResponse::standard_response($_response, $result);
//  }

  public function included_contents_read(\ew\APIResponse $_response, $_input, $_identifier) {
    $_input->id = $_identifier;
    $_input->filter = [
        'include' => ['content']
    ];

    $query = (new PostsRepository())->new_select(['ew_blog_posts.*']);

    $query->with('content')->join('ew_contents', 'ew_blog_posts.content_id', '=', 'ew_contents.id');
    $query->orderBy('ew_contents.date_modified', 'desc');

    $result = New \ew\Result;
    
    $result->total = $query->get()->count();
    
    \ew\DBUtility::paginate($query, $_input->start, $_input->page_size);

    $result->data = $query->get();        
    $result->start = intval($_input->start);
    $result->page_size = intval($_input->page_size);

    return \ew\APIResponse::standard_response($_response, $result);
  }

  public function options() {
    return [
        'name'        => 'EW Blog - Comments',
        'description' => 'Add comments feature to the contents',
        'version'     => '0.5.0'
    ];
  }

}
