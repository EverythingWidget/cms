<?php

namespace admin;

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
class ContentManagement extends \Section
{

   public function __construct($app)
   {
      parent::__construct($app);
      require_once('ew_contents.php');
      require_once('ew_contents_labels.php');
   }

   private $file_types = array("jpeg" => "image",
       "jpg" => "image",
       "png" => "image",
       "gif" => "image",
       "txt" => "text",
       "mp3" => "sound",
       "mp4" => "video");
   private $images_resources = array("/is/htdocs/wp1067381_3GN1OJU4CE/www/culturenights/app/webroot/img/logos/");

   public function init_plugin()
   {
      ob_start();
      include 'link-chooser-document.php';
      $lcd = ob_get_clean();
      EWCore::register_form("ew-link-chooser-form-default", "contents-list", ["title" => "Contents", "content" => $lcd]);
      // $this->file_types 
      EWCore::register_resource("images", array($this, "image_loader"));
      $this->register_permission("see-content", "User can see the contents", array('index.php', 'index', "get_content",
          "get_category",
          "get_article",
          "get_album",
          "get_categories_list",
          "get_articles_list",
          "get_medias_list",
          "article-form.php_see",
          "category-form.php_see",
          "album-form.php_see"));

      $this->register_permission("manipulate-content", "User can add new, edit, delete contents", array('index.php', 'index', "add_content",
          "add_category",
          "add_article",
          "add_album",
          "upload-form.php",
          "update_content",
          "update_category",
          "update_article",
          "update_album",
          "delete_content",
          "delete_content_by_id",
          "delete_category",
          "delete_article",
          "delete_album",
          "article-form.php:tr{New Article}",
          "category-form.php:tr{New Folder}",
          "album-form.php:tr{New Album}"));
      $this->register_content_label("document", ["title" => "Document", "description" => "Attach this content to other content", "type" => "data_url", "value" => "app-admin/ContentManagement/get_articles_llist"]);
      $this->register_content_label("language", ["title" => "Language", "description" => "Language of the content"]);
      $this->register_widget_feeder("page", "article");
      $this->register_widget_feeder("list", "folder");
      $this->register_widget_feeder("menu", "languages");
   }

   public function ew_label_document($key, $value, $data, $form_id)
   {
      ob_start();
      include 'label_document.php';
      $html = ob_get_clean();
      return (["html" => $html]);
   }

   public function ew_label_language($key, $value, $data, $form_id)
   {
      if (!$value)
         $value = "en";
      ob_start();
      include 'label_language.php';
      $html = ob_get_clean();
      return (["html" => $html]);
   }

