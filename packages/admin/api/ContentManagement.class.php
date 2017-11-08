<?php

namespace admin;

use ew\APIResponse;
use ew\DBUtility;
use ew\Result;
use EWCore;

/**
 * Description of ContentManagement.
 *
 * ew-article-form for UI
 * ew-article-action-get, add, update, delete for custom operation for correspondidng action
 * note that custom action is fired after the default action has been done succesfully.
 *
 * @author Eeliya
 */
class ContentManagement extends \ew\Module {

  protected $resource = 'api';
  private $file_types = [
      'jpeg' => 'image',
      'jpg' => 'image',
      'png' => 'image',
      'gif' => 'image',
      'txt' => 'text',
      'mp3' => 'sound',
      'mp4' => 'video'
  ];
  private $images_resources = [
      '/is/htdocs/wp1067381_3GN1OJU4CE/www/culturenights/app/webroot/img/logos/'
  ];

  protected function install_assets() {
    require_once 'models/ew_contents.php';
    require_once 'models/ew_contents_labels.php';
    require_once 'asset/DocumentComponent.class.php';
    require_once 'asset/LanguageComponent.class.php';

    if (!in_array('ew_tags', \EWCore::$DEFINED_TABLES)) {
      $ew_tags_table_install = DBUtility::create_table('ew_tags', [
          'id' => 'BIGINT(20) AUTO_INCREMENT PRIMARY KEY',
          'name' => 'VARCHAR(256) NOT NULL',
          'date_created' => 'DATETIME NOT NULL',
          'date_modified' => 'DATETIME NOT NULL',
          'date_deleted' => 'DATETIME NOT NULL'
      ]);

      $pdo = EWCore::get_db_PDO();
      $stm = $pdo->prepare($ew_tags_table_install);
      if (!$stm->execute()) {
        echo EWCore::log_error(500, '', $stm->errorInfo());
      }
    }

    if (!in_array('ew_contents_tags', \EWCore::$DEFINED_TABLES)) {
      $ew_contents_tags_table_install = DBUtility::create_table('ew_contents_tags', [
          'id' => 'BIGINT(20) AUTO_INCREMENT PRIMARY KEY',
          'content_id' => 'BIGINT(20) NOT NULL',
          'tag_id' => 'BIGINT(20) NOT NULL',
          'date_created' => 'DATETIME NOT NULL',
          'date_modified' => 'DATETIME NOT NULL',
          'date_deleted' => 'DATETIME NOT NULL'
      ]);

      $stm = $pdo->prepare($ew_contents_tags_table_install);
      if (!$stm->execute()) {
        echo EWCore::log_error(500, '', $stm->errorInfo());
      }
    }

    EWCore::register_ui_element('apps', 'content-management', [
        'title' => $this->get_title(),
        'id' => 'content-management',
        'url' => 'html/admin/content-management/index.php',
        'description' => $this->get_description()
    ]);

    $this->register_content_component('document', [
        'title' => 'Document',
        'description' => 'Main document',
        'explorer' => 'admin/html/content-management/documents/explorer-document.php',
        'explorerUrl' => 'html/admin/content-management/documents/explorer-document.php',
        'form' => 'admin/html/content-management/documents/label-document.php'
    ]);

    $this->register_content_component('language', [
        'title' => 'Language',
        'description' => 'Language of the content',
        'explorer' => 'admin/html/content-management/documents/explorer-language.php',
        'explorerUrl' => 'html/admin/content-management/documents/explorer-language.php',
        'form' => 'admin/html/content-management/documents/label-language.php'
    ]);

    EWCore::register_ui_element('apps/contents/navs', 'documents', [
        'id' => 'content-management/documents',
        'title' => 'Documents',
        'url' => 'html/admin/content-management/documents/component.php'
    ]);

    EWCore::register_ui_element('apps/contents/navs', 'media', [
        'id' => 'content-management/media',
        'title' => 'Media',
        'url' => 'html/admin/content-management/media/component.php'
    ]);

    EWCore::register_ui_element('forms/content/tabs', 'properties', [
        'title' => 'Properties',
        'template_url' => 'admin/html/content-management/content-form/properties.php'
    ]);

    EWCore::register_ui_element('forms/content/tabs', 'content-html', [
        'title' => 'Content',
        'template_url' => 'admin/html/content-management/content-form/editor.php'
    ]);

    EWCore::register_ui_element('forms/content/tabs', 'json-linked-data', [
        'title' => 'JSON Linked Data',
        'template_url' => 'admin/html/content-management/content-form/json-linked-data.php'
    ]);

    EWCore::register_ui_element('components/link-chooser', 'custom-url', [
        'title' => 'URL',
        'template_url' => 'admin/html/content-management/link-chooser/custom-url.php'
    ]);

    EWCore::register_ui_element('components/link-chooser', 'content-chooser', [
        'title' => 'Contents',
        'template_url' => 'admin/html/content-management/link-chooser/documents.php'
    ]);
  }

  protected function install_handlers() {
    EWCore::register_handler(\ew\HTMLResourceHandler::PAGE_UIS_HANDLER, [
        'object' => $this,
        'method' => 'page_uis_handler_documents'
    ]);
  }

