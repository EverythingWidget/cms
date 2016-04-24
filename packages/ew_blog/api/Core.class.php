<?php

namespace ew_blog;

use EWCore;

/**
 * Description of SectionManagement
 *
 * @author Eeliya
 */
class Core extends \ew\Module {

  protected $resource = "api";

  public function get_title() {
    return "Apps";
  }

  protected function install_assets() {

//    $table_install = EWCore::create_table('ew_blog_subscribers', [
//                'id' => 'BIGINT(20) AUTO_INCREMENT PRIMARY KEY',
//                'email' => 'VARCHAR(200) NOT NULL',
//                'options' => 'TEXT NOT NULL',
//                'date_created' => 'DATETIME NOT NULL'
//    ]);
//
//    $pdo = EWCore::get_db_PDO();
//    $stm = $pdo->prepare($table_install);
//    if (!$stm->execute()) {
//      echo json_encode(EWCore::log_error(500, '', $stm->errorInfo()));
//    }
    $table_install = EWCore::create_table('ew_blog_posts', [
                'id'             => 'BIGINT AUTO_INCREMENT PRIMARY KEY',
                'content_id'     => 'VARCHAR(200) NOT NULL',
                'visibility'     => 'VARCHAR(300) NOT NULL',
                'post_status'    => 'TINYINT(1) NULL',
                'draft'          => 'BOOLEAN',
                'date_published' => 'DATETIME NULL',
                'post_order'     => 'SMALLINT DEFAULT 0',
                'user_id'        => 'BIGINT(20) NOT NULL'
    ]);

    $pdo = EWCore::get_db_PDO();
    $stm = $pdo->prepare($table_install);
    if (!$stm->execute()) {
      echo EWCore::log_error(500, '', $stm->errorInfo());
    }

    $this->register_content_component("event", [
        "title"       => "Event",
        "description" => "Event information",
        "explorer"    => "ew-blog/html/core/explorer-event.php",
        "explorerUrl" => "~ew-blog/html/core/explorer-event.php",
        "form"        => "ew-blog/html/core/label-event.php"
    ]);

    $events_feeder = new \ew\WidgetFeeder("events", $this, "list", "ew_list_feeder_events");
    $events_feeder->title = "events";
    \webroot\WidgetsManagement::register_widget_feeder($events_feeder);

    $posts_feeder = new \ew\WidgetFeeder("posts", $this, "list", "ew_list_feeder_posts");
    $posts_feeder->title = "posts";
    \webroot\WidgetsManagement::register_widget_feeder($posts_feeder);

    $post_feeder = new \ew\WidgetFeeder("post", $this, "page", "ew_page_feeder_post");
    $post_feeder->title = "post";
    \webroot\WidgetsManagement::register_widget_feeder($post_feeder);

    EWCore::register_form("ew/ui/forms/content/tabs", "post-publish", [
        'title' => 'Publish',
        "form"  => EWCore::get_view('ew_blog/html/core/tab-post-publish.php')
    ]);

    $this->add_listener('admin/api/content-management/add-article', 'call_on_article_update');
    $this->add_listener('admin/api/content-management/update-article', 'call_on_article_update');
    $this->add_listener('admin/api/content-management/get-article', 'call_on_article_get');
  }

  protected function install_permissions() {
    $this->register_public_access([
        'api/get_app_sections',
        'api/get_apps',
        'api/ew_list_feeder_events',
        'api/ew-list-feeder-posts',
        'api/ew-page-feeder-post',
    ]);
  }

  public function get_app_sections($appDir) {
    $app_class_name = $appDir . '\\App';
    if (class_exists($app_class_name)) {
      // Create an instance of section with its parent App
      $obj = new $app_class_name;
      return json_encode($obj->get_app_api_modules());
    }
  }

