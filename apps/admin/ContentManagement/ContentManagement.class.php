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

   private $file_types;
   private $images_resources = array("/is/htdocs/wp1067381_3GN1OJU4CE/www/culturenights/app/webroot/img/logos/");

   public function init_plugin()
   {
      $this->file_types = array("jpeg" => "image",
          "jpg" => "image",
          "png" => "image",
          "gif" => "image",
          "txt" => "text",
          "mp3" => "sound",
          "mp4" => "video");
      EWCore::register_resource("images", array($this, "image_loader"));
      $this->register_permission("see-content", "User can see the contents", array($this->get_index(), "get_content",
          "get_category",
          "get_article",
          "get_album",
          "get_categories_list",
          "get_articles_list",
          "get_medias_list",
          "article-form.php_see",
          "category-form.php_see",
          "album-form.php_see"));

      $this->register_permission("manipulate-content", "User can add new, edit, delete contents", array($this->get_index(), "add_content",
          "add_category",
          "add_article",
          "add_album",
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
      /* $this->register_activity("article-form", array("form" => "article-form.php", "title" => "New Article"));
        $this->register_activity("article-form", array("form" => "article-form.php?see", "title" => "See Article"));
        $this->register_activity("category-form", array("form" => "category-form.php", "title" => "New Folder")); */
   }

   private function ew_label_document($key, $value, $data, $form_id)
   {
      //print_r($data);
      /* if ($value)
        {
        //echo "haa";
        $articleInfo = json_decode(EWCore::process_command("admin", "ContentManagement", "get_article", array("articleId" => $value)), true);
        }
        else if ($data)
        {
        //echo "asdasd";
        $articleInfo = json_decode(stripslashes($data), true);
        $value = $articleInfo["id"];
        } */
      ob_start();
      ?>
      <div class="col-xs-12">
         <input class="text-field" type="hidden" id="<?php echo $key ?>_key" name="key" value="<?php echo $key ?>"/>
         <input class="text-field" type="hidden" id="<?php echo $key ?>_value" name="value" value=""/>
         <input class="text-field" data-label="Select a content" id="<?php echo $key ?>_text" name="text" value="" />
      </div>
      <div class="col-xs-12">
         <ul id="<?php echo $key ?>_attached" class="list indent">

         </ul>
      </div>
      <script>

         $("#<?php echo $key ?>_text").autocomplete({
            source: function (input) {
               $.post("<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_contents", {title_filter: $("#<?php echo $key ?>_text").val(), type: "article", size: 30}, function (data) {
                  input.trigger("updateList", [data.result]);
               }, "json");
            },
            templateText: "<li class='text-item'><a href='#'><%= title %><span><%= date_created %></span></a><li>",
            insertText: function (item) {
               $("#<?php echo $key ?>_value").val(item.id);
               return item.title;
            }
         });

         $("#<?php echo $form_id ?>").on("refresh", function (e, formData)
         {
            // Init
            if (!ContentForm.getLabel("admin_ContentManagement_document"))
            {
               $("#<?php echo $key ?>_value").val(formData["id"]);
               $("#<?php echo $key ?>_text").val(formData["title"]).change();
            }
            $.post("<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_content_with_label", {content_id: ContentForm.getLabel("admin_ContentManagement_document"), key: "<?php echo $key ?>"}, function (data) {
               $("#<?php echo $key ?>_attached").empty();
               $.each(data, function (i, content)
               {
                  var langItem = $("<li class=''><a rel='ajax' href='#' class='link'>" + content.title + "</a></li>");
                  if (content.id == "<?php echo $value ?>")
                  {
                     $("#<?php echo $key ?>_value").val(content.id);
                     $("#<?php echo $key ?>_text").val(content.title).change();
                  }
                  if (content.id == formData.id)
                  {
                     langItem.addClass("active");
                     //$("#<?php echo $key ?>_value").val(formData["id"]);
                     //$("#<?php echo $key ?>_text").val(formData["title"]).change();
                  }
                  else
                     langItem.find("a").on("click", function ()
                     {
                        $.post("<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_article", {articleId: content.id}, function (data)
                        {
                           ContentForm.setData(data);
                        }, "json");
                     });
                  $("#<?php echo $key ?>_attached").append(langItem);
               });
            }, "json");
         });
      </script>

      <?php
      $html = ob_get_clean();
      return json_encode(["html" => $html]);
   }

   private function ew_label_language($key, $value, $data, $form_id)
   {
      if (!$value)
         $value = "en";
      ob_start();
      ?>
      <div class="col-xs-12">
         <input class="text-field" type="hidden" id="<?php echo $key ?>_key" name="key" value="<?php echo $key ?>"/>
         <input class="text-field" type="hidden" id="<?php echo $key ?>_value" name="value" value=""/>
         <select id="<?php echo $key ?>_select" data-label="tr{Add a language}">
            <option value="en">Default</option>
            <option value="en">English</option>
            <option value="es">Spanish</option>
            <option value="de">German</option>
            <option value="ru">Russian</option>
            <option value="cmn">Mandarin</option>
            <option value="ar">Arabic</option>
            <option value="fa">فارسی</option>
         </select>
      </div>
      <div class="col-xs-12">
         <ul id="<?php echo $key ?>_languages" class="list links">

         </ul>
      </div>
      <script>
         var languages = {en: "English", es: "Spanish", de: "German", ru: "Russian", cmn: "Mandarin", ar: "Arabic", fa: "فارسی"};
         $("#<?php echo $key ?>_value").val("<?php echo $value ?>");
         $("#<?php echo $key ?>_select").on("change", function ()
         {
            $("#<?php echo $key ?>_value").val($("#<?php echo $key ?>_select").val());
         });
         $("#<?php echo $form_id ?>").on("refresh", function (e, formData)
         {
            var documentId = formData.id;
            if (ContentForm.getLabel("admin_ContentManagement_document") != documentId)
            {
               documentId = ContentForm.getLabel("admin_ContentManagement_document");
            }
            //init
            if (!ContentForm.getLabel("admin_ContentManagement_language"))
            {
               $("#<?php echo $key ?>_select").change();
            }
            $.post("<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_content_with_label", {content_id: documentId, key: "<?php echo $key ?>"}, function (data) {
               $("#<?php echo $key ?>_languages").empty();
               $.each(data, function (i, content)
               {
                  //$("#<?php echo $key ?>_select option[value='" + content.value + "']").remove();
                  var langItem = $("<li><a rel='ajax' href='#' class='link'>" + languages[content.value] + "<p>" + content["title"] + "</p></a></li>");
                  if (content.id == formData.id)
                  {
                     langItem.addClass("active");
                     $("#<?php echo $key ?>_value").val(content.value);
                  }
                  else
                     langItem.find("a").on("click", function ()
                     {
                        $.post("<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_article", {articleId: content.id}, function (data)
                        {
                           ContentForm.setData(data);
                           //EW.setHashParameter("articleId", lang.id)
                        }, "json");
                     });
                  $("#<?php echo $key ?>_languages").append(langItem);
               });
            }, "json");
         });

      </script>

      <?php
      $html = ob_get_clean();
      return json_encode(["html" => $html]);
   }

   public function image_loader($file)
   {

      preg_match('/(.*)\.(\d*),(\d*)\.([^\.]\w*)/', $file, $match);
//print_r($match);
//return;
      //print_r($match);
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
      // If the resized file still does not exist, then the original file will be send
      if (!file_exists($file))
      {
         //echo $this->images_resources[0] . $match[1] . "." . $match[4];
         if (file_exists($this->images_resources[0] . $match[1] . "." . $match[4]))
         {
            //echo "h3";
            $file = $this->images_resources[0] . $match[1] . "." . $match[4];
         }
         else
         {
            $file = EW_APPS_DIR. "/admin/ContentManagement/no-image.png";
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
      $db = EWCore::get_db_connection();

      $setting = $db->query("SELECT * FROM ew_contents_labels WHERE content_id = $content_id AND `key` = '$key'") or die($db->error);
      if ($user_info = $setting->fetch_assoc())
      {
         if ($value)
         {
            $stm = $db->prepare("UPDATE ew_contents_labels SET `value` = ? WHERE content_id = ? AND `key` = ?") or die($db->error);
            $stm->bind_param("sss", $value, $content_id, $key) or die($db->error);
            if ($stm->execute())
            {
               $res = array("status" => "success", "id" => $stm->insert_id);
            }
         }
         else
         {
            $stm = $db->prepare("DELETE FROM ew_contents_labels WHERE content_id = ? AND `key` = ?") or die($db->error);
            $stm->bind_param("ss", $content_id, $key) or die($db->error);
            if ($stm->execute())
            {
               $res = array("status" => "success");
            }
         }
      }
      else
      {
         $stm = $db->prepare("INSERT INTO ew_contents_labels (content_id, `key` , `value`) 
            VALUES (? , ? , ? )") or die($db->error);
         $stm->bind_param("sss", $content_id, $key, $value) or die($db->error);
         if ($stm->execute())
         {
            $res = array("status" => "success", "id" => $stm->insert_id);
         }
      }

      return json_encode($res);
   }

   /**
    * 
    * @param type $content_id
    * @return json <p>A list of content labels</p>
    */
   public static function get_content_labels($content_id, $key = '%')
   {
      $db = EWCore::get_db_connection();
      if (!$key)
         $key = '%';
      //$totalRows = $MYSQLI->query("SELECT COUNT(*)  FROM ew_contents_labels WHERE id=$content_id") or die($MYSQLI->error);
      //$totalRows = $totalRows->fetch_assoc();
      $result = $db->query("SELECT * FROM ew_contents_labels WHERE content_id=$content_id AND `key`LIKE '$key'") or die($db->error);

      //$out = array();
      $rows = array();

      while ($r = $result->fetch_assoc())
      {
         $rows[] = $r;
      }
      //$MYSQLI->close();
      //$out = array("totalRows" => $totalRows['COUNT(*)'], "result" => $rows);
      return json_encode($rows);
   }

   public static function get_content_with_label($content_id, $key, $value = '%')
   {
      $db = EWCore::get_db_connection();
      if (!$content_id)
         return json_encode([]);
      if (!$value)
         $value = '%';
      //$totalRows = $MYSQLI->query("SELECT COUNT(*)  FROM ew_contents_labels WHERE id=$content_id") or die($MYSQLI->error);
      //$totalRows = $totalRows->fetch_assoc();
      $result = $db->query("SELECT *,ew_contents.id AS id, DATE_FORMAT(date_created,'%d-%m-%Y') FROM ew_contents_labels, ew_contents "
              //. "WHERE content_id in (SELECT content_id from ew_contents_labels  WHERE `key` = 'admin_ContentManagement_document' AND `value` = $content_id)"
              . "WHERE (content_id in (SELECT content_id from ew_contents_labels  WHERE `content_id` = $content_id)"
              . "OR content_id in (SELECT content_id from ew_contents_labels  WHERE `key` = 'admin_ContentManagement_document' AND `value` = $content_id))"
              . "AND ew_contents_labels.content_id = ew_contents.id "
              . "AND `key`LIKE '$key' AND `value` LIKE '$value' ORDER BY `value`") or die($db->error);

      //$out = array();
      $rows = array();

      while ($r = $result->fetch_assoc())
      {
         $rows[] = $r;
      }
      //$MYSQLI->close();
      //$out = array("totalRows" => $totalRows['COUNT(*)'], "result" => $rows);
      return json_encode($rows);
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
   public function add_content($type, $title, $parent_id, $keywords, $description, $content, $featured_image, $labels, $date_created, $date_modified)
   {

      $db = EWCore::get_db_connection();
      if (!$date_created)
      {
         $date_created = date('Y-m-d H:i:s');
      }
      if (!$date_modified)
      {
         $date_modified = date('Y-m-d H:i:s');
      }
      $content = stripcslashes($content);

      if (!$type)
      {
         $res = array("status" => "error", "error_message" => "type is required");
      }

      //if (!$order)
      //  $order = 0;

      $stm = $db->prepare("INSERT INTO ew_contents (type, title , keywords , description , parent_id , content , featured_image , date_created, date_modified) 
            VALUES (? , ? , ? , ? , ? , ? , ? , ? , ?)") or die($db->error);
      $stm->bind_param("sssssssss", $type, $title, $keywords, $description, $parent_id, $content, $featured_image, $date_created, $date_modified) or die($db->error);
      if ($stm->execute())
      {
         $res = array("status" => "success", "id" => $stm->insert_id);
         foreach ($labels as $label)
         {
            
         }

         //$MYSQLI->close();
      }
      return json_encode($res);
   }

   public function update_content($id, $title, $type, $parent_id, $keywords, $description, $content, $featured_image, $labels)
   {
      $v = new \Valitron\Validator($this->get_current_command_args());
      //global $functions_arguments;
      //print_r($this->get_current_method_args());
      $db = \EWCore::get_db_connection();
      //print_r(func_get_args());     
      $v->rule('required', "title")->message(' {field} is required');
      $v->rule('integer', "parent_id")->message(' {field} should be integer');
      $v->labels(array(
          "title" => 'tr{Title}',
          "parent_id" => 'Folder ID'
      ));

      if (!$v->validate())
      {
         return EWCore::log_error("400", "tr{Content has not been updated}", $v->errors());
      }
      $labels = json_decode(stripslashes($labels), true);
      $content = (stripcslashes($content));
      //if
      $date_modified = date('Y-m-d H:i:s');
      $stm = $db->prepare("UPDATE ew_contents 
            SET title = ? 
            , slug = ? 
            , type = ?
            , keywords = ? 
            , description = ? 
            , parent_id = ? 
            , content = ? 
            , date_modified = ? WHERE id = ?");
      $stm->bind_param("sssssssss", $title, EWCore::to_slug($title, 'ew_contents'), $type, $keywords, $description, $parent_id, $content, $date_modified, $id);

      if ($stm->execute())
      {
         foreach ($labels as $key => $value)
         {
            //echo $key . ': ' . $value;
            $this->update_label($id, $key, $value);
         }
         //$stm->close();
         //$db->close();

         return json_encode([status => "success", message => "tr{The content has been updated successfully}", "data" => json_decode($this->get_content($id), TRUE)]);
      }
      else
      {
         return EWCore::log_error("400", "Something went wrong, content has not been updated", $db->error_list);
      }
   }

   public function add_article($labels)
   {
      $MYSQLI = get_db_connection();
      $title = $MYSQLI->real_escape_string($_REQUEST['title']);
      $parentId = $MYSQLI->real_escape_string($_REQUEST['parent_id']);
      if (!$parentId)
         $parentId = 0;
      $keywords = $MYSQLI->real_escape_string($_REQUEST['keywords']);
      $description = $MYSQLI->real_escape_string($_REQUEST['description']);
      //$sourcePageAddress = $MYSQLI->real_escape_string($_REQUEST['source_page_address']);
      //$htmlContent = html_entity_decode($_REQUEST['content']);
      $htmlContent = $_REQUEST['content'];
      $order = $MYSQLI->real_escape_string($_REQUEST['order']);
      //$createdDate = $MYSQLI->real_escape_string($_REQUEST['date_created']);
      $this->get_current_command_args();
      if (!$title)
      {
         $res = array("status" => "error", message => "The Tile and Date fields are required");
      }
      if (!$order)
         $order = 0;

      $result = $this->add_content("article", $title, $parentId, $keywords, $description, $htmlContent, $labels);
      $result = json_decode($result, true);

      if ($result["id"])
      {
         return json_encode(["status" => "success", "categoryId" => $result["id"], "title" => $title, "message" => "tr{The new article has been added succesfully}", "data" => ["id" => $result["id"]]]);
         // End of plugins actions call
      }
      return \EWCore::log_error(400, "tr{Something went wrong, content has not been added}");
   }

   public function get_article($articleId)
   {
      //global $EW;
      $MYSQLI = get_db_connection();
      //$articleId = $MYSQLI->real_escape_string($_REQUEST["articleId"]);

      $result = $MYSQLI->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents WHERE id = '$articleId'") or $MYSQLI->error;
      /* $result = $MYSQLI->query("SELECT *,ew_contents.id AS id,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents, ew_contents_labels "
        . "WHERE ew_contents.id = ew_contents_labels.content_id "
        . "AND ew_contents_labels.key = 'admin_ContentManagement_document' "
        . "AND ew_contents_labels.value = ew_contents.id "
        . "AND ew_contents.id = '$articleId'") or $MYSQLI->error; */

      if ($rows = $result->fetch_assoc())
      {
         $rows["labels"] = ContentManagement::get_content_labels($articleId);

         $MYSQLI->close();

         /* $actions = EWCore::read_actions_registry("ew-article-action-get");
           try
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

   public function update_article($id, $title, $parent_id, $keywords = null, $description = null, $content = null, $labels = null)
   {
//$oo = new ReflectionMethod()
      //$keys = array("id", "title", "parent_id", "keywords", "description", "content");
      //$method_object = new ReflectionMethod($this, __FUNCTION__);
      //$keys = $method_object->getParameters();
      $v = new \Valitron\Validator($this->get_current_command_args());
      //print_r(json_decode(stripslashes($labels), TRUE));
      //echo $parent_id;
      //global $functions_arguments;
      //print_r($this->get_current_method_args());
      $MYSQLI = get_db_connection();
      //print_r(func_get_args());     
      $v->rule('required', ["title", "parent_id"])->message(' {field} is required');
      $v->rule('integer', "parent_id")->message(' {field} should be integer');
      $v->labels(array(
          "title" => 'tr{Title}',
          "parent_id" => 'Folder ID'
      ));
      /* $id = $MYSQLI->real_escape_string($_REQUEST['id']);
        $title = $MYSQLI->real_escape_string($_REQUEST['title']);
        $parent_id = $MYSQLI->real_escape_string($_REQUEST['parent_id']);
        $keywords = $MYSQLI->real_escape_string($_REQUEST['keywords']);
        $description = $MYSQLI->real_escape_string($_REQUEST['description']); */
      if (!$v->validate())
         return EWCore::log_error("400", "New article has not been added", $v->errors());

      /* $content = (stripcslashes($content));
        $createdModified = date('Y-m-d H:i:s');
        $stm = $MYSQLI->prepare("UPDATE ew_contents
        SET title = ?
        , keywords = ?
        , description = ?
        , parent_id = ?
        , content = ?
        , date_modified = ? WHERE id = ?");
        $stm->bind_param("sssssss", $title, $keywords, $description, $parent_id, $content, $createdModified, $id); */
      $result = json_decode($this->update_content($id, $title, 'article', $parent_id, $keywords, $description, $content, null, $labels), TRUE);

      if ($result["status"] === "success")
      {
         $result["message"] = "tr{Article has been updated successfully}";
         return json_encode($result);
      }
      else
      {
         return EWCore::log_error("400", "New article has not been added", $MYSQLI->error_list);
      }
   }

   public function get_categories_list($parent_id, $token, $size)
   {
      $MYSQLI = get_db_connection();

      $result = $MYSQLI->query("SELECT parent_id FROM ew_contents WHERE id = '$parent_id'") or die("safasfasf");
      while ($r = $result->fetch_assoc())
      {
         $container_id = $r["parent_id"];
      }

      $totalRows = $MYSQLI->query("SELECT COUNT(*) FROM ew_contents WHERE type = 'folder' AND parent_id = '$parent_id'") or die(error_reporting());
      $totalRows = $totalRows->fetch_assoc();

      $result = $MYSQLI->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents WHERE type = 'folder' AND parent_id = '$parent_id'") or die("safasfasf");

      $rows = array();
      while ($r = $result->fetch_assoc())
      {
         $r["parent_id"] = $container_id;
         $rows[] = $r;
      }
      $MYSQLI->close();
      $out = array("totalRows" => $totalRows['COUNT(*)'], "result" => $rows);
      return json_encode($out);
   }

   public function get_articles_list($parent_id = null, $token, $size)
   {
      $MYSQLI = get_db_connection();

      if (!isset($token))
      {
         $token = 0;
      }
      if (!$size)
      {
         $size = 99999999999999;
      }

      if (is_null($parent_id) && $parent_id != 0)
      {
         //$result = $MYSQLI->query("SELECT parent_id FROM ew_contents") or die("safasfasf");
         $result = $MYSQLI->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents WHERE type = 'article' ORDER BY title") or die("EW:error on selecting articles list");
      }
      else
      {
         $result = $MYSQLI->query("SELECT parent_id FROM ew_contents WHERE id = '$parent_id'") or die("safasfasf");
         while ($r = $result->fetch_assoc())
         {
            $container_id = $r["parent_id"];
         }
         $result = $MYSQLI->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents WHERE type = 'article' AND parent_id = '$parent_id' ORDER BY title") or die("EW:error on selecting articles list");
      }

      $rows = array();
      while ($r = $result->fetch_assoc())
      {
         $r["pre_parent_id"] = $container_id;
         $rows[] = $r;
      }
      $MYSQLI->close();
      $out = array("totalRows" => $result->num_rows, "result" => $rows);
      return json_encode($out);
   }

   public function get_content($id)
   {
      $MYSQLI = get_db_connection();
      //$articleId = $MYSQLI->real_escape_string($_REQUEST["articleId"]);

      $result = $MYSQLI->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents WHERE id = '$id'") or $MYSQLI->error;
      if ($rows = $result->fetch_assoc())
      {
         $rows["labels"] = ContentManagement::get_content_labels($id);

         $MYSQLI->close();
         return json_encode($rows);
      }
   }

   public function get_contents($title_filter = '%', $type = '%', $token = 0, $size = 99999999999999)
   {
      $MYSQLI = get_db_connection();
      //$parentId = $MYSQLI->real_escape_string($this->get_param("parentId"));
      //echo "asssssssssssssssss";
      if (!$token)
      {
         $token = 0;
      }
      if (!$size)
      {
         $size = 99999999999999;
      }

      $totalRows = $MYSQLI->query("SELECT COUNT(*)  FROM ew_contents WHERE  title LIKE '$title_filter%' AND type LIKE '$type'") or die($MYSQLI->error);
      $totalRows = $totalRows->fetch_assoc();
      $result = $MYSQLI->query("SELECT *,DATE_FORMAT(date_created,'%d-%m-%Y') AS 'date_created' FROM ew_contents WHERE title COLLATE UTF8_GENERAL_CI LIKE '$title_filter%' AND type LIKE '$type' ORDER BY title  LIMIT $token,$size") or die($MYSQLI->error);

      //$out = array();
      $rows = array();

      while ($r = $result->fetch_assoc())
      {
         $rows[] = $r;
      }
      $MYSQLI->close();
      $out = array("totalRows" => $totalRows['COUNT(*)'], "result" => $rows);
      return json_encode($out);
   }

   public function add_category($title, $parent_id, $keywords, $description, $html_content, $labels)
   {
      $MYSQLI = \EWCore::get_db_connection();

      if (!$parentId)
         $parentId = 0;

      $html_content = $_REQUEST['content'];

      $result = $this->add_content("folder", $title, $parent_id, $keywords, $description, $html_content, $labels);
      $result = json_decode($result, true);

      /* $stm = $MYSQLI->prepare("INSERT INTO content_categories (title , parent_id , date_created , content_categories.order) VALUES (? , ? , NOW() , '0')");
        $stm->bind_param("ss", $title, $parentId); */

      if ($result["id"])
      {
         $content_id = $result["id"];
         $res = array("status" => "success", "message" => "Folder has been added successfully", "data" => ["id" => $content_id]);
         return json_encode($res);
      }
   }

   public function get_category($id)
   {
      $MYSQLI = get_db_connection();
      //$categoryId = $MYSQLI->real_escape_string($_REQUEST["categoryId"]);


      $result = $MYSQLI->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents WHERE id = '$id'") or die($MYSQLI->error);

      if ($rows = $result->fetch_assoc())
      {
         $MYSQLI->close();
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
      $MYSQLI = get_db_connection();

      //$createdModified = date('Y-m-d H:i:s');
      $v = new \Valitron\Validator($this->get_current_command_args());
      //print_r(json_decode(stripslashes($labels), TRUE));
      //echo $parent_id;
      //global $functions_arguments;
      //print_r($this->get_current_method_args());
      //$MYSQLI = get_db_connection();
      //print_r(func_get_args());     
      $v->rule('required', ["title", "parent_id"])->message(' {field} is required');
      $v->rule('integer', "parent_id")->message(' {field} should be integer');
      $v->labels(array(
          "title" => 'tr{Title}',
          "parent_id" => 'Folder ID'
      ));
      /* $id = $MYSQLI->real_escape_string($_REQUEST['id']);
        $title = $MYSQLI->real_escape_string($_REQUEST['title']);
        $parent_id = $MYSQLI->real_escape_string($_REQUEST['parent_id']);
        $keywords = $MYSQLI->real_escape_string($_REQUEST['keywords']);
        $description = $MYSQLI->real_escape_string($_REQUEST['description']); */
      if (!$v->validate())
         return EWCore::log_error("400", "New folder has not been added", $v->errors());

      /* $content = (stripcslashes($content));
        $createdModified = date('Y-m-d H:i:s');
        $stm = $MYSQLI->prepare("UPDATE ew_contents
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
         return EWCore::log_error("400", "New folder has not been added", $MYSQLI->error_list);
      }
   }

   public function delete_image($id)
   {
      $MYSQLI = get_db_connection();
      if (!$id)
         $id = $MYSQLI->real_escape_string($_REQUEST["id"]);
      $result = $MYSQLI->query("SELECT * FROM ew_contents WHERE parent_id = '$id' LIMIT 1");
      if ($result->fetch_assoc())
      {
         return json_encode(array(status => "unable", status_code => 2));
         return;
      }
      $result = $MYSQLI->query("SELECT * FROM ew_contents, ew_images WHERE ew_contents.id = ew_images.content_id AND ew_contents.id = '$id' LIMIT 1");
      if ($file = $result->fetch_assoc())
      {
         $path_parts = pathinfo(EW_MEDIA_DIR . '/' . $file["source"]);
         unlink(EW_MEDIA_DIR . '/' . $path_parts["basename"]);
         unlink(EW_MEDIA_DIR . '/' . $path_parts["filename"] . '.thumb.' . $path_parts["extension"]);
      }
      $result = $MYSQLI->query("DELETE FROM ew_contents WHERE type = 'image' AND id = '$id'");
      $MYSQLI->close();
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
      $MYSQLI = get_db_connection();
      if (!$type)
         $type = $MYSQLI->real_escape_string($_REQUEST["type"]);
      if (!$id)
         $id = $MYSQLI->real_escape_string($_REQUEST["id"]);
      $result = $MYSQLI->query("SELECT * FROM ew_contents WHERE parent_id = '$id' LIMIT 1");
      if ($result->fetch_assoc())
      {
         return array(status => "unable", status_code => 2);
         return;
      }
      $result = $MYSQLI->query("DELETE FROM ew_contents WHERE type = '$type' AND id = '$id'");
      $MYSQLI->close();
      if ($result)
      {
         return array("status" => "success", "status_code" => 1, "message" => "Content has been deleted successfully");
      }
      else
      {
         return array("status" => "unsuccess", "status_code" => 0, "message" => "Subcontent should be deleted first, content has not been deleted");
      }
   }

   public function delete_album()
   {
      $MYSQLI = get_db_connection();
      $albumId = $MYSQLI->real_escape_string($_REQUEST["albumId"]);
      $res = $this->delete_content("album", $albumId);
      if ($res["status_code"] == 1)
         $res["message"] = "The album has been deleted successfuly";
      else if ($res["status_code"] == 2)
         $res["message"] = "Unable to delete the album";
      else
         $res["message"] = "Album has NOT been deleted";
      return json_encode($res);
   }

   public function delete_category()
   {
      $MYSQLI = get_db_connection();
      $categoryId = $MYSQLI->real_escape_string($_REQUEST["categoryId"]);
      /* $result = $MYSQLI->query("SELECT * FROM ew_contents WHERE parent_id = '$categoryId' LIMIT 1");
        if ($result->fetch_assoc())
        {
        echo json_encode(array(status => "unable"));
        return;
        }
        $result = $MYSQLI->query("DELETE FROM ew_contents WHERE type = 'folder' AND id = '$categoryId'");
        $MYSQLI->close();
        if ($result)
        {
        echo json_encode(array(status => "success"));
        }
        else
        {
        echo json_encode(array(status => "unsuccess"));
        } */
      return json_encode($this->delete_content("folder", $categoryId));
   }

   public function delete_article($articleId)
   {
      $MYSQLI = get_db_connection();
      //$articleId = $MYSQLI->real_escape_string($_REQUEST["articleId"]);
      $result = $MYSQLI->query("DELETE FROM ew_contents WHERE id = '$articleId'");
      $MYSQLI->close();
      if ($result)
      {
         echo json_encode(array(status => "success", "message" => "tr{Article has been deleted succesfully}"));
      }
      else
      {

         return EWCore::log_error("400", "tr{Something went wrong, please try again}", $MYSQLI->error_list);
      }
   }

   public function get_documents_list($parentId, $token = null, $size = null)
   {
      $MYSQLI = get_db_connection();

      if (!isset($token))
      {
         $token = 0;
      }
      if (!$size)
      {
         $size = 99999999999999;
      }

      $result = $MYSQLI->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM content_categories WHERE parent_id = '$parentId' ORDER BY title") or die("safasfasf");
      $categories = array();
      while ($r = $result->fetch_assoc())
      {
         $r["document_type"] = "folder";
         $categories[] = $r;
      }

      $result = $MYSQLI->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents WHERE category_id = '$parentId' ORDER BY title") or die("safasfasf");
      $articles = array();
      while ($r = $result->fetch_assoc())
      {
         $r["document_type"] = "article";
         $articles[] = $r;
      }
      $documents = array_merge($categories, $articles);
      $MYSQLI->close();
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
      $MYSQLI = get_db_connection();

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

         $result = $MYSQLI->query("SELECT parent_id FROM ew_contents WHERE id = '$parent_id'") or die("safasfasf");
         while ($r = $result->fetch_assoc())
         {
            $container_id = $r["parent_id"];
         }

         $files = array();
         // Folder
         $result = $MYSQLI->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents WHERE type = 'album' AND parent_id = '$parent_id' ORDER BY title") or die("safasfasf");
         while ($r = $result->fetch_assoc())
         {
            $files[] = array(title => $r["title"], type => "folder", size => "", ext => "", "parentId" => $container_id, "id" => $r["id"]);
         }

         // images
         $result = $MYSQLI->query("SELECT *,ew_contents.id AS content_id, DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents, ew_images WHERE ew_contents.id = ew_images.content_id AND parent_id = '$parent_id' ORDER BY title") or die("safasfasf");
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
            $tumbURL = 'media' . $path . $file_info["filename"] . ".thumb." . $file_info["extension"];

            list($width, $height) = getimagesize($file_path);
            if (!file_exists($root . $path . $file_info["filename"] . ".thumb." . $file_info["extension"]) && $width > 140)
            {
               $this->create_image_thumb($file_path, 140);
               $tumbURL = 'media' . $path . $file_info["filename"] . ".thumb." . $file_info["extension"];
            }
            else if ($width <= 140)
            {
               $tumbURL = 'media' . $path . $file;
            }

            $files[] = array("id" => $r["content_id"], title => $r["title"], "parentId" => $container_id,
                type => $this->file_types[$file_info["extension"]] ? $this->file_types[$file_info["extension"]] : "unknown",
                size => round(filesize($file_path) / 1024), ext => $file_info["extension"],
                url => $file,
                filename => $file_info["filename"],
                fileExtension => $file_info["extension"],
                absUrl => EW_ROOT_URL . "media/$file",
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

   public function get_album()
   {
      $MYSQLI = get_db_connection();
      $albumId = $MYSQLI->real_escape_string($_REQUEST["albumId"]);


      $result = $MYSQLI->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents WHERE id = '$albumId'") or die($MYSQLI->error);

      if ($rows = $result->fetch_assoc())
      {
         $MYSQLI->close();

         return json_encode($rows);
      }
   }

   public function add_album($title = null)
   {
      $MYSQLI = get_db_connection();
      if (!$title)
         $title = $MYSQLI->real_escape_string($_REQUEST['title']);

      /* $parentId = $MYSQLI->real_escape_string($_REQUEST['parentId']);
        if (!$parentId)
        $parentId = 0; */
      $keywords = $MYSQLI->real_escape_string($_REQUEST['keywords']);
      $description = $MYSQLI->real_escape_string($_REQUEST['description']);
      $htmlContent = stripcslashes($_REQUEST['html_content']);

      $result = $this->add_content("album", $title, 0, $keywords, $description, $htmlContent);
      $result = json_decode($result, true);
      $res = array(status => "success", message => "The directory {" . $title . "} hase been created succesfuly");

      // $stm->close();
      //$MYSQLI->close();
      // Call plugins actions
      // End of plugins actions call
      //$root = EW_MEDIA_DIR;
      //$dir_path = $root . $path . '/';
      //$res = array();
      /* try
        {
        if (!is_dir($dir_path))
        {
        mkdir($dir_path);
        $dir_info = pathinfo($dir_path);
        $res = array(status => "success", message => "The directory {" . $dir_info["filename"] . "} hase been created succesfuly");
        }
        else
        {
        $res = array(status => "unsuccess", message => "The directory {" . $dir_info["filename"] . "} is already exist");
        }
        } catch (Exception $e)
        {
        echo $e->getMessage();
        } */
      return json_encode($res);
   }

   public function update_album()
   {
      $MYSQLI = get_db_connection();
      $albumId = $MYSQLI->real_escape_string($_REQUEST['id']);
      $title = $MYSQLI->real_escape_string($_REQUEST['title']);
      $parent_id = $MYSQLI->real_escape_string($_REQUEST['parent_id']);
      $keywords = $MYSQLI->real_escape_string($_REQUEST['keywords']);
      $description = $MYSQLI->real_escape_string($_REQUEST['description']);
      $htmlContent = stripcslashes($_REQUEST['html_content']);
      $createdModified = date('Y-m-d H:i:s');
      $stm = $MYSQLI->prepare("UPDATE ew_contents 
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
         $MYSQLI->close();

         echo json_encode(array(status => "success", title => $title));
      }
      else
      {
         echo json_encode(array(status => "unsuccess"));
      }
   }

   public function delete_content_by_id($id)
   {
      $MYSQLI = get_db_connection();

      if (!$id)
         $id = $MYSQLI->real_escape_string($_REQUEST["id"]);
      $result = $MYSQLI->query("SELECT * FROM ew_contents WHERE parent_id = '$id' LIMIT 1");
      $output = array();
      if ($result->fetch_assoc())
      {
         return json_encode(array(status => "unable", status_code => 2));
      }
      $result = $MYSQLI->query("DELETE FROM ew_contents WHERE id = '$id'");
      $MYSQLI->close();
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
      $MYSQLI = get_db_connection();
      require_once EW_ROOT_DIR . "core/upload.class.php";
      ini_set("memory_limit", "100M");
      if (isset($_REQUEST["path"]))
         $path = $_REQUEST["path"];

      if (!$parent_id)
         $parent_id = $MYSQLI->real_escape_string($_REQUEST["parentId"]);
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
         $foo = new \upload($file);
         if ($foo->uploaded)
         {

            // save uploaded image with no changes
            $foo->Process($root);
            if ($foo->processed)
            {
               $result = $this->add_content("image", $foo->file_dst_name_body, $parent_id, "", "", "");
               $result = json_decode($result, true);
               //print_r($result);
               /* $stm = $MYSQLI->prepare("INSERT INTO ew_contents (title , keywords , description , parent_id , source_page_address , html_content , ew_contents.order , date_created,type) 
                 VALUES (? , ? , ? , ? , ? , ? , ? , ?,'article')") or die($MYSQLI->error);
                 $stm->bind_param("ssssssss", $title, $keywords, $description, $categoryId, $sourcePageAddress, $htmlContent, $order, $createdDate) or die($MYSQLI->error); */
               if ($result["id"])
               {
                  $content_id = $result["id"];
                  $stm = $MYSQLI->prepare("INSERT INTO ew_images (content_id, source , alt_text) 
            VALUES (? , ? , ?)") or die($MYSQLI->error);
                  $image_path = $foo->file_dst_name;
                  $stm->bind_param("sss", $content_id, $image_path, $alt_text) or die($MYSQLI->error);
                  if ($stm->execute())
                  {
                     $res = array("status" => "success", "id" => $stm->insert_id);
                     $stm->close();
                     $MYSQLI->close();
                  }
               }

               $this->create_image_thumb($foo->file_dst_pathname, 140);
               $succeed++;
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

      return json_encode(array(status => "success", message => "Uploaded: " . $succeed . " Error: " . $error));
   }

   /**
    * 
    * @param array $form_config [optional] <p>An array that contains content form configurations.<br/>
    * the keys are: <b>title</b>, <b>saveActivity</b>, <b>updateActivity</b>, <b>data</b>
    * </p>
    * @return string
    */
   public static function get_content_form($form_config = null)
   {
      ob_start();
      include 'content-form.php';
      return ob_get_clean();
   }

}