  protected function install_feeders() {
    $article_feeder = new \ew\WidgetFeeder('articles', $this, 'page', 'ew-page-feeder-articles');
    $article_feeder->set_title('articles');
    \webroot\WidgetsManagement::register_widget_feeder($article_feeder);

    $folder_feeder = new \ew\WidgetFeeder('folders', $this, 'list', 'ew-list-feeder-folders');
    $folder_feeder->set_title('folders');
    \webroot\WidgetsManagement::register_widget_feeder($folder_feeder);

    $content_feeder = new \ew\WidgetFeeder('pages', $this, 'page,list', 'feeder');
    $content_feeder->set_title('Pages');
    \webroot\WidgetsManagement::register_widget_feeder($content_feeder);
  }

  protected function install_permissions() {
    $this->register_permission('see-content', 'User can see the contents', [
        'api/index',
        'api/content_fields',
        'api/contents-labels',
        'api/folders_read',
        'api/get_media_list',
        'api/media-audios',
        'api/get-content-by-slug',
      // ------ html resources ------ //
        'html/index.php',
        'html/article-form/component.php',
        'html/folder-form/component.php',
        'html/album-form/component.php'
    ]);

    $this->register_permission('manipulate-content', 'User can add new, edit, delete contents', [
        'api/index',
        'api/contents-create',
        'api/contents-update',
        'api/contents-delete',
        'api/update_article',
        'api/update_album',
        'api/delete_content',
        'api/folder-delete',
        'api/delete-image',
        'api/upload-file',
        'api/upload_audio',
        'api/images-create',
      // ------ html resources ------ //
        'html/index.php',
        'html/article-form/component.php',
        'html/folder-form/component.php',
        'html/album-form/component.php',
        'html/upload-form/component.php',
        'html/media/upload-audio-form.php',
    ]);

    $this->register_public_access([
        'api/contents-read',
        'api/articles-read',
        'api/ew-page-feeder-articles',
        'api/ew-list-feeder-folders',
        'api/ew-list-feeder-related-contents',
        'api/feeder'
    ]);
  }

  /**
   *
   * @param type $content_id
   * @return json <p>A list of content labels</p>
   */
  private function get_content_labels($content_id, $key = '%') {
    if (preg_match('/\$content\.(\w*)/', $content_id))
      return [];
    if (!$key)
      $key = '%';
    $labels = \ew_contents_labels::where('content_id', '=', $content_id)->where('key', 'LIKE', $key)->get();
    return $labels->toArray();
  }

  public static function contents_labels($_response, $content_id, $key, $value = '%') {
    if (preg_match('/\$content\.(\w*)/', $content_id))
      return [];

    if (!$content_id)
      return [];

    if (!$value)
      $value = '%';

    $rows = \ew_contents_labels::join('ew_contents', 'ew_contents_labels.content_id', '=', 'ew_contents.id')->where(function ($query) use ($content_id) {
      $query->whereIn('content_id', function ($query) use ($content_id) {
        $query->select('content_id')->from('ew_contents_labels')->where('content_id', '=', $content_id);
      })->orWhereIn('content_id', function ($query) use ($content_id) {
        $query->select('content_id')->from('ew_contents_labels')->where('key', '=', 'admin_ContentManagement_document')->where('value', '=', $content_id);
      });
    })->where('key', 'LIKE', $key)->where('value', 'LIKE', $value)->orderBy('value');
    /* return ["collection_size" => $rows->count(),
      "result" => $rows->get(['*',
      \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")])]; */
    $result = $rows->get([
        '*',
        \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")
    ]);

    $_response->properties['total'] = $rows->count();

    return $result->toArray();
  }

  public function ew_page_feeder_articles($_response, $id, $_language = 'en') {
    if (!is_numeric($id)) {
      $id = EWCore::slug_to_id($id, 'ew_contents');
    }

    $articles = $this->contents_labels($_response, $id, 'admin_ContentManagement_language', $_language);
    $article = [];

    if ($articles[0]) {
      $article = $articles[0];
      $result['title'] = $article['title'];
      $result['keywords'] = $article['keywords'];
      $result['description'] = $article['description'];
      $result['content'] = $article['content'];
      $result['content_fields'] = $article['content_fields'];

      $parent_data = (new ContentsRepository)->find_by_id($article['parent_id']);
      if (isset($parent_data->data)) {
        $_response->properties['parent'] = $parent_data->data;
      }
      $_response->properties['type'] = 'object';

      return $result;
    }

    return [];
  }

  public function ew_list_feeder_folders($_response, $id, $token = 0, $page_size, $order_by = null, $_language = 'en') {
    if (!$token)
      $token = 0;
    if (!$page_size)
      $page_size = 30;

    $articles = $this->articles_read($_response, $id, $token, $page_size, $order_by, $_language);

    $result = [];
    if (isset($articles)) {
      foreach ($articles as $article) {
        $article["content_fields"]['@content/date-created'] = [
            'tag' => 'p',
            'content' => \DateTime::createFromFormat('Y-m-d H:i:s', $article['date_created'])->format('Y-m-d')
        ];

        $result[] = [
            'id' => $article['id'],
            'slug' => $article['slug'],
            'html' => $article['content'],
            'content_fields' => $article['content_fields']
        ];
      }
    }

    $folder_data = (new ContentsRepository)->find_by_id($id);
    $parent_data = [];
    if (isset($folder_data->data)) {
      $parent_data = $folder_data->data->toArray();
    }

    $_response->properties['total'] = $articles['total'];
    $_response->properties['page_size'] = $articles['page_size'];
    $_response->properties['parent'] = $parent_data;

    return $result;
  }