  public function get_apps($type = "all") {
    $path = EW_PACKAGES_DIR . '/';

    $apps_dirs = opendir($path);
    $apps = array();
    if (!isset($type))
      $type = "all";

    while ($app_dir = readdir($apps_dirs)) {
      if (strpos($app_dir, '.') === 0)
        continue;

      if (!is_dir($path . $app_dir))
        continue;

      $app_dir_content = opendir($path . $app_dir);

      while ($file = readdir($app_dir_content)) {

        if (strpos($file, '.') === 0)
          continue;
        //$i = strpos($file, '.ini');

        if (strpos($file, ".app.php") != 0) {
          require_once EW_PACKAGES_DIR . "/" . $app_dir . "/" . $file;
          $app_class_name = $app_dir . "\\" . substr($file, 0, strpos($file, "."));
          $app_object = new $app_class_name();
          //echo $app_object->get_type() . "\n\r";

          if ($type === "all") {
            $apps[] = $app_object->get_app_details();
          }
          else if ($app_object->get_type() == $type) {
            $apps[] = $app_object->get_app_details();
          }
        }
      }
    }
    return json_encode($apps);
  }

  public function ew_list_feeder_events($id, $_language, $params = [], $order_by = 'DESC') {
    if (!isset($id)) {
      return \ew\APIResourceHandler::to_api_response([]);
    }

    $query = \admin\ew_contents::select([
                'ew_contents.id',
                'ew_contents.content',
                'ew_contents.content_fields',
                'events.value AS event_date'
    ]);
    
    $query->where('parent_id', '=', $id)
            ->join('ew_contents_labels as langs', 'ew_contents.id', '=', 'langs.content_id')
            ->join('ew_contents_labels as events', 'ew_contents.id', '=', 'events.content_id')
            ->where('type', 'article')
            ->where('langs.key', 'admin_ContentManagement_language')
            ->where('langs.value', $_language)
            ->where('events.key', 'ew_blog_Core_event');

    if (strtoupper($order_by) === 'DEFAULT') {
      $order_by = 'DESC';
    }

    $from_date = date("Y-m-d");
    switch ($params['show']) {
      case 'all':
        break;
      default:
        $query->whereDate('events.value', '>=', $from_date);
        break;
    }

    $query->orderBy("events.value", $order_by);

    $events = $query->get();

    if (isset($events)) {
      foreach ($events as $article) {
        $result[] = [
            "id"             => $article->id,
            "html"           => $article->content,
            'event_date'     => $article->event_date,
            "content_fields" => json_decode($article->content_fields, true)
        ];
      }
    }

    return \ew\APIResourceHandler::to_api_response($result, [
                'page_size'       => $events->count(),
                "collection_size" => $events->count()
    ]);
  }

  public function ew_list_feeder_posts($id, $params = [], $token = 0, $size = 30, $order_by = 'DESC', $_language = 'en') {
    $query = \admin\ew_contents::select([
                'ew_contents.id',
                'date_created',
                'content_fields',
                'content',
                'posts.date_published'
    ]);

    $query->where('parent_id', '=', $id)
            ->join('ew_contents_labels as langs', 'ew_contents.id', '=', 'langs.content_id')
            ->join('ew_blog_posts as posts', 'ew_contents.id', '=', 'posts.content_id')
            ->where('type', 'article')
            ->where('langs.key', 'admin_ContentManagement_language')
            ->where('langs.value', $_language)
            ->where('posts.date_published', '<>', '0000-00-00 00:00:00');

    if (strtoupper($order_by) === 'DEFAULT') {
      $order_by = 'DESC';
    }

    $from_date = date("Y-m-d");

    switch ($params['show']) {
      case 'future':
        $query->whereDate('posts.date_published', '>', $from_date);
        break;
      case 'all':
        break;
      default:
        $query->whereDate('posts.date_published', '<=', $from_date);
        break;
    }

    $query->orderBy("posts.date_published", $order_by)
            ->take($size)
            ->skip($token);

    $articles = $query->get([
    ]);

    $posts = array_map(function($row) {
      $row["content_fields"] = json_decode($row["content_fields"], true);
      return $row;
    }, $articles->toArray());

    $result = [];
    if (isset($posts)) {
      foreach ($posts as $post) {
        $content_fields = $post["content_fields"];
        $content_fields['@content/date-created'] = [
            'tag'     => 'p',
            'content' => \DateTime::createFromFormat('Y-m-d H:i:s', $post['date_created'])->format('Y-m-d')
        ];

        $content_fields['@post/date-published'] = [
            'tag'     => 'p',
            'content' => \DateTime::createFromFormat('Y-m-d H:i:s', $post['date_published'])->format('Y-m-d')
        ];

        $result[] = [
            "id"             => $post["id"],
            "html"           => $post["content"],
            "content_fields" => $content_fields
        ];
      }
    }

    $folder_info = \EWCore::call_cached_api('admin/api/content-management/contents', [
                'id' => $id
    ]);

    return \ew\APIResourceHandler::to_api_response($result, [
                'parent_content_fields' => $folder_info['data']['content_fields'],
                'page_size'             => count($articles),
                "collection_size"       => count($articles)
    ]);
  }