   public function image_loader($file)
   {
      preg_match('/(.*)\.?(\d*)?,?(\d*)?\.([^\.]\w*)/', $file, $match);

      $file = EW_MEDIA_DIR . "/" . $file;
      //Check if the requested url has been matched
      if (count($match) > 0)
      {
         $real_file_name = EW_MEDIA_DIR . "/" . $match[1] . "." . $match[4];

         // Execute when size has been set and resized file does not exist
         if (!file_exists($file) && $match[2])
         {
            // If file is in media dir
            if (file_exists($real_file_name))
            {
               //echo count($match);
               $this->create_resized_image($real_file_name, $match[2], $match[3]);
            }
            // If file is another resource dir
            else if (file_exists($this->images_resources[0] . $match[1] . "." . $match[4]))
            {
               //echo $this->images_resources[0] . $match[1] . "." . $match[4];
               $this->create_resized_image($this->images_resources[0] . $match[1] . "." . $match[4], $match[2], $match[3], false);
            }
         }

         //$file = EW_MEDIA_DIR . "/" . $match[1] . "." . $match[4];
      }
      // If the resized file still does not exist, then the no-image will be sent
      if (!file_exists($file))
      {
         //echo urldecode($file);
         //echo $this->images_resources[0] . $match[1] . "." . $match[4];
         if (file_exists($this->images_resources[0] . $match[1] . "." . $match[4]))
         {
            //echo "h3";
            $file = $this->images_resources[0] . $match[1] . "." . $match[4];
         }
         else
         {
            $file = EW_APPS_DIR . "/admin/ContentManagement/no-image.png";
            /* $apps_dir = opendir("/is/htdocs/wp1067381_3GN1OJU4CE/www/culturenights/app/webroot/img/logos/");
              while ($app_root = readdir($apps_dir))
              {
              echo $app_root . "<br>";
              } */

            //echo "404 NOT FOUND ".$file;
            //return;
         }
      }
      //echo headers_sent();
      $path_parts = pathinfo($file);
      $type = 'image/' . $path_parts["extension"];

      $lastModified = filemtime($file);
//get a unique hash of this file (etag)
      $etagFile = md5_file($file);
//get the HTTP_IF_MODIFIED_SINCE header if set
      $ifModifiedSince = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
//get the HTTP_IF_NONE_MATCH header if set (etag: unique file hash)
      $etagHeader = (isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);
      if (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastModified && $etagHeader == $etagFile)
      {
         header("HTTP/1.1 304 Not Modified");
      }
      //set last-modified header
      header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastModified) . " GMT");
      header("Expires: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
      //set etag-header
      header("Etag: $etagFile");
      //make sure caching is turned on
      header('Cache-Control: public');
      header('Content-Type: ' . $type);
      header('Content-Length: ' . filesize($file));
      header('Content-transfer-encoding: binary');
      header('Connection: close');
      //header("Keep-Alive: timeout=5, max=98");
      //echo $path_parts["filename"];
      ob_clean();
      flush();
      readfile($file);
      exit;
   }

   public function update_label($content_id, $key, $value)
   {
      if (!$content_id)
         EWCore::log_error(400, 'tr{Content Id is requierd}');
      $content = ew_contents::find($content_id)->toArray();

      $value = preg_replace_callback('/\$content\.(\w*)/', function($m) use ($content) {
         return $content[$m[1]];
      }, $value);
      $label = \ew_contents_labels::firstOrNew(['content_id' => $content_id, 'key' => $key]);

      if ($value)
      {
         $label->value = $value;
         $label->save();
      }
      else if ($label->exists)
      {
         $label->delete();
      }

      return json_encode(["status" => "success", "id" => $label->id]);
   }

   /**
    * 
    * @param type $content_id
    * @return json <p>A list of content labels</p>
    */
   public static function get_content_labels($content_id, $key = '%')
   {
      if (preg_match('/\$content\.(\w*)/', $content_id))
         return [];
      if (!$key)
         $key = '%';
      $labels = \ew_contents_labels::where('content_id', '=', $content_id)->where('key', 'LIKE', $key)->get();
      return $labels;
   }

   public static function get_content_with_label($content_id, $key, $value = '%')
   {
      if (preg_match('/\$content\.(\w*)/', $content_id))
         return [];
      if (!$content_id)
         return null;
      if (!$value)
         $value = '%';

      $rows = \Illuminate\Database\Capsule\Manager::table('ew_contents_labels')->join('ew_contents', 'ew_contents_labels.content_id', '=', 'ew_contents.id')
                      ->where(function($query) use ($content_id) {
                         $query->whereIn('content_id', function($query) use ($content_id) {
                            $query->select('content_id')
                            ->from('ew_contents_labels')
                            ->where('content_id', '=', $content_id);
                         })->orWhereIn('content_id', function($query) use ($content_id) {
                            $query->select('content_id')
                            ->from('ew_contents_labels')
                            ->where('key', '=', 'admin_ContentManagement_document')
                            ->where('value', '=', $content_id);
                         });
                      })
                      ->where('key', 'LIKE', $key)
                      ->where('value', 'LIKE', $value)->orderBy('value');

      return ["totalRows" => $rows->count(), "result" => $rows->get(['*', \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")])];
   }

   /**
    * 
    * @param type $type
    * @param type $title
    * @param type $parent_id
    * @param type $keywords
    * @param type $description 
    * @param type $content
    * @param type $featured_image
    * @param string $date_created
    * @param string $date_modified
    * @return JSON json object which hold the result, if the opration is succesful get new row id with "id"
    */
   public function add_content($type, $title, $parent_id, $keywords, $description, $html_content, $featured_image, $labels, $date_created, $date_modified)
   {
      $validator = \SimpleValidator\Validator::validate(compact(['title', 'type', 'parent_id']), ew_contents::$rules);
      if (!$validator->isSuccess())
         return EWCore::log_error("400", "tr{Content has not been added}", $validator->getErrors());


      $content = new ew_contents;
      $content->author_id = $_SESSION['EW.USER_ID'];
      $content->type = $type;
      $content->title = $title;
      $content->keywords = $keywords;
      $content->description = $description;
      $content->parent_id = $parent_id;
      $content->content = $html_content;
      $content->featured_image = $featured_image;
      $content->date_created = date('Y-m-d H:i:s');
      $content->date_modified = date('Y-m-d H:i:s');
      $content->save();

      if ($content->id)
      {
         $res = ["status" => "success", "data" => $content->toArray()];
         $id = $content->id;
         $labels = json_decode($labels, true);
         foreach ($labels as $key => $value)
         {
            $this->update_label($id, $key, $value);
         }
      }
      return json_encode($res);
   }

   public function update_content($id, $title, $type, $parent_id, $keywords, $description, $html_content, $featured_image, $labels)
   {
      $validator = \SimpleValidator\Validator::validate(compact(['title', 'type', 'parent_id']), ew_contents::$rules);
      if (!$validator->isSuccess())
         return EWCore::log_error("400", "tr{Content has not been added}", $validator->getErrors());

      $content = ew_contents::find($id);
      $content->author_id = $_SESSION['EW.USER_ID'];
      $content->type = $type;
      $content->title = $title;
      $content->keywords = $keywords;
      $content->description = $description;
      $content->parent_id = $parent_id;
      $content->content = $html_content;
      $content->featured_image = $featured_image;
      $content->date_modified = date('Y-m-d H:i:s');
      $content->save();

      if ($content->id)
      {
         $res = ["status" => "success", "data" => $content->toArray()];
         $id = $content->id;
         $labels = json_decode($labels, true);
         foreach ($labels as $key => $value)
         {
            $this->update_label($id, $key, $value);
         }
         return json_encode([status => "success", message => "tr{The content has been updated successfully}", "data" => $content->toArray()]);
      }
      return EWCore::log_error("400", "Something went wrong, content has not been updated");
   }

   public function add_article($title, $parent_id, $keywords, $description, $labels)
   {
      if (!$parent_id)
         $parent_id = 0;

      $htmlContent = $_REQUEST['content'];

      if (!$title)
      {
         \EWCore::log_error(400, "tr{Title is requierd}");
      }

      $result = $this->add_content("article", $title, $parent_id, $keywords, $description, $htmlContent, "", $labels);
      $result = json_decode($result, true);

      if ($result["data"]["id"])
      {
         return json_encode(["status" => "success", "title" => $title, "message" => "tr{The new article has been added succesfully}", "data" => ["id" => $result["data"]["id"], "type" => "article"]]);
         // End of plugins actions call
      }
      return $result;
//      return \EWCore::log_error(400, "tr{Something went wrong, content has not been added}");
   }

   public function ew_page_feeder_article($id, $language)
   {

      $articles = $this->get_content_with_label($id, "admin_ContentManagement_language", $language);
      $article = [];
      //echo count($articles['result']);
      if ($articles)
      {
         $article = $articles["result"][0];
         $result["html"] = "WIDGET_DATA_MODEL";
         $result["title"] = $article['title'];
         $result["content"] = $article['content'];
         return json_encode($result);
      }

      return NULL;
   }

   public function ew_list_feeder_folder($id, $token = 0, $size)
   {
      if (!$token)
         $token = 0;
      if (!$size)
         $size = 30;
      $articles = $this->get_articles_list($id, $token, $size);
      $result["num_rows"] = $articles["totalRows"];
      foreach ($articles["result"] as $article)
      {
         $result["items"][] = ["html" => "{$article["content"]}"];
      }
      //print_r($language);
      return json_encode($result);
   }

   public function ew_menu_feeder_languages($id, $token = 0, $size)
   {
      if (!$token)
         $token = 0;
      if (!$size)
         $size = 30;
      /* $articles = $this->get_articles_list($id, $token, $size);
        $result["num_rows"] = $articles["totalRows"];
        foreach ($articles["result"] as $article)
        {
        $result["items"][] = ["html" => "{$article["content"]}"];
        }
        //print_r($language);
        return json_encode($result); */
   }

   public function get_article($articleId)
   {
      //echo "$articleId";
      if (!$articleId)
      {
         return EWCore::log_error(400, 'tr{Article Id is requierd}');
      }
      $article = ew_contents::find($articleId, ['*', \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")])->toArray();
      $article["labels"] = ContentManagement::get_content_labels($articleId);
      return $article;
   }

   public function update_article($id, $title, $parent_id, $keywords = null, $description = null, $content = null, $labels = null)
   {
      $v = new \Valitron\Validator($this->get_current_command_args());


      $v->rule('required', ["title", "parent_id"])->message(' {field} is required');
      $v->rule('integer', "parent_id")->message(' {field} should be integer');
      $v->labels(array(
          "title" => 'tr{Title}',
          "parent_id" => 'Folder ID'
      ));
      if (!$v->validate())
         return EWCore::log_error("400", "tr{New article has not been added}", $v->errors());

      $result = json_decode($this->update_content($id, $title, 'article', $parent_id, $keywords, $description, $content, null, $labels), TRUE);

      if ($result["status"] === "success")
      {
         $result["message"] = "tr{Article has been updated successfully}";
         return json_encode($result);
      }
      else
      {
         return EWCore::log_error("400", "New article has not been added");
      }
   }

   public function get_categories_list($parent_id, $token, $size)
   {
      $container_id = ew_contents::find($parent_id);
      $container_id = $container_id['parent_id'];
      $folders = ew_contents::all(['*', \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")])->where('parent_id', '=', $parent_id)->where('type', 'folder');
      $rows = array();
      $folders_ar = $folders->toArray();
      foreach ($folders_ar as $i)
      {
         $i["parent_id"] = $container_id;
         $rows[] = $i;
      }
      $out = array("totalRows" => $folders->count(), "result" => $rows);
      return json_encode($out);
   }

   public function get_articles_list($parent_id = null, $token, $size)
   {
      if (!isset($token))
      {
         $token = 0;
      }
      if (!$size)
      {
         $size = '18446744073709551610';
      }

      // if there is no parent_id then select all the articles
      if (is_null($parent_id) && $parent_id != 0)
      {
         $articles = ew_contents::where('type', 'article')->orderBy('title')->get(['*', \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")]);
         return ["totalRows" => $articles->count(), "result" => $articles->toArray()];
      }
      else
      {
         $container_id = ew_contents::find($parent_id);
         $container_id = $container_id['parent_id'];
         $articles = ew_contents::where('parent_id', '=', $parent_id)->where('type', 'article')->take($size)->skip($token)->get(['*', \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")]);
         $rows = array();
         $articles_ar = $articles->toArray();
         foreach ($articles_ar as $i)
         {
            $i["pre_parent_id"] = $container_id;
            $rows[] = $i;
         }
         return ["totalRows" => $articles->count(), "result" => $rows];
      }

      return \EWCore::log_error(400, 'tr{Something went wrong}');
   }

   public function get_content($id)
   {
      if (!isset($id))
         return \EWCore::log_error(400, 'tr{Content Id is requird}');
      $content = ew_contents::find($id, ['*', \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")]);
      return $content->toArray();
   }

   public function get_contents($title_filter = '%', $type = '%', $token = 0, $size = 99999999999999)
   {
      $db = \EWCore::get_db_connection();
      //$parentId = $db->real_escape_string($this->get_param("parentId"));
      //echo "asssssssssssssssss";
      if (!$token)
      {
         $token = 0;
      }
      if (!$size)
      {
         $size = '18446744073709551610';
      }

      $contents = ew_contents::where('type', 'LIKE', $type)
                      ->where(\Illuminate\Database\Capsule\Manager::raw("`title` COLLATE UTF8_GENERAL_CI"), 'LIKE', $title_filter . '%')
                      ->orderBy('title')->take($size)->skip($token)->get($id, ['*', \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")]);
      //print_r($contents);
      return ["totalRows" => $contents->count(), "result" => $contents->toArray()];
      /* $totalRows = $db->query("SELECT COUNT(*)  FROM ew_contents WHERE  title LIKE '$title_filter%' AND type LIKE '$type'") or die($db->error);
        $totalRows = $totalRows->fetch_assoc();
        $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%d-%m-%Y') AS 'date_created' FROM ew_contents WHERE title COLLATE UTF8_GENERAL_CI LIKE '$title_filter%' AND type LIKE '$type' ORDER BY title  LIMIT $token,$size") or die($db->error);

        //$out = array();
        $rows = array();

        while ($r = $result->fetch_assoc())
        {
        $rows[] = $r;
        }
        $db->close();
        $out = array("totalRows" => $totalRows['COUNT(*)'], "result" => $rows);
        return json_encode($out); */
   }

   public function add_category($title, $parent_id, $keywords, $description, $labels)
   {
      $db = \EWCore::get_db_connection();

      if (!$parentId)
         $parentId = 0;

      $html_content = $_REQUEST['content'];

      $result = $this->add_content("folder", $title, $parent_id, $keywords, $description, $html_content, "", $labels);
      $result = json_decode($result, true);

      /* $stm = $db->prepare("INSERT INTO content_categories (title , parent_id , date_created , content_categories.order) VALUES (? , ? , NOW() , '0')");
        $stm->bind_param("ss", $title, $parentId); */

      if ($result['data']["id"])
      {
         $content_id = $result["id"];
         $res = array("status" => "success", "message" => "Folder has been added successfully", "data" => ["id" => $content_id, "type" => "folder"]);
         return json_encode($res);
      }
      return $result;
   }

   public function get_category($id)
   {
      $db = \EWCore::get_db_connection();
      //$categoryId = $db->real_escape_string($_REQUEST["categoryId"]);


      $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents WHERE id = '$id'") or die($db->error);

      if ($rows = $result->fetch_assoc())
      {
         $db->close();
         //$actions = EWCore::read_actions_registry("ew-category-action-get");
         /* try
           {
           foreach ($actions as $id => $data)
           {
           if (method_exists($data["class"], $data["function"]))
           {
           $func_result = call_user_func(array($data["class"], $data["function"]), $rows);
           if ($func_result)
           $rows = $func_result;
           }
           }
           }
           catch (Exception $e)
           {

           } */
         return json_encode($rows);
      }
   }

   public function update_category($id = null, $title = null, $parent_id = null, $keywords = null, $description = null, $content = null, $labels = null)
   {
      $db = \EWCore::get_db_connection();

      //$createdModified = date('Y-m-d H:i:s');
      $v = new \Valitron\Validator($this->get_current_command_args());
      //print_r(json_decode(stripslashes($labels), TRUE));
      //echo $parent_id;
      //global $functions_arguments;
      //print_r($this->get_current_method_args());
      //$db = \EWCore::get_db_connection();
      //print_r(func_get_args());     
      $v->rule('required', ["title", "parent_id"])->message(' {field} is required');
      $v->rule('integer', "parent_id")->message(' {field} should be integer');
      $v->labels(array(
          "title" => 'tr{Title}',
          "parent_id" => 'Folder ID'
      ));
      /* $id = $db->real_escape_string($_REQUEST['id']);
        $title = $db->real_escape_string($_REQUEST['title']);
        $parent_id = $db->real_escape_string($_REQUEST['parent_id']);
        $keywords = $db->real_escape_string($_REQUEST['keywords']);
        $description = $db->real_escape_string($_REQUEST['description']); */
      if (!$v->validate())
         return EWCore::log_error("400", "New folder has not been added", $v->errors());

      /* $content = (stripcslashes($content));
        $createdModified = date('Y-m-d H:i:s');
        $stm = $db->prepare("UPDATE ew_contents
        SET title = ?
        , keywords = ?
        , description = ?
        , parent_id = ?
        , content = ?
        , date_modified = ? WHERE id = ?");
        $stm->bind_param("sssssss", $title, $keywords, $description, $parent_id, $content, $createdModified, $id); */
      $result = json_decode($this->update_content($id, $title, 'folder', $parent_id, $keywords, $description, $content, null, $labels), TRUE);

      if ($result["status"] === "success")
      {
         $result["message"] = "tr{Folder has been updated successfully}";
         return json_encode($result);
      }
      else
      {
         return EWCore::log_error("400", "New folder has not been added", $db->error_list);
      }
   }

   public function delete_image($id)
   {
      $db = \EWCore::get_db_connection();
      if (!$id)
         $id = $db->real_escape_string($_REQUEST["id"]);
      $result = $db->query("SELECT * FROM ew_contents WHERE parent_id = '$id' LIMIT 1");
      if ($result->fetch_assoc())
      {
         return json_encode(array(status => "unable", status_code => 2));
         return;
      }
      $result = $db->query("SELECT * FROM ew_contents, ew_images WHERE ew_contents.id = ew_images.content_id AND ew_contents.id = '$id' LIMIT 1");
      if ($file = $result->fetch_assoc())
      {
         $path_parts = pathinfo(EW_MEDIA_DIR . '/' . $file["source"]);
         unlink(EW_MEDIA_DIR . '/' . $path_parts["basename"]);
         unlink(EW_MEDIA_DIR . '/' . $path_parts["filename"] . '.thumb.' . $path_parts["extension"]);
      }
      $result = $db->query("DELETE FROM ew_contents WHERE type = 'image' AND id = '$id'");
      $db->close();
      if ($result)
      {
         return json_encode(array("status" => "success", "status_code" => 1, "message" => ""));
      }
      else
      {
         return json_encode(array("status" => "unsuccess", "status_code" => 0, "message" => ""));
      }
   }

   public function delete_content($type, $id)
   {
      $db = \EWCore::get_db_connection();
      if (!$type)
         $type = $db->real_escape_string($_REQUEST["type"]);
      if (!$id)
         $id = $db->real_escape_string($_REQUEST["id"]);
      $result = $db->query("SELECT * FROM ew_contents WHERE parent_id = '$id' LIMIT 1");
      if ($result->fetch_assoc())
      {
         //return array(status => "unable", status_code => 2);
         return \EWCore::log_error(400, "tr{In order to delete this folder, you must delete content of this folder first}");
      }
      $result = $db->query("DELETE FROM ew_contents WHERE type = '$type' AND id = '$id'");
      if ($result)
      {
         return array("status" => "success", "status_code" => 1, "message" => "Content has been deleted successfully");
      }
      else
      {
         return \EWCore::log_error(400, "tr{Something went wrong, please try again}");
      }
   }

   public function delete_album()
   {
      $db = \EWCore::get_db_connection();
      $albumId = $db->real_escape_string($_REQUEST["albumId"]);
      $res = $this->delete_content("album", $albumId);
      if ($res["status_code"] == 1)
         $res["message"] = "The album has been deleted successfuly";
      else if ($res["status_code"] == 2)
         $res["message"] = "Unable to delete the album";
      else
         $res["message"] = "Album has NOT been deleted";
      return json_encode($res);
   }

   public function delete_category($categoryId)
   {
      /* $db = \EWCore::get_db_connection();
        $categoryId = $db->real_escape_string($_REQUEST["categoryId"]); */
      return json_encode($this->delete_content("folder", $categoryId));
   }

   public function delete_article($articleId)
   {
      $db = \EWCore::get_db_connection();
      //$articleId = $db->real_escape_string($_REQUEST["articleId"]);
      $result = $db->query("DELETE FROM ew_contents WHERE id = '$articleId'");
      $db->close();
      if ($result)
      {
         echo json_encode(array(status => "success", "message" => "tr{Article has been deleted succesfully}"));
      }
      else
      {

         return EWCore::log_error("400", "tr{Something went wrong, please try again}", $db->error_list);
      }
   }

   public function get_documents_list($parentId, $token = null, $size = null)
   {
      $db = \EWCore::get_db_connection();

      if (!isset($token))
      {
         $token = 0;
      }
      if (!$size)
      {
         $size = 99999999999999;
      }

      $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM content_categories WHERE parent_id = '$parentId' ORDER BY title") or die("safasfasf");
      $categories = array();
      while ($r = $result->fetch_assoc())
      {
         $r["document_type"] = "folder";
         $categories[] = $r;
      }

      $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents WHERE category_id = '$parentId' ORDER BY title") or die("safasfasf");
      $articles = array();
      while ($r = $result->fetch_assoc())
      {
         $r["document_type"] = "article";
         $articles[] = $r;
      }
      $documents = array_merge($categories, $articles);
      $db->close();
      $out = array("totalRows" => count($documents), "result" => $documents);
      return json_encode($out);
   }

   public function get_title()
   {
      return "Content";
   }

   public function get_description()
   {
      return "Manage the content of your website. Add new artile, Edit or Delete exiting article";
   }

   function createThumbs($pathToImages, $pathToThumbs, $thumbWidth)
   {
      // open the directory
      $dir = opendir($pathToImages);

      // loop through it, looking for any/all JPG files:
      while (false !== ($fname = readdir($dir)))
      {
         // parse path for the extension
         $info = pathinfo($pathToImages . $fname);
         // continue only if this is a JPEG image
         if (strtolower($info['extension']) == 'jpg')
         {
            echo "Creating thumbnail for {$fname} <br />";

            // load image and get image size
            $img = imagecreatefromjpeg("{$pathToImages}{$fname}");
            $width = imagesx($img);
            $height = imagesy($img);

            // calculate thumbnail size
            $new_width = $thumbWidth;
            $new_height = floor($height * ( $thumbWidth / $width ));

            // create a new temporary image
            $tmp_img = imagecreatetruecolor($new_width, $new_height);

            // copy and resize old image into new image 
            imagecopyresized($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            // save thumbnail into a file
            imagejpeg($tmp_img, "{$pathToThumbs}{$fname}");
         }
      }
      // close the directory
      closedir($dir);
   }

   public function get_media_list($parent_id, $token = null, $size = null)
   {
      $db = \EWCore::get_db_connection();

      $path = "/";

      $root = EW_MEDIA_DIR;
      $new_width = 140;

      try
      {

         //$dir_contents = opendir($root . $path);
         /* if (!is_dir($root . $path . '.thumbs/'))
           {
           mkdir($root . $path . '.thumbs/');
           } */

         /* $result = $db->query("SELECT parent_id FROM ew_contents WHERE id = '$parent_id'") or die("safasfasf");
           while ($r = $result->fetch_assoc())
           {
           $container_id = $r["parent_id"];
           } */

         $files = array();
         // Folder
         $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents WHERE type = 'album' AND parent_id = '$parent_id' ORDER BY title") or die("safasfasf");
         while ($r = $result->fetch_assoc())
         {
            $files[] = array(title => $r["title"], type => "folder", size => "", ext => "", "parentId" => 0, "id" => $r["id"]);
         }

         // images
         $result = $db->query("SELECT *,ew_contents.id AS content_id, DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents, ew_images WHERE ew_contents.id = ew_images.content_id AND parent_id = '$parent_id' ORDER BY title") or die("safasfasf");
         while ($r = $result->fetch_assoc())
         {
            $file = $r["source"];
            /* while ($file = readdir($dir_contents))
              {
              if (strpos($file, '.') === 0 || strpos($file, '.thumb.'))
              continue; */
            $file_path = $root . $path . $file;
            $file_info = pathinfo($file_path);

            // create thumb for image if doesn't exist
            $tumbURL = 'asset/images' . $path . $file_info["filename"] . ".thumb." . $file_info["extension"];

            list($width, $height) = getimagesize($file_path);
            if (!file_exists($root . $path . $file_info["filename"] . ".thumb." . $file_info["extension"]) && $width > 140)
            {
               $this->create_image_thumb($file_path, 140);
               $tumbURL = 'asset/images' . $path . $file_info["filename"] . ".thumb." . $file_info["extension"];
            }
            else if ($width <= 140)
            {
               $tumbURL = 'asset/images' . $path . $file;
            }
//echo $file_info["extension"]." ".$this->file_types["jpg"];
//print_r($this->file_types);
            $files[] = array("id" => $r["content_id"], title => $r["title"], "parentId" => $container_id,
                type => $this->file_types[$file_info["extension"]] ? $this->file_types[$file_info["extension"]] : "unknown",
                size => round(filesize($file_path) / 1024), ext => $file_info["extension"],
                url => 'asset/images' . $path . $file,
                absUrl => EW_ROOT_URL . "asset/images/$file",
                originalUrl => EW_ROOT_URL . "media/$file",
                filename => $file_info["filename"],
                fileExtension => $file_info["extension"],
                thumbURL => EW_DIR . $tumbURL,
                path => $file_path);
         }
      }
      catch (Exception $e)
      {
         echo $e->getMessage();
      }
      return json_encode($files);
   }

   public function get_album($albumId)
   {
      $db = \EWCore::get_db_connection();


      $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents WHERE id = '$albumId'") or die($db->error);

      if ($rows = $result->fetch_assoc())
      {
         $db->close();

         return json_encode($rows);
      }
   }

   public function add_album($title = null, $keywords = NULL, $description = NULL, $html_content = NULL, $labels)
   {

      $validator = new \Valitron\Validator($this->get_current_command_args());
      $validator->rule('required', ['title']);
      if (!$validator->validate())
      {
         return EWCore::log_error(400, 'tr{Form validation error}', $validator->errors());
      }

      $result = $this->add_content("album", $title, 0, $keywords, $description, $htmlContent, "", $labels);
      $result = json_decode($result, true);
      //$res = array(status => "success", message => "The directory {" . $title . "} hase been created succesfuly");*/
      return json_encode(['status' => "success",
          'message' => "The directory '$title' hase been created succesfuly",
          'data' => $result]);
   }

   public function update_album()
   {
      $db = \EWCore::get_db_connection();
      $albumId = $db->real_escape_string($_REQUEST['id']);
      $title = $db->real_escape_string($_REQUEST['title']);
      $parent_id = $db->real_escape_string($_REQUEST['parent_id']);
      $keywords = $db->real_escape_string($_REQUEST['keywords']);
      $description = $db->real_escape_string($_REQUEST['description']);
      $htmlContent = stripcslashes($_REQUEST['html_content']);
      $createdModified = date('Y-m-d H:i:s');
      $stm = $db->prepare("UPDATE ew_contents 
            SET title = ? 
            , keywords = ? 
            , description = ? 
            , parent_id = ? 
            , content = ? 
            , date_modified = ? WHERE id = ?");
      $stm->bind_param("sssssss", $title, $keywords, $description, $parent_id, $htmlContent, $createdModified, $albumId);

      if ($stm->execute())
      {
         $stm->close();
         $db->close();

         echo json_encode(array(status => "success", title => $title));
      }
      else
      {
         echo json_encode(array(status => "unsuccess"));
      }
   }

   public function delete_content_by_id($id)
   {
      $db = \EWCore::get_db_connection();

      if (!$id)
         $id = $db->real_escape_string($_REQUEST["id"]);
      $result = $db->query("SELECT * FROM ew_contents WHERE parent_id = '$id' LIMIT 1");
      $output = array();
      if ($result->fetch_assoc())
      {
         return json_encode(array(status => "unable", status_code => 2));
      }
      $result = $db->query("DELETE FROM ew_contents WHERE id = '$id'");
      $db->close();
      if ($result)
      {
         return json_encode(array("status" => "success", "status_code" => 1, "message" => ""));
      }
      else
      {
         return json_encode(array("status" => "unsuccess", "status_code" => 0, "message" => ""));
      }
      //return json_encode(array("status" => "success", "status_code" => 1, "message" => ""));
   }

   public function create_resized_image($image_path, $width = null, $height = null, $same_path = true)
   {
      if (!$width && !$height)
         return;
      $src_image = imagecreatefromstring(file_get_contents($image_path));
      $path_parts = pathinfo($image_path);
      $type = $path_parts['extension'];
      //$foo->
      imagealphablending($src_image, true);
      if (!$height || $height == 0)
         $height = floor(imagesy($src_image) * ( $width / imagesx($src_image) ));
      if (!$width || $width == 0)
         $width = floor(imagesx($src_image) * ( $height / imagesy($src_image) ));
      $dst = imagecreatetruecolor($width, $height);
      imagealphablending($dst, false);
      imagesavealpha($dst, true);
      imagecopyresampled($dst, $src_image, 0, 0, 0, 0, $width, $height, imagesx($src_image), imagesy($src_image));
      if (!$same_path)
      {
         $path_parts['dirname'] = EW_MEDIA_DIR;
      }
      switch ($type)
      {
         case 'bmp': imagewbmp($dst, EW_MEDIA_DIR . '/' . $path_parts['filename'] . ".{$width},{$height}.bmp");
            break;
         case 'gif': imagegif($dst, EW_MEDIA_DIR . '/' . $path_parts['filename'] . ".{$width},{$height}.gif");
            break;
         case 'jpg': imagejpeg($dst, EW_MEDIA_DIR . '/' . $path_parts['filename'] . ".{$width},{$height}.jpg", 100);
            break;
         case 'jpeg': imagejpeg($dst, EW_MEDIA_DIR . '/' . $path_parts['filename'] . ".{$width},{$height}.jpg", 100);
            break;
         case 'png': imagepng($dst, EW_MEDIA_DIR . '/' . $path_parts['filename'] . ".{$width},{$height}.png");
            break;
      }
   }

   public function create_image_thumb($image_path, $width = null, $height = null)
   {
      if (!$width && !$height)
         return;
      $src_image = imagecreatefromstring(file_get_contents($image_path));
      $path_parts = pathinfo($image_path);
      $type = $path_parts["extension"];
      //$foo->
      imagealphablending($src_image, true);
      if (!$height)
         $height = floor(imagesy($src_image) * ( $width / imagesx($src_image) ));
      if (!$width)
         $width = floor(imagesx($src_image) * ( $height / imagesy($src_image) ));
      $dst = imagecreatetruecolor($width, $height);
      imagealphablending($dst, false);
      imagesavealpha($dst, true);
      imagecopyresampled($dst, $src_image, 0, 0, 0, 0, $width, $height, imagesx($src_image), imagesy($src_image));
      // save thumbnail into a file
      //imagepng($dst, $path_parts['dirname'] . '/' . $path_parts['filename'] . '.thumb.png', 9, PNG_ALL_FILTERS);
      switch ($type)
      {
         case 'bmp': imagewbmp($dst, $path_parts['dirname'] . '/' . $path_parts['filename'] . ".thumb.bmp");
            break;
         case 'gif': imagegif($dst, $path_parts['dirname'] . '/' . $path_parts['filename'] . ".thumb.gif");
            break;
         case 'jpg': imagejpeg($dst, $path_parts['dirname'] . '/' . $path_parts['filename'] . ".thumb.jpg", 90);
            break;
         case 'jpeg': imagejpeg($dst, $path_parts['dirname'] . '/' . $path_parts['filename'] . ".thumb.jpg", 90);
            break;
         case 'png': imagepng($dst, $path_parts['dirname'] . '/' . $path_parts['filename'] . ".thumb.png", 9, PNG_ALL_FILTERS);
            break;
      }
   }

   public function upload_file($path, $parent_id)
   {
      $db = \EWCore::get_db_connection();
      require_once EW_ROOT_DIR . "core/upload.class.php";
      ini_set("memory_limit", "100M");
      if (isset($_REQUEST["path"]))
         $path = $_REQUEST["path"];

      if (!$parent_id)
         $parent_id = 0;
      $alt_text = $_REQUEST["alt_text"];
      //if (!$order)
      //  $order = 0;


      $root = EW_MEDIA_DIR;
      $succeed = 0;
      $error = 0;
      $thegoodstuf = '';
      $alt_text = "";
      $files = array();
      foreach ($_FILES['img'] as $k => $l)
      {
         foreach ($l as $i => $v)
         {
            if (!array_key_exists($i, $files))
               $files[$i] = array();
            $files[$i][$k] = $v;
         }
      }
      foreach ($files as $file)
      {
         //print_r($file);
         $foo = new \upload($file);
         if ($foo->uploaded)
         {

            // save uploaded image with no changes
            $foo->Process($root);
            if ($foo->processed)
            {
               $result = $this->add_content("image", $foo->file_dst_name_body, $parent_id, "", "", "", "", "");
               $result = json_decode($result, true);
               //print_r($result);
               // $stm = $db->prepare("INSERT INTO ew_contents (title , keywords , description , parent_id , source_page_address , html_content , ew_contents.order , date_created,type) 
               //  VALUES (? , ? , ? , ? , ? , ? , ? , ?,'article')") or die($db->error);
               //  $stm->bind_param("ssssssss", $title, $keywords, $description, $categoryId, $sourcePageAddress, $htmlContent, $order, $createdDate) or die($db->error); 
               //print_r($result);
               if ($result["data"]["id"])
               {
                  $content_id = $result["data"]["id"];
                  $stm = $db->prepare("INSERT INTO ew_images (content_id, source , alt_text) 
            VALUES (? , ? , ?)") or die($db->error);
                  $image_path = $foo->file_dst_name;
                  $stm->bind_param("sss", $content_id, $image_path, $alt_text) or die($db->error);
                  if ($stm->execute())
                  {
                     $res = array("status" => "success", "id" => $stm->insert_id);
                     //$stm->close();
                     //$db->close();
                  }
               }

               $this->create_image_thumb($foo->file_dst_pathname, 140);
               $succeed++;
               $stm->close();
               $db->close();
            }
            else
            {
               $error++;
            }
         }
         else
         {
            $error+=2;
         }
      }

      return json_encode(array(status => "success", message => "Uploaded: " . $succeed . " Error: " . $error . ' ' . $foo->error));
   }

   /**
    * 
    * @param array $form_config [optional] <p>An array that contains content form configurations.<br/>
    * the keys are: <b>title</b>, <b>saveActivity</b>, <b>updateActivity</b>, <b>data</b>
    * </p>
    * @return string
    */
   public static function create_content_form($form_config = null)
   {
      ob_start();
      include 'content-form.php';
      return ob_get_clean();
   }

}