  public function ew_list_feeder_related_contents($_response, $content_id, $key, $value = '%') {
    return $this->contents_labels($_response, $content_id, $key, $value);
  }

  public function ew_menu_feeder_languages($id, $token = 0, $page_size) {
    if (!$token)
      $token = 0;
    if (!$page_size)
      $page_size = 30;

    return [
        'title' => [
            'link' => '',
            'icon' => ''
        ]
    ];
  }

  public function ew_menu_feeder_cp_languages($parameters) {
  }

  public function page_uis_handler_documents($url, $url_parts = []) {

    if ($url_parts[0] === 'articles') {
      if (is_string($url_parts[1])) {
        $article = $this->get_content_by_slug($url_parts[1]);
        if (isset($article)) {
          return \webroot\WidgetsManagement::get_path_uis('/articles/' . $article['id']);
        }
      }

      $uis = \webroot\WidgetsManagement::get_path_uis("/folders/{$article['parent_id']}/articles");
      if (isset($uis)) {
        return $uis;
      }

      $uis = \webroot\WidgetsManagement::get_path_uis('/articles/');
      if (isset($uis)) {
        return $uis;
      }

      if (is_numeric($url_parts[1])) {
        $article = $this->read_contents($url_parts[1]);

        if (isset($article)) {
          return \webroot\WidgetsManagement::get_path_uis('/folders/' . $article['parent_id']);
        }
      }

      $uis = \webroot\WidgetsManagement::get_path_uis('/articles/');
      if (isset($uis)) {
        return $uis;
      }
    }

    return null;
  }

  public function folders_read($_response, $parent_id, $start, $page_size, $_language = 'en') {
    $container_id = ew_contents::find($parent_id);
    $up_parent_id = $container_id['parent_id'] ? $container_id['parent_id'] : 0;

    $query = ew_contents::select([
        '*',
        \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")
    ]);

    $query->where('parent_id', '=', $parent_id)->where('type', 'folder');

    $_response->properties['total'] = $query->get()->count();

    if (isset($page_size)) {
      $folders = $query->take($page_size)->skip($start)->get();
    } else {
      $folders = $query->get();
    }

    $rows = [];
    $folders_ar = $folders->toArray();

    foreach ($folders_ar as $i) {
      $i["up_parent_id"] = $up_parent_id;
      $rows[] = $i;
    }


    $_response->properties['start'] = intval($start);
    $_response->properties['page_size'] = intval($page_size);
    $_response->properties['parent'] = isset($container_id) ? $container_id->toArray() : null;

    return $rows;
  }

  public function content_fields($_parts__id, $language) {
    $content = $this->get_content_by_id($_parts__id, $language);

    return \ew\APIResourceHandler::to_api_response($content->data['content_fields']);
  }