  public function ew_page_feeder_post($id, $params = [], $_language = 'en') {
    $post = \admin\ew_contents::join('ew_contents_labels as langs', 'ew_contents.id', '=', 'langs.content_id')
                    ->join('ew_blog_posts as posts', 'ew_contents.id', '=', 'posts.content_id')
                    ->where('ew_contents.id', '=', $id)
                    ->where('langs.key', 'admin_ContentManagement_language')
                    ->where('langs.value', $_language)
                    ->where('posts.publish_date', '<>', '0000-00-00 00:00:00')
                    ->get([
                        'ew_contents.id',
                        'ew_contents.title',
                        'ew_contents.content',
                        'ew_contents.parsed_content',
                        'ew_contents.content_fields',
                        'posts.date_published'
                    ])->toArray();

    if (count($post) > 0) {
      $post_data = $post[0];
      $result["title"] = $post_data["title"];
      $result["content"] = $post_data["content"];
      $result["parsed_content"] = $post_data["parsed_content"];
      $result["content_fields"] = json_decode($post_data["content_fields"], true);
      $result["publish_date"] = $post_data["publish_date"];
      return \ew\APIResourceHandler::to_api_response($result, ["type" => "object"]);
    }
    return \ew\APIResourceHandler::to_api_response([], ["type" => "object"]);
  }

  public function call_on_article_update($id, $__response_data, $ew_blog) {
    $pdo = EWCore::get_db_PDO();
    $publish_date = $ew_blog['date_published'];
    $table_name = 'ew_contents';
    $post_id = \ew\DBUtility::row_exist($pdo, 'ew_blog_posts', $id, 'content_id');
    if ($post_id) {
      $this->update_post($post_id['id'], $publish_date);
    }
    else {
      $this->add_post($__response_data['data']['id'], $publish_date);
    }

    return [
        'data'     => [
            'date_published' => $publish_date,
        ],
        'included' => [
            [
                'type' => 'ew_blog_post',
                'id'   => $post_id['id']
            ]
        ]
    ];
  }

  public function call_on_article_get($articleId) {
    $post = $this->get_post($articleId);
    $date = $post['date_published'];

    if (!$date || $date === '0000-00-00 00:00:00') {
      $date = '';
    }
    else {
      $date = \DateTime::createFromFormat('Y-m-d H:i:s', $post['date_published'])->format('Y-m-d');
    }

    $result = [];

    if ($post) {
      $result['data'] = [
          'ew_blog/date_published' => $date
      ];
    }

    return $result;
  }

  public function get_post($content_id) {
    $pdo = EWCore::get_db_PDO();
    $stmt = $pdo->prepare("SELECT * FROM ew_blog_posts WHERE content_id = ?");

    $stmt->execute([$content_id]);

    return $stmt->fetch(\PDO::FETCH_ASSOC);
  }

  public function add_post($content_id, $publish_date) {
    $pdo = EWCore::get_db_PDO();
    $stmt = $pdo->prepare("INSERT INTO ew_blog_posts(content_id, date_published, user_id) VALUES (?, ?, ?)");
    return $stmt->execute([$content_id, $publish_date, $_SESSION['EW.USER_ID']]);
  }

  public function update_post($id, $publish_date) {
    $pdo = EWCore::get_db_PDO();
    $stmt = $pdo->prepare("UPDATE ew_blog_posts SET date_published = ? WHERE id = ?");

    return $stmt->execute([$publish_date, $id]);
    //return $stmt->queryString;
  }

}