  private function get_content_by_id($id, $language = 'en') {
    if (!isset($id)) {
      return \EWCore::log_error(400, 'tr{Content Id is requird}');
    }

    $content = ew_contents::find($id, [
        '*',
        \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")
    ]);

    if (isset($content)) {
      $labels = $this->get_content_labels($id);
      $content->labels = $labels;
      $content->labels = $this->parse_labels($labels, $content);

      return \ew\APIResourceHandler::to_api_response($content->toArray());
    }

    return EWCore::log_error(404, "content not found");
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

  private function get_content_by_slug($slug, $language = 'en') {
    if (!isset($slug))
      return \EWCore::log_error(400, 'tr{Content Id is requird}');
    $content = ew_contents::where('slug', $slug)->get([
        '*',
        \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")
    ]);

    if (isset($content)) {
      //$cf = $this->get_content_fields($content->content);
      //$content->content_fields = json_decode($content->content_fields, true);
      //$content->parsed_content = $cf['html'];

      $labels = $this->get_content_labels($slug);
      $content->labels = $this->parse_labels($labels, $content);

      return $content->toArray();
    }

    return EWCore::log_error(404, "content not found");
  }

  private function delete_files($files = []) {
    foreach ($files as $file) {
      if (file_exists($file)) {
        unlink($file);
      }
    }
  }

  public function delete_image($id) {
    $pdo = \EWCore::get_db_PDO();

    $result = $pdo->prepare("SELECT * FROM ew_contents, ew_images WHERE ew_contents.id = ew_images.content_id AND ew_contents.id = ? LIMIT 1");
    $result->execute([$id]);
    $file = $result->fetchAll(\PDO::FETCH_ASSOC);
    if (isset($file[0])) {
      $path_parts = pathinfo(EW_MEDIA_DIR . '/' . $file[0]["source"]);

      $this->delete_files([
          $path_parts['dirname'] . '/' . $path_parts["basename"],
          $path_parts['dirname'] . '/' . $path_parts["filename"] . '.thumb.' . $path_parts["extension"]
      ]);
    }

    $result = $pdo->prepare("DELETE FROM ew_contents WHERE type = 'image' AND id = ?");
    $result->execute([$id]);

    $result = $pdo->prepare("DELETE FROM ew_images WHERE content_id = ?");
    if ($result->execute([$id])) {
      return \ew\APIResourceHandler::to_api_response([
          "status" => "success",
          "status_code" => 200,
          "message" => "Image has been deleted succesfully"
      ]);
    } else {
      return \EWCore::log_error(400, "Unable to delete the image");
    }
  }

  public function get_title() {
    return "Contents";
  }

  public function get_description() {
    return "Manage the content of your website. Add new artile, Edit or Delete exiting article";
  }

  public function get_media_list($_response, $parent_id, $token = null, $size = null) {
    $db = \EWCore::get_db_connection();

    $path = "/";

    $root = EW_MEDIA_DIR;
    $new_width = 140;
    try {
      $files = [];
      $included = [];
      // Folder
      $files = ew_contents::where('type', 'album')->where('type', 'album')->where('parent_id', $parent_id)->orderBy('title')->get([
          '*',
          \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")
      ])->toArray();
      if (isset($parent_id) && $parent_id !== "0") {
        $included["album"] = ew_contents::where('type', 'album')->where('id', $parent_id)->orderBy('title')->get([
            '*',
            \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")
        ])->toArray()[0];
      }
      // images
      $result = $db->query("SELECT *,ew_contents.id AS content_id, DATE_FORMAT(date_created,'%Y-%m-%d') " . "AS round_date_created " . "FROM ew_contents, ew_images " . "WHERE ew_contents.id = ew_images.content_id " . "AND ew_contents.parent_id = '$parent_id' " . "ORDER BY title") or die('no no no');

      while ($r = $result->fetch_assoc()) {
        //echo "asd";
        $file = $r["source"];
        $file_path = $root . $path . $file;
        $file_info = pathinfo($file_path);

        // create thumb for image if doesn't exist
        $tumbnailURL = 'album-' . $parent_id . $path . 'thumbnails/' . $file_info["filename"] . ".thumb." . $file_info["extension"];

        if (!file_exists($file_path)) {
          $files[] = [
              "id" => $r["content_id"],
              "title" => $r["title"],
            //"parentId" => $container_id,
              "type" => $this->file_types[$file_info["extension"]] ? $this->file_types[$file_info["extension"]] : "unknown",
              "size" => 0,
              "ext" => "unknown",
              url => 'media' . $path . $file,
              'absURL' => EW_ROOT_URL . "public/rm/media/$file",
              'originalUrl' => EW_ROOT_URL . "public/rm/media/$file",
              "filename" => $file_info["filename"],
              "fileExtension" => $file_info["extension"],
              "thumbURL" => "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAYAAADDPmHLAAAOdElEQVR4Xu2dddR1RRWHHywURQxMltiCgd2Jgd3d3ZiI3aAuA7u7W+zCRMVu7EZZFiJid6yHby5cX77vPXvmnLn31F7r/evdZ2LP787s2TXbMdOkJbDdpGc/T54ZABMHwQyAGQATl8DEpz/vADMAJi6BiU9/3gFmAExcAhOf/rwDzACYjAROBZweOC2wE7A9cJI0+38Afwd+DxwJ/AY4egqSGeMOcAbgUsCFgAsC5wHOBpwyc0EFw2HA94FvAF8HPgcckdlOr9nHAIBTA1cHrglcEThHZYn/EPgk8EHgw0PfKYYKALfxmwI3B64EnLDyom+r+X8BBwNvAQ4EjlrTOIq7HRoArgrcHbjh0vldPPmOP1SHeAfwEuDjHbddrbkhAEBF7TbAPsAFqkmi24YPBZ4BvAH4Z7dNd9tanwFwIuBOwKOAXbud9spaU4ncH3gN4HHRO+orAK4PHACcu3cSKxvQ99IO9v6yz+t91TcAnBN4QdLq6816fS0LgL3T9XJ9o1jquS8AOEH6hewHnKwXkqk3iD+nY+3ZwH/rdRNruQ8AOCvwWuAKsSGPhutjwO2Bn69zRusGwA2AVyfTbC05eD37Qfo7HPgl8DvgT0lDVwbeNHYAdk7mYkGpQWm3ytdN7Qa3BT5Qa/JN7a4LAG75ascPh87jEn8NfCRZ6z4PfLvFVezEwO7ApYHLA3sBZ2oSaub//wN49Pm38iNhHQDwjH89cKNMQW3G/lPgTcDbgC9XFuSFgZsAtwTO1eEc3gzcITmlOmx286ZWDYDTAGrCOmvakvdqLW8vTObYlf96kt6iZfIWgLtFWzoEuN4q/QurBIBeOp0ne7SU0t+SufXpwM9attXV57sA909XPHWJNvS1dA3WJV2dVgUAF1+niedpKXlWvhx4/Lo1500mYLyBlst7AVoyS0m9Zc8Ul1DaRui7VQDAbd/Fb/PL/wxw7+STD01szUznBZ4PXLnFOL4KXKX2cVAbACp8esZKz3yNJg9N1sF1nPEt1u+YT++SnEK5wSiLftUJrlZTMawJAK96bwVuXCjFryRN2zv8kMloJG89ly2chLebW9e62dQEwBOBRxROWg3/oikUq7CJXn2mPqBzS0WxhB6T7CYl3276TS0AaOHzitam/V+kM9SYvLHQnYEXFyiIKsDXSWFoncqizQJtayCaUQ2gNPK2LY0RBMYvvh04eaZwNBsb5Nqp76BrAHjuq/F36dgZIwgulwxiucrhR5M5ujOFuGsA7As8LRPZEfaxguCggp3gfsBzI0KL8HQJAIM5jJ+v5c8fIwg8Dt6XqRPoxTwfoGezNXUJANHshGrSGEGgreBlmUJ7N6Ci3Zq6AoAxfO/KHI1XvRJz6RhB4JZ+n0z5+WPTt9KKugCAi6jtOieAUyPPHdO15swFMxgbCPQkmm1k3EGUvpVuBV4Ri6kLANwteeeig9C8e5EUoWPenqbiGQRbIpD0BO4YFSRwO+B1GfzHY20LAEOpNNXmxO271ekoWdAMguNkYWyBhqIomaeo46k456AtAEzceEV0tIBePUOrNt5jZxBsEaLr4Y5ovmOU9BO8Mcq8ka8tALz2RdO1PKu072sl3BrNINgiFeXpURBNeFWfutg6AGCipsGXUfKqo76wGc0g2CIdk2MMKomSafGfijIv87XZAQxiND07QoZxGUAZsWPPIIAzAj/OMKqZV2GOQTaVAsD8fK9iixIrTR0/J9MVOoNgS3bxA5sEm/7/1xSublWTLCoFwD2AFwV7UkPVTJwbwDl1EJwl7QJRY5l2FZNssqgUAHqljFeLkLH6N4swboVn6iAwGsiQ8wiZXXTtCGNbHcCaPIYsR7VUgdKmYsaUQaDs/LFFyEIUHs1/jDAveEp2ABEpMiNkxs7ZO4hnmyoIXB+VQeMKI2S21TsjjG0A8Mpkx4/08xTgYRHGAM9UQfBU4MEB+ciiFfGeQd5j2Ep2gB9llGK7BPClnAE18E4RBIbUW58wQt9JsQIR3iIAmOHzq2DrZumaSdtZ+FLqd2ogMMxOWZq63kTKWr5wubrcHSDH728svLnvNWhqIMi5DXgTCNcbyAXAo1Mee2RRtRVYM68WTQkEelCjcYDqXOpeIcoFgJk+VuiMkHn023L8RL6P8EwFBDp7orpU1s6bCwAX1Nj0JrIsyyna+KmbOlj6/xRAcNJU0iZie/kicMmo/HIBoK05Esv+zZbZwNHxL/imAILvpppFTbLRSGeaeohyAGC9fYsrRci0sNKk0Ej7W+MZOwhU7KyIHiGzjv4SYcwBgAK24mWEcr1/kTYjPGMGgaVwokYeLYdaYRspBwCXSSFdjY2m6l9PjjBW4BkrCKyMYpZwhC6eimU18uYAwO0ner8UqTnBjY0DzWQYIwjuC7izRshoLQtRNlIOAMxEiToaNAB5HVknjQ0EppZbIylCYWNQDgD06fsyRoT0GEZ5I+2V8owJBL6ZEM0BCHsFawHAIorGDPaBxgKCtQMg5wgwQNFAxb7QGECQk4NR5QgYkhK4NeANHQTWBbDEfISqKIE510CLJVokqm80ZBA8FnhcUKD6DkwYaaQcHcDs32jBpnUZghonnB6SHGJCqtdqcwcjVMUQlGMKtlaAT7v1lYa4E1hk+1pBgVYxBdt31BlkzmDEaxicTxW2oYHATGDzK5qomjPIjk1a9E3eJvIxZlFYnLbc1EFH/x8KCKy7ZLh3xB38hZzSvDk6gDLXuBNN8nAHcCfoOw0BBPr3ff0kQlUDQnJCwlRYXhoZcQ94+g4CS8w+Kygni2sbSh6i3B0gJyjU1zJ9AmUo1GcQmF7nMzURUlH0ZfMQ5QLASBNDlCPk61wltX8ibdfi6SMIPPdV7EzJayLDwk0PiwbuFCWGWBMo+liSxaBUHIdEfQOBJXWixR+s1nb+HGHn7gC2rUtS12SEngCoNwyN+gSCZwIPCArQqCFfVglTCQBy3MLeXXPqB4YHvgLGPoDA7d+6CtGjVIedVUTDVAIALYKeSdHCBcX1a8KzqMe4bhDo1bOWcIS0vXj+W0s4TCUAsHFLlPqWTYQMYrCg4VBpnSB4b3ooIiI7gXLdCOMyTykAcqqDWrhA54Q1hYZK6wCBR6e5ACaHRqgoBqMUAD4F54JuHxlZekPgIUHevrKtGgQa0e4aFIY5AGZi/yHIfyxbKQBswOqUhn5FyPrA1sI9IsLcY55VgcCqKuZgRJ+jtTiURaKyqQ0AfBQxFHqcRmV2q1EtQ6dVgEC9yRjAKGkr+HSUeZmvDQBsJ5osKq+eQR1EVrEYOtUEgRVBPptRvcWsYSuxFFFbAGjrf1VGz+4YxquNgWqAwHu/2b1aUKPUKgK7LQA8owwTi1axclI+kZJTYTwqiHXwdQ0CFeVwcYcke98P+nfp5NsCwH5zMlbkV1M1qOSw0kH37LuuQGCVcLfz6M1KMbQqFW8DXQBAi6D1AHbLWBjfDbAmft8jhqJTagsCXwVV8ctx5Byajoq1PxmjkHJMlguhGuMedXJEF2KdfG1A4BYeCfdanl/bCqzHtNXFDrAYlKbI3Fq1Hh8WnhwLtQFBjgx8ejYaILJpu10CQEXQoyDnTVzNxNqvP5Qz+57z1gaBOpSKX+TthUZRdQkAO3NL13+dQ1oJr1FqyMjpaIW8NUGgv1+/fyfUNQBsz2dkoqXkF5MQ1R4fRdasTiTRfSM1QGCsn3LqrPpq1wBQjLsAaqg6jHLIncC89tavYeZ0Wpm3SxAYg6ElNVqqNzS1GgCwY1GqLzu3fXUCy8uMxVCkLLoAgbcEj8no2wGhxZcpd4HCDQM52awb230esA8gIMZAVk3VyJN71VvMPav8a47AagLAtnUZR5882ThuS6TrEfPBhCGTFj6LPecYeZbnWzW/oiYAnIRmTZVC3ZUlZD7cvinDqDPFp2QgBd/4a39QKq6dY95d7urgtPUb71eFagPAQRtEaj6+22ApfQLYG/DF7CGQLl2vajlevY3z8sjQc5od5ZMjoFUAwPGcDhDNGjBKSUXI8vP7da0Jlw5oK98ZybN/ctK0ka1JtXvmPPxQOoc2g8ztUxAc1PJXYZ8+kuibhRqcDs8dRCV+Azh15RofEQ3j2tZQ/OVbj+m3lcb6f82uEgCL4+A9LXSC5cHrSTww7QoeMavWETzjvZppmTMhMxq9u9m6ukua3FF1218ewKoBYN8qREYRRQNKIz8EYwt8zEJAGFHTykW6SYcuusWydMT4bnI0YycyB7V9o4BXevVdBwAUhv1aScyqV138cpYFfGQymBySYutUHH28uoR8qEHrm4t+hWTijmTp5vSlbvPIzEignPY35V0XABaDcus0ECLXbJwjAAVsjuJPUhSSoemCRNOzL5tIPoK9Y0rB9lftu71a8PRwlhpvImPUvHurGha+SOeLX2KUtxafvgPj2scSLBqVk44dY/mj9Rai7WbxrXsHWAzWcfgy1pPSW0NZkxgYswqepl1vMqtWXI8nqr4AYDGwXdPzaJaiGSMZyWNyTCfBHF0IqG8AWMxpr3TPL7WfdyGbLtvQPW6wTJtX1Lscz7Ft9RUADtDbgc4gvYqRAolVBNSyUXMmvOlYOr/W1bTVEPsMgMXEDDu3KokvaLexrbcSVObHWvMOAKzuVZy0kdlnEfsQALA8Me/i1ibw9VKrZ/aJTNHWGGVa92BC24YGgMWC75SKURtrYKWStvb3UiDppjWEzS3eAtkrM+GWDnjjd0MFwPI8NOBoQ9CBYj2i3StGOnltM7tZ97T3eJNds2rydLVwXbUzBgBslIWFkqytqwl3jyWLnt7IHNJKp/XQuohq8f5ZiPmonEb6zjtGAGxL5jukuISdAY8QnVKLSB1Nwv4dndywLn7o6dW+L3DT+KYEgCZZTPL/MwAmuezHTXoGwAyAiUtg4tOfd4AZABOXwMSnP+8AMwAmLoGJT3/eAWYATFwCE5/+/wD+P8afztOu5gAAAABJRU5ErkJggg==",
              "path" => "data:image/png;base64,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"
          ];
          continue;
        }

        list($width, $height) = getimagesize($file_path);

        if (!file_exists($root . $path . $tumbnailURL) && $width > 200) {
          $this->create_image_thumb($file_path, 200);
          //$tumbnailURL = 'album-' . $parent_id . $path . $file_info["filename"] . ".thumb." . $file_info["extension"];
        } else if ($width <= 200) {
          $tumbnailURL = $path . $file;
        }

        $files[] = [
            "id" => $r["content_id"],
            title => $r["title"],
            "parentId" => $r['parent_id'],
            type => $this->file_types[$file_info["extension"]] ? $this->file_types[$file_info["extension"]] : "unknown",
            size => round(filesize($file_path) / 1024),
            ext => $file_info["extension"],
            url => 'media' . $path . $file,
            absURL => EW_ROOT_URL . "public/rm/media/$file",
            originalUrl => EW_ROOT_URL . "public/rm/media/$file",
            filename => $file_info["filename"],
            fileExtension => $file_info["extension"],
            thumbURL => EW_ROOT_URL . 'public/rm/media/' . $tumbnailURL,
            path => $file_path
        ];
      }
    } catch (Exception $e) {
      echo $e->getMessage();
    }

    $_response->properties['included'] = $included;

    return $files;
  }

  public function create_resize_image($image_path, $width = null, $height = null, $same_path = true) {
    if (!$width && !$height)
      return;
    $src_image = imagecreatefromstring(file_get_contents($image_path));
    $path_parts = pathinfo($image_path);
    $type = $path_parts['extension'];
    //$foo->
    imagealphablending($src_image, true);
    if (!$height || $height == 0)
      $height = floor(imagesy($src_image) * ($width / imagesx($src_image)));
    if (!$width || $width == 0)
      $width = floor(imagesx($src_image) * ($height / imagesy($src_image)));
    $dst = imagecreatetruecolor($width, $height);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    imagecopyresampled($dst, $src_image, 0, 0, 0, 0, $width, $height, imagesx($src_image), imagesy($src_image));
    if (!$same_path) {
      $path_parts['dirname'] = EW_MEDIA_DIR;
    }
    switch ($type) {
      case 'bmp':
        imagewbmp($dst, EW_MEDIA_DIR . '/' . $path_parts['filename'] . ".{$width},{$height}.bmp");
        break;
      case 'gif':
        imagegif($dst, EW_MEDIA_DIR . '/' . $path_parts['filename'] . ".{$width},{$height}.gif");
        break;
      case 'jpg':
        imagejpeg($dst, EW_MEDIA_DIR . '/' . $path_parts['filename'] . ".{$width},{$height}.jpg", 100);
        break;
      case 'jpeg':
        imagejpeg($dst, EW_MEDIA_DIR . '/' . $path_parts['filename'] . ".{$width},{$height}.jpg", 100);
        break;
      case 'png':
        imagepng($dst, EW_MEDIA_DIR . '/' . $path_parts['filename'] . ".{$width},{$height}.png");
        break;
    }
  }

  public function create_image_thumb($image_path, $width = null, $height = null) {
    if (!$width && !$height) {
      return;
    }

    $src_image = imagecreatefromstring(file_get_contents($image_path));
    $path_parts = pathinfo($image_path);
    $type = $path_parts["extension"];
    //$foo->
    imagealphablending($src_image, true);
    if (!$height)
      $height = floor(imagesy($src_image) * ($width / imagesx($src_image)));
    if (!$width)
      $width = floor(imagesx($src_image) * ($height / imagesy($src_image)));
    $dst = imagecreatetruecolor($width, $height);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    imagecopyresampled($dst, $src_image, 0, 0, 0, 0, $width, $height, imagesx($src_image), imagesy($src_image));

    if (!is_dir($path_parts['dirname'] . '/thumbnails/')) {
      if (!mkdir($path_parts['dirname'] . '/thumbnails/', 0777, true)) {
        return \EWCore::log_error(500, 'failed to create thumbnails folder');
      }
    }
    // save thumbnail into a file
    //imagepng($dst, $path_parts['dirname'] . '/' . $path_parts['filename'] . '.thumb.png', 9, PNG_ALL_FILTERS);
    switch ($type) {
      case 'bmp':
        imagewbmp($dst, $path_parts['dirname'] . '/thumbnails/' . $path_parts['filename'] . ".thumb.bmp");
        break;
      case 'gif':
        imagegif($dst, $path_parts['dirname'] . '/thumbnails/' . $path_parts['filename'] . ".thumb.gif");
        break;
      case 'jpg':
        imagejpeg($dst, $path_parts['dirname'] . '/thumbnails/' . $path_parts['filename'] . ".thumb.jpg", 90);
        break;
      case 'jpeg':
        imagejpeg($dst, $path_parts['dirname'] . '/thumbnails/' . $path_parts['filename'] . ".thumb.jpg", 90);
        break;
      case 'png':
        imagepng($dst, $path_parts['dirname'] . '/thumbnails/' . $path_parts['filename'] . ".thumb.png", 9, PNG_ALL_FILTERS);
        break;
    }
  }

  public function images_create($_response, $_input) {
    ini_set('memory_limit', '100M');
    ini_set('post_max_size', '64M');
    ini_set('upload_max_filesize', '64M');

    $uploaded_file = [];

    if (isset($_FILES['images'])) {
      $uploaded_file = $_FILES['images'];
    }

    $root = EW_MEDIA_DIR . '/album-' . $_input->parent_id;
    $succeed = 0;
    $error = 0;
    $files = [];
    foreach ($uploaded_file as $k => $l) {
      foreach ($l as $i => $v) {
        if (!array_key_exists($i, $files))
          $files[$i] = [];
        $files[$i][$k] = $v;
      }
    }

    $contents_repository = new ContentsRepository;
    $images_repository = new ImagesRepository();

    foreach ($files as $file) {
      $uploader = new \upload($file);
      if ($uploader->uploaded) {

        // save uploaded image with no changes
        $uploader->Process($root);
        if ($uploader->processed) {
          $content_details = new \stdClass();
          $content_details->type = 'image';
          $content_details->title = $uploader->file_dst_name_body;
          $content_details->parent_id = $_input->parent_id;


          $result = $contents_repository->create($content_details);

          if ($result->data->id) {
            $image_details = new \stdClass();
            $image_details->content_id = $result->data->id;
            $image_details->source = 'album-' . $result->data->parent_id . '/' . $uploader->file_dst_name;
            $image_details->alt_text = '';

            $image = $images_repository->create($image_details);

            $this->create_image_thumb($uploader->file_dst_pathname, 200);

            if (!isset($image->error)) {
              $succeed++;
            }
          } else {
            $this->delete_content($result->data->id);
            $error++;
          }
        } else {
          $error++;
        }
      } else {
        $error += 1;
      }
    }

    $_response->properties['message'] = "Uploaded: " . $succeed . " Error: " . $error . ' ' . $uploader->error;

    return [];
  }

  /**
   *
   * @param array $form_config [optional] <p>An array that contains content form configurations.<br/>
   * the keys are: <b>title</b>, <b>saveActivity</b>, <b>updateActivity</b>, <b>data</b>
   * </p>
   * @return string
   */
  public static function create_content_form($form_config = null) {
    return \EWCore::load_file('admin/html/content-management/content-form/component.php', $form_config);
  }

  public function contents_create($_response, $_input) {
    $result = (new ContentsRepository())->create($_input);
    $_response->set_result($result);

    return $_response;
  }

  /**
   * @param $_input
   * @param $_response \ew\APIResponse
   * @param $_parts__id
   * @return mixed
   */
  public function contents_read($_input, $_response, $_parts__id) {
    $_input->id = $_parts__id;
    $result = (new ContentsRepository())->read($_input);
    $_response->set_result($result);

    return $_response;
  }

  public function contents_update($_input, $_response) {
    $result = (new ContentsRepository())->update($_input);
    $_response->set_result($result);

    return $_response;
  }

  public function contents_delete($_input, $_response) {
    $result = (new ContentsRepository())->delete($_input);
    $_response->set_result($result);

    return $_response;
  }

  public function folder_delete($_input, $_response) {
    $result = (new ContentsRepository())->delete_folder($_input);
    $_response->set_result($result);

    return $_response;
  }

  /**
   * @param $_response \ew\APIResponse
   * @param $_input
   * @param string $order_by
   * @param $_language
   * @param int $token
   * @param int $page_size
   * @return \ew\APIResponse
   */
  public function feeder($_response, $_input, $order_by = 'ASC', $_language, $token = 0, $page_size = 100) {
    if (!is_numeric($_input->_resource_id)) {
      $_input->id = EWCore::slug_to_id($_input->_resource_id, 'ew_contents');
    }

    if (!$_input->id) {
      $not_found = new Result();
      $not_found->error = 400;
      $not_found->message = "`id` not found";
      $_response->set_result($not_found);

      return $_response;
    }


    $articles = $this->articles_read($_response, $_input->id, $token, $page_size, $order_by, $_language);

    $result = new \ew\Result;
    $result->data = new \Illuminate\Database\Eloquent\Collection;

    if (isset($articles)) {
      foreach ($articles as $article) {
        $article['content_fields']['@content/date-created'] = [
            'tag' => 'p',
            'content' => \DateTime::createFromFormat('Y-m-d H:i:s', $article['date_created'])->format('Y-m-d')
        ];

        $result->data->add([
            'id' => $article['id'],
            'html' => $article['content'],
            'content_fields' => $article['content_fields']
        ]);
      }
    }

    $folder_data = (new ContentsRepository)->find_by_id($_input->id);
    $parent_data = [];
    if (isset($folder_data->data)) {
      $parent_data = $folder_data->data;
    }

    $result->parent = $parent_data;
    $_response->set_result($result);

    return $_response;
  }


  public function articles_read($_response, $parent_id = null, $start = 0, $page_size = 100, $order_by = null, $language) {
    if (is_null($start)) {
      $start = 0;
    }

    if (is_null($page_size)) {
      $page_size = 100;
    }

    // if there is no parent_id then select all the articles
    if (is_null($parent_id)) {
      $articles = ew_contents::where('type', 'article')->orderBy('title')->get([
          '*',
          'ew_contents.id',
          \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")
      ]);

      $_response->properties['total'] = $articles->count();
      $_response->set_data($articles->toArray());

      return $_response;
    } else {
      $parent_data = ew_contents::find($parent_id);
      $up_parent_id = isset($parent_data['parent_id']) ? $parent_data['parent_id'] : 0;

      $query = ew_contents::select([
          '*',
          'ew_contents.id',
          \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")
      ]);

      $query->where('parent_id', '=', $parent_id)->where('type', 'article')->join('ew_contents_labels', 'ew_contents.id', '=', 'ew_contents_labels.content_id');

      if (isset($language) && !is_null($language)) {
        $query->where('ew_contents_labels.key', 'admin_ContentManagement_language')->where('ew_contents_labels.value', $language);
      } else {
        $query->where('ew_contents_labels.key', 'admin_ContentManagement_document')->groupBy('ew_contents_labels.value');
      }

      $_response->properties['total'] = $query->get()->count();

      if (isset($order_by)) {
        $articles = $query->take($page_size)->skip($start)->orderBy('date_modified', $order_by)->get();
      } else {
        $articles = $query->take($page_size)->skip($start)->get();
      }

      $data = array_map(function ($article) use ($up_parent_id) {
        $article['up_parent_id'] = $up_parent_id;
        return $article;
      }, $articles->toArray());

      $_response->properties['start'] = intval($start);
      $_response->properties['page_size'] = intval($page_size);
      $_response->properties['parent'] = isset($parent_data) ? $parent_data->toArray() : null;
      $_response->set_data($data);

      return $_response;
    }

    return \EWCore::log_error(400, 'tr{Something went wrong}');
  }
}
