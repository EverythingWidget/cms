<?php

namespace admin;

use Section;
use EWCore;

/**
 * Description of ContentManagement
 *
 * @author Eeliya
 */
class WidgetsManagement extends Section
{

   private static $panel_index = 0;
   private static $widget_index = 0;
   private static $title = "";
   private static $html_scripts = array();
   private static $html_keywords;

   public function init_plugin()
   {
      //global $EW;
      $this->register_form("ew-article-form", "uis_tab", ["title" => "UI"]);
      $this->register_form("ew-category-form", "uis_tab", ["title" => "UI"]);

      //EWCore::register_action("ew-category-action-add", "WidgetsManagement.category_action_add", "category_action_update", $this);
      //EWCore::register_action("ew-category-action-update", "WidgetsManagement.category_action_update", "category_action_update", $this);
      //EWCore::register_action("ew-category-action-get", "WidgetsManagement.category_action_get", "category_action_get", $this);
      $this->add_listener("app-admin/ContentManagement/add_category", "category_action_update");
      $this->add_listener("app-admin/ContentManagement/update_category", "category_action_update");
      $this->add_listener("app-admin/ContentManagement/get_category", "category_action_get");

      //EWCore::register_action("ew-article-action-add", "WidgetsManagement.article_action_add", "article_action_update", $this);
      //EWCore::register_action("ew-article-action-update", "WidgetsManagement.article_action_update", "article_action_update", $this);
      //EWCore::register_action("ew-article-action-get", "WidgetsManagement.article_action_get", "article_action_get", $this);
      $this->add_listener("app-admin/ContentManagement/add_article", "article_action_update");
      $this->add_listener("app-admin/ContentManagement/update_article", "article_action_update");
      $this->add_listener("app-admin/ContentManagement/get_article", "article_action_get");

      $this->register_permission("export-uis", "User can export UIS", array("export_uis", "ne-uis.php_see"));
      $this->register_permission("import-uis", "User can import UIS", array("import_uis", "ne-uis.php_see"));

      //$this->register_content_label("uis", "");
   }

   public function category_action_get($_output, $_data)
   {
//echo "assssssssssssssssssss".$data["id"];
      if ($_data["id"])
      {
         $page_uis = json_decode($this->get_path_uis("/category/" . $_data["id"]), true);
         $_data["WidgetManagement_pageUisId"] = ($page_uis["id"]) ? $page_uis["id"] : "";
         $_data["WidgetManagement_name"] = ($page_uis["name"]) ? $page_uis["name"] : "Inherit/Default";
      }
      return json_encode($_data);
   }

   public function category_action_update($_output, $_data, $id, $WidgetManagement_pageUisId)
   {
      if ($WidgetManagement_pageUisId)
      {
         $this->set_uis("/category/" . $id, $WidgetManagement_pageUisId);
      }
      else
      {
         $this->set_uis("/category/" . $id);
      }
   }

   public function article_action_get($_output, $_data)
   {
      if ($_data["id"])
      {
         $page_uis = json_decode(($this->get_path_uis("/article/" . $_data["id"])), true);
         $_data["WidgetManagement_pageUisId"] = ($page_uis["id"]) ? $page_uis["id"] : "";
         $_data["WidgetManagement_name"] = ($page_uis["name"]) ? $page_uis["name"] : "Inherit/Default";
         return json_encode($_data);
      }
   }

   public function article_action_update($_output, $_data, $id, $WidgetManagement_pageUisId)
   {
      //echo $id;
      if ($WidgetManagement_pageUisId)
      {
         $this->set_uis("/article/" . $id, $WidgetManagement_pageUisId);
      }
      else
      {
         $this->set_uis("/article/" . $id);
      }
   }

   public function ew_form_uis_tab($form_config, $form_id)
   {

      ob_start();
      //echo "asdasdasdasd";
      include "uis-tab.php";
      $html = ob_get_clean();
      return json_encode(["html" => $html]);
   }

   public function get_uis_list($token = 0, $size = 99999999999999)
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

      $totalRows = $MYSQLI->query("SELECT COUNT(*)  FROM ew_ui_structures ") or die(error_reporting());
      $totalRows = $totalRows->fetch_assoc();
      $result = $MYSQLI->query("SELECT *  FROM ew_ui_structures ORDER BY name LIMIT $token,$size") or die(error_reporting());

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

   public function get_path_uis_list()
   {
      $MYSQLI = get_db_connection();

      $result = $MYSQLI->query("SELECT ew_pages_ui_structures.path,ew_ui_structures.id, ew_ui_structures.name, ew_ui_structures.template  FROM ew_pages_ui_structures,ew_ui_structures WHERE ew_pages_ui_structures.ui_structure_id = ew_ui_structures.id") or die(error_reporting());

//$out = array();
      $rows = array();
      while ($r = $result->fetch_assoc())
      {
         $rows[] = array($r["path"] . "_uisId" => $r["id"], $r["path"] => $r["name"]);
      }
      $MYSQLI->close();
      //$out = array(;
      return json_encode($rows);
   }

   public function get_all_pages_uis_list()
   {
      $MYSQLI = get_db_connection();
      $result = $MYSQLI->query("SELECT ew_pages_ui_structures.id AS id, ew_pages_ui_structures.path AS path, ew_ui_structures.name AS name FROM ew_pages_ui_structures,ew_ui_structures WHERE ew_pages_ui_structures.ui_structure_id = ew_ui_structures.id AND ew_pages_ui_structures.path LIKE '%'") or die(error_reporting());
      $rows = array();
      while ($r = $result->fetch_assoc())
      {
         $rows[] = $r;
      }
      $MYSQLI->close();
      //$out = array(;
      return json_encode(array("totalRows" => $result->num_rows, "result" => $rows));
   }

   public function add_uis($name = null, $template = null, $structure = null)
   {
      $MYSQLI = get_db_connection();

      if (!$name)
      {
         $res = array("status" => "unsuccess", "message" => "The field name is mandatory");
         $MYSQLI->close();
         return json_encode($res);
      }
      $stm = $MYSQLI->prepare("INSERT INTO ew_ui_structures(name,template,structure) VALUES (?,?,?)");
      $stm->bind_param("sss", $name, $template, $structure);
      $stm->execute();
      if ($_REQUEST['defaultUIS'] == "true")
      {
         $this->set_uis("@DEFAULT", $stm->insert_id);
      }
      if ($_REQUEST['homeUIS'] == "true")
      {
         $this->set_uis("@DEFAULT", $stm->insert_id);
      }
      $res = array("status" => "success", "uisId" => $stm->insert_id, "name" => $name);
      $stm->close();
      $MYSQLI->close();
      return json_encode($res);
   }

   public function import_uis()
   {
      $MYSQLI = get_db_connection();

      $fileContent = json_decode(file_get_contents($_FILES['uis_file']['tmp_name']), true);

      if (!$fileContent["name"])
      {
         $res = array("status" => "unsuccess", "message" => "The field name is mandatory");
         $MYSQLI->close();
         return json_encode($res);
      }
      $stm = $MYSQLI->prepare("INSERT INTO ew_ui_structures(name,template,structure) VALUES (?,?,?)");
      $stm->bind_param("sss", $fileContent["name"], $fileContent["template"], $fileContent["structure"]);
      $stm->execute();
      /* if ($_REQUEST['defaultUIS'] == "true")
        {
        $this->set_uis("@DEFAULT", $stm->insert_id);
        }
        if ($_REQUEST['homeUIS'] == "true")
        {
        $this->set_uis("@DEFAULT", $stm->insert_id);
        } */
      $res = array("status" => "success", "uisId" => $stm->insert_id, "message" => "tr{The UIS has been imported succesfully}");
      $stm->close();
      $MYSQLI->close();
      return json_encode($res);
   }

   public function export_uis($uis_id)
   {
      $MYSQLI = get_db_connection();
      $table = "ew_ui_structures";

      // load the original record into an array
      $result = $MYSQLI->query("SELECT * FROM {$table} WHERE id={$uis_id}");
      $original_record = $result->fetch_assoc();
      $name = $original_record["name"] . "-exported-" . date('Y-m-d H:i');
      $template = $original_record["template"];
      $structure = $original_record["structure"];
      $user_interface_structure = array("name" => $name, "template" => $template, "structure" => $structure);
      $file = json_encode($user_interface_structure);
      //fwrite($file, $user_interface_structure);
      //fclose($file);

      header("Cache-Control: public");
      header("Content-Description: File Transfer");
      header("Content-Length: " . strlen($file) . ";");
      header("Content-Disposition: attachment; filename=\"$name.json\"");
      header("Content-Type: application/octet-stream");
      return $file;
   }

   public function clone_uis($id = null)
   {
      $MYSQLI = get_db_connection();
      $table = "ew_ui_structures";

      // load the original record into an array
      $result = $MYSQLI->query("SELECT * FROM {$table} WHERE id={$id}");
      $original_record = $result->fetch_assoc();
      $name = $original_record["name"] . " - clone";
      $template = $original_record["template"];
      $structure = $original_record["structure"];
      //$this->add_uis($name);
      // insert the new record and get the new auto_increment id
      /* $MYSQLI->query("INSERT INTO {$table} (`{$id_field}`) VALUES (NULL)");
        $newid = $MYSQLI->insert_id;

        // generate the query to update the new record with the previous values
        $query = "UPDATE {$table} SET ";
        foreach ($original_record as $key => $value)
        {
        if ($key != $id_field)
        {
        $query .= '`' . $key . '` = "' . str_replace('"', '\"', $value) . '", ';
        }
        }
        $query = substr($query, 0, strlen($query) - 2); // lop off the extra trailing comma
        $query .= " WHERE {$id_field}={$newid}";
        $MYSQLI->query($query); */

      // return the new id
      $res = $this->add_uis($name, $template, $structure);
      return $res;
   }

   public function update_uis($uisId = null, $name = null, $template = null, $perview_url = null, $structure = null)
   {
      $MYSQLI = get_db_connection();

      if (!$name)
      {
         $res = array("status" => "unsuccess", "message" => "The field name is mandatory");
         $MYSQLI->close();
         return json_encode($res);
      }
      $stm = $MYSQLI->prepare("UPDATE ew_ui_structures SET name = ?, template= ?, perview_url = ?, structure = ? WHERE id = ?") or die($MYSQLI->error);
      $stm->bind_param("sssss", $name, $template, $perview_url, $structure, $uisId);
      $error = $MYSQLI->errno;
      if ($stm->execute())
      {
         if ($_REQUEST['defaultUIS'] == "true")
         {
            $this->set_uis("@DEFAULT", $uisId);
         }
         if ($_REQUEST['homeUIS'] == "true")
         {
            $this->set_uis("@DEFAULT", $uisId);
         }
         $stm->close();
         $MYSQLI->close();
         echo json_encode(array(status => "success","message"=>"tr{The layout has been saved successfully}", "data"=>[title => $name]));
      }
      else
      {
         echo json_encode(array(status => "unsuccess", message => $error));
      }
   }

   public function get_uis($uisId = null)
   {
      $MYSQLI = get_db_connection();

      if (!$uisId)
         return;
      $result = $MYSQLI->query("SELECT * FROM ew_ui_structures WHERE id = '$uisId'") or die(null);
      $default_uis = json_decode($this->get_path_uis("@DEFAULT"), true);
      $home_uis = json_decode($this->get_path_uis("@HOME_PAGE"), true);

      if ($rows = $result->fetch_assoc())
      {
         if ($default_uis["id"] == $uisId)
            $rows["uis-default"] = "true";
         if ($home_uis["id"] == $uisId)
            $rows["uis-home-page"] = "true";
//$rows["structure"] = stripslashes($rows["structure"]);
         $MYSQLI->close();
         return json_encode($rows);
      }
      else
      {
         return null;
      }
   }

   public function delete_uis($uisId)
   {
      $MYSQLI = get_db_connection();

      $result = $MYSQLI->query("DELETE FROM ew_ui_structures WHERE id = '$uisId'");
      $MYSQLI->close();
      if ($result)
      {
         echo json_encode(array(status => "success"));
      }
      else
      {
         echo json_encode(array(status => "unsuccess"));
      }
   }

   public function add_panel($uisId = null, $styleId = null, $styleClass = null)
   {
      $MYSQLI = get_db_connection();

      $parameters = $MYSQLI->real_escape_string($_REQUEST["parameters"]);
      $container_id = $MYSQLI->real_escape_string($_REQUEST["containerId"]);

      if (!$uisId)
      {
         $res = array("status" => "unsuccess", "message" => "The field UIS ID is mandatory");
         $MYSQLI->close();
         return json_encode($res);
      }
      $stm = $MYSQLI->prepare("INSERT INTO ui_structures_parts (ui_structure_id
  , item_type ,style_id, style_class,widgets_parameters,container_id,ui_structures_parts.order)
  SELECT ? , 'panel' , ? , ?, ? ,?,  count(*) FROM ui_structures_parts WHERE item_type = 'panel' AND ui_structure_id = $uisId") or die($MYSQLI->error);
      $stm->bind_param("sssss", $uisId, $styleId, $styleClass, $parameters, $container_id);
      if ($stm->execute())
      {
         $res = array("status" => "success", message => "Panel has been added successfully", "uisId" => $stm->insert_id);
      }
      else
      {
         $res = array("status" => "error", message => "Panel has NOT been added, Please try again");
      }
      $stm->close();
      $MYSQLI->close();
      return json_encode($res);
   }

   public function update_panel()
   {
      $MYSQLI = get_db_connection();

      $panelId = $MYSQLI->real_escape_string($_REQUEST['panelId']);
      $styleId = $MYSQLI->real_escape_string($_REQUEST['styleId']);
      $styleClass = $MYSQLI->real_escape_string($_REQUEST['styleClass']);
      $parameters = $MYSQLI->real_escape_string($_REQUEST["parameters"]);
      if (!$panelId)
      {
         $res = array("status" => "unsuccess", "message" => "The field Panel ID is mandatory");
         $MYSQLI->close();
         return json_encode($res);
      }
      $stm = $MYSQLI->prepare("UPDATE ui_structures_parts SET style_id = ?, style_class = ? , widgets_parameters = ? WHERE id = ?");
      $stm->bind_param("ssss", $styleId, $styleClass, $parameters, $panelId);
      if ($stm->execute())
      {
         $res = array("status" => "success", message => "Panel has been updated successfully");
      }
      else
      {
         $res = array("status" => "error", message => "Panel has NOT been updated, Please try again");
      }
      $stm->close();
      $MYSQLI->close();
      return json_encode($res);
   }

   public static function create_panel_content($panel = array(), $container_id)
   {
      $result_html = '';
      foreach ($panel as $key => $value)
      {
//echo $value["type"];
         if ($value["type"] == "panel")
         {
//echo $value["type"] . " - " . $value["class"] . "<br>";
            self::$panel_index++;
            $result_html.=self::open_panel("panel-" . self::$panel_index, $container_id, $value["class"], $value["id"], $value["panelParameters"], FALSE);
            $result_html.=self::create_panel_content($value["children"], "panel-" . self::$panel_index);
            $result_html.=self::close_panel();
         }
         else
         {
            self::$widget_index++;
            $result_html.=self::open_widget("widget-" . self::$widget_index, $value["widgetType"], $value["class"], $value["widgetClass"], $value["id"], $value["widgetParameters"]);
            $result_html.=self::close_widget();

//echo $value["type"] . " - " . $value["class"] . "<br>";
         }
      }
      return $result_html;
   }

   public static function open_panel($panel_id, $container_id, $style_class, $style_id, $parameters, $row = TRUE, $block_name)
   {
      $result_html = '';
      $param_json = $parameters;
      $parameters = json_decode($parameters, TRUE);
//$default_class = "panel ";
      // Check if width has been set and add value to the style
      //echo $panel_parameters["width-opt"];
      $style = $parameters["width-opt"] ? "width:" . $parameters["width"] . ";" : "";

      // Check if margin has been set and add value to the style
      $style .= $parameters["margin-opt"] ? "margin:" . $parameters["margin"] . ";" : "";

      // Check if padding has been set and add value to the style
      $style .= $parameters["padding-opt"] ? "padding:" . $parameters["padding"] . ";" : "";

      if ($style_id)
         $style_id_text = "id='$style_id'";

      if ($block_name)
      {
         require_once EW_TEMPLATES_DIR . "/blocks/" . $block_name . ".php";
         $block_name::initiate();
      }
//if ($row)
//$default_class = "row";
      $result_html.= "<div class='panel $style_class'  $style_id_text  data-panel-id='$panel_id'  data-container-id='$container_id'  style='$style' data-panel-parameters='" . stripcslashes($param_json) . "' data-block-name='$block_name'><div class='row'>";

//echo '<div class="wrapper">';

      if ($parameters["title"] && $parameters["title"] != "none")
      {
         $result_html.= "<div class='col-xs-12 panel-header'><{$parameters["title"]}>" . $parameters["title-text"] . "</{$parameters["title"]}></div>";
      }
//echo '</div>';
      //
      return $result_html;
   }

   public static function close_panel()
   {
      return '</div></div>';
   }

   public static function open_block($panel_id, $container_id, $style_class, $style_id, $parameters, $row = TRUE, $block_name)
   {
      $result_html = '';
      $param_json = $parameters;
      $parameters = json_decode($parameters, TRUE);
//$default_class = "panel ";
      // Check if width has been set and add value to the style
      //echo $panel_parameters["width-opt"];
      $style = $parameters["width-opt"] ? "width:" . $parameters["width"] . ";" : "";

      // Check if margin has been set and add value to the style
      $style .= $parameters["margin-opt"] ? "margin:" . $parameters["margin"] . ";" : "";

      // Check if padding has been set and add value to the style
      $style .= $parameters["padding-opt"] ? "padding:" . $parameters["padding"] . ";" : "";

      if ($style_id)
         $style_id_text = "id='$style_id'";

      if ($block_name)
      {
         require_once EW_TEMPLATES_DIR . "/blocks/" . $block_name . ".php";
         $block_name::initiate();
      }
      $result_html.= "<div class='panel $style_class'  $style_id_text  data-panel-id='$panel_id'  data-container-id='$container_id'  style='$style' data-panel-parameters='" . stripcslashes($param_json) . "' data-block-name='$block_name'>";
      /* if ($parameters["title"] && $parameters["title"] != "none")
        {
        $result_html.= "<div class='col-xs-12 panel-header'><{$parameters["title"]}>" . $parameters["title-text"] . "</{$parameters["title"]}></div>";
        } */
      return $result_html;
   }

   public static function close_block()
   {
      return '</div>';
   }

   private static $widget_style_class;

   /**
    * Set style class for the widget which is currently being initialized
    * 
    * @param String $class class name
    */
   public static function set_widget_style_class($class)
   {
      self::$widget_style_class.="$class ";
   }

   public static function get_widget_style_class()
   {
      return self::$widget_style_class;
   }

   /**
    * Create a widget element
    * 
    * @param type $widget_id
    * @param type $widget_type
    * @param string $style_class widget container style classes
    * @param string $widget_style_class widget style classes
    * @param string  $style_id widget style id
    * @param json $params widget parameters
    */
   public static function open_widget($widget_id, $widget_type, $style_class, $widget_style_class, $style_id, $params)
   {
      $result_html = '';
      if ($style_id)
         $WIDGET_STYLE_ID = "id='$style_id'";
      $widget_parameters = json_decode(($params), TRUE);

      // Include widget content
      ob_start();
      include EW_WIDGETS_DIR . '/' . $widget_type . '/index.php';
      $widget_content = ob_get_clean();

      // Add widget style class which specified with UIS editor to the widget
      self::set_widget_style_class($widget_style_class);
      $WIDGET_STYLE_CLASS = self::get_widget_style_class();

      $result_html.= "<div class='widget-container $style_class' >";
      $result_html.= "<div class='widget $WIDGET_STYLE_CLASS' $WIDGET_STYLE_ID data-widget-id='$widget_id' data-widget-parameters='" . ($params) . "' data-widget-type='$widget_type'>";
      $result_html.= $widget_content;
      self::$widget_style_class = "";
      return $result_html;
   }

   public static function close_widget()
   {
      return '</div></div>';
   }

   public function create_widget($widget_id, $widget_type, $style_class, $widget_style_class, $style_id, $widget_parameters)
   {
      //echo (stripcslashes($widget_parameters));
      //$widget_parameters = html_entity_decode($widget_parameters);
      $widget_html = '';
      $widget_html .=self::open_widget($widget_id, $widget_type, $style_class, $widget_style_class, $style_id, stripcslashes($widget_parameters));
      $widget_html .=self::close_widget();
      return $widget_html;
   }

   public function get_widget($widgetId)
   {
      $MYSQLI = get_db_connection();
      if (!isset($widgetId))
         $widgetId = $MYSQLI->real_escape_string($_REQUEST["wId"]);
      if (!$widgetId)
         return;
      $result = $MYSQLI->query("SELECT * FROM ui_structures_parts WHERE id = '$widgetId'") or die(null);

      if ($rows = $result->fetch_assoc())
      {
         $MYSQLI->close();
         return json_encode($rows);
      }
      else
      {
         return null;
      }
   }

   public function update_widget($widgetId = null, $widgetType = null, $widgetParameters = null, $styleId = null, $styleClass = null, $style = null)
   {
      $MYSQLI = get_db_connection();

      $stm = $MYSQLI->prepare("UPDATE ui_structures_parts SET  widget_type = ?, widgets_parameters = ? ,style_id = ?, style_class= ?, style = ? 
            WHERE id = ?") or die($MYSQLI->error);
      $stm->bind_param("ssssss", $widgetType, $widgetParameters, $styleId, $styleClass, $style, $widgetId);

      if ($stm->execute())
      {
         $res = array("status" => "success", message => "Widget has been updated successfully", "widgetType" => $widgetType);
      }
      else
      {
         $res = array("status" => "error", message => "Widget has NOT been updated, Please try again");
      }
      $stm->close();
      $MYSQLI->close();
      return json_encode($res);
   }

   public function get_blocks()
   {
      $path = EW_TEMPLATES_DIR . '/blocks/';

      $apps_dirs = opendir($path);
      $apps = array();
      $count = 0;
      while ($block_files = readdir($apps_dirs))
      {
         if (strpos($block_files, '.') === 0)
            continue;

         $title = null;
         $description = "";

         $title = EWCore::get_comment_parameter("title", $path . $block_files);
         $description = EWCore::get_comment_parameter("description", $path . $block_files);

         $count++;
         $apps[] = array("name" => substr($block_files, 0, stripos($block_files, ".")), "path" => $block_files, "title" => ($title) ? $title : $block_files, "description" => $description);
      }
      $out = array("totalRows" => $count, "result" => $apps);
      return json_encode($out);
   }

   public function get_widgets_types()
   {
      $path = EW_WIDGETS_DIR . '/';

      $apps_dirs = opendir($path);
      $apps = array();
      $count = 0;
      while ($widget_dir = readdir($apps_dirs))
      {
         if (strpos($widget_dir, '.') === 0)
            continue;

         $title = null;
         $description = "";
         //print_r($tokens);
         $title = EWCore::get_comment_parameter("title", $path . $widget_dir . '/admin.php');
         $description = EWCore::get_comment_parameter("description", $path . $widget_dir . '/admin.php');

         $count++;
         $apps[] = array("name" => $widget_dir, "path" => $widget_dir, "title" => ($title) ? $title : $widget_dir, "description" => $description);
      }
      $out = array("totalRows" => $count, "result" => $apps);
      return json_encode($out);
   }

   function get_widget_cp($widgetName = null)
   {
      ob_start();
      echo '<form id="uis-widget" onsubmit="return false;">';
      if ($widgetName)
      {
         include EW_WIDGETS_DIR . '/' . $widgetName . '/admin.php';
      }
      if (function_exists("get_content"))
         echo get_content();
      echo '</form>';
      return ob_get_clean();
   }

   function get_widget_cp_full()
   {
      $widgetName = $_REQUEST["widgetName"];
      ob_start();
      echo '<form id="uis-widget" onsubmit="return false;">';
      include EW_WIDGETS_DIR . '/' . $widgetName . '/admin.php';
      if (function_exists("get_content"))
         echo get_content();
      echo '</form>';
      if (function_exists("get_script"))
         echo get_script();
      echo ob_get_clean();
   }

   public function add_to_panel($panelId = null, $widgetType = null, $widgetParameters = null, $uisId = null, $styleId = null, $styleClass = null, $style = null)
   {
      $MYSQLI = get_db_connection();

      $stm = $MYSQLI->prepare("INSERT INTO ui_structures_parts (ui_structure_id , item_type, widget_type, widgets_parameters , container_id ,style_id,style_class,style, ui_structures_parts.order) 
            SELECT ?,'widget',?,?,?,?,?,?,count(*) FROM ui_structures_parts WHERE container_id = $panelId") or die($MYSQLI->error);
      $stm->bind_param("sssssss", $uisId, $widgetType, $widgetParameters, $panelId, $styleId, $styleClass, $style);

      if ($stm->execute())
      {
         $res = array("status" => "success", message => "Widget has been added successfully", "wId" => $stm->insert_id, "widgetType" => $widgetType);
      }
      else
      {
         $res = array("status" => "error", message => "Widget has NOT been added, Please try again");
      }
      $stm->close();
      $MYSQLI->close();
      return json_encode($res);
   }

   public function remove_from_panel()
   {
      $MYSQLI = get_db_connection();
      $uisId = $MYSQLI->real_escape_string($_REQUEST['uisId']);
      $widgetId = $MYSQLI->real_escape_string($_REQUEST['widgetId']);

      $stm = $MYSQLI->prepare("DELETE FROM ui_structures_parts WHERE id = ? AND ui_structure_id = ? AND item_type = 'widget'") or die($MYSQLI->error);
      $stm->bind_param("ss", $widgetId, $uisId);

      if ($stm->execute())
      {
         $res = array("status" => "success", message => "Widget has been removed successfully",);
      }
      else
      {
         $res = array("status" => "error", message => "Widget has NOT been removed, Please try again");
      }
      return json_encode($res);
   }

   public function remove_panel()
   {
      $MYSQLI = get_db_connection();
      $uisId = $MYSQLI->real_escape_string($_REQUEST['uisId']);
      $widgetId = $MYSQLI->real_escape_string($_REQUEST['panelId']);

      $stm = $MYSQLI->prepare("DELETE FROM ui_structures_parts WHERE (id = ? OR container_id = ?) AND ui_structure_id = ?") or die($MYSQLI->error);
      $stm->bind_param("sss", $widgetId, $widgetId, $uisId);

      if ($stm->execute())
      {
         $res = array("status" => "success", message => "Panel has been removed successfully",);
      }
      else
      {
         $res = array("status" => "error", message => "Panel has NOT been removed, Please try again");
      }
      return json_encode($res);
   }

   public static function generate_view($uisId)
   {
      $RESULT_HTML = '';
      $MYSQLI = \EWCore::get_db_connection();

      $panels = $MYSQLI->query("SELECT * FROM ew_ui_structures WHERE id = '$uisId' ") or die($MYSQLI->error);

      self::$panel_index = 0;
      self::$widget_index = 0;
      while ($rows = $panels->fetch_assoc())
      {
         $res = json_decode(stripcslashes($rows["structure"]), true);
      }
      //ob_start();
      foreach ($res as $key => $value)
      {
         $RESULT_HTML.=self::open_block("panel-" . self::$panel_index, "", "block " . $value["class"], $value["id"], $value["panelParameters"], FALSE, $value["blockName"]);
         $RESULT_HTML.=self::create_panel_content($value["children"], "panel-" . self::$panel_index);
         $RESULT_HTML.=self::close_block();
         self::$panel_index++;
      }
      //$html = ob_get_clean();
      return $RESULT_HTML;
   }

   public static function get_html_styles()
   {
      return "";
   }

   public static function add_html_script($src, $script)
   {
      self::$html_scripts[] = array("src" => $src, "script" => $script);
   }

   public static function get_html_scripts()
   {
      $result = "";
      foreach (self::$html_scripts as $script)
      {
         if ($script["src"])
            $result.="<script src='{$script["src"]}'>{$script["script"]}</script>";
         else if ($script["script"])
            $result.=$script["script"];
      }
      return $result;
   }

   public static function set_html_title($title)
   {
      self::$title = $title;
   }

   public static function get_html_title()
   {
      return self::$title;
   }

   public static function set_html_keywords($keywords)
   {
      self::$html_keywords .= $keywords . ", ";
   }

   public static function get_html_keywords($keywords)
   {
      return self::$html_keywords;
   }

   public function show_container($container_id)
   {
      $MYSQLI = get_db_connection();
      $items = $MYSQLI->query("SELECT * FROM ui_structures_parts WHERE container_id = '$container_id'  ORDER BY ui_structures_parts.order") or die($MYSQLI->error);

      while ($rows = $items->fetch_assoc())
      {
         if ($rows["item_type"] == 'panel')
         {
            $this->open_panel($rows["id"], $rows["container_id"], $rows["style_class"], $rows["style_id"], $rows["widgets_parameters"], FALSE);
            $this->show_container($rows["id"]);
            $this->close_panel();
         }
         else if ($rows["item_type"] == 'widget')
         {
            $this->open_widget($rows["id"], $rows["widget_type"], $rows["style_class"], $rows["style_id"], $rows["widgets_parameters"]);
            $this->close_widget();
         }
      }
   }

   public function set_uis($path = null, $uis_id = null)
   {
      $path = ($path) ? $path : $_REQUEST["path"];
      $uis_id = ($uis_id) ? $uis_id : $_REQUEST["uisId"];
      $MYSQLI = get_db_connection();
      $res = array("status" => "success", message => "UIS has been set successfully for $path");
      if (!$uis_id)
      {
         $result = $MYSQLI->query("DELETE FROM ew_pages_ui_structures WHERE path = '$path'");
         if ($result)
         {
            return json_encode($res);
         }
      }
      $MYSQLI->query("SELECT * FROM ew_pages_ui_structures WHERE path = '$path'") or die($MYSQLI->error);


      if ($MYSQLI->affected_rows == 0)
      {
         $stm = $MYSQLI->prepare("INSERT INTO ew_pages_ui_structures(path ,ui_structure_id ) VALUES(?,?)") or die($MYSQLI->error);
         $stm->bind_param("ss", $path, $uis_id);
         if ($stm->execute())
            $res = array("status" => "success", message => "UIS has been set successfully for $path ", "puisId" => $stm->insert_id);
         else
            $res = array("status" => "error", message => "UIS has NOT been sat, Please try again");
      }
      else
      {
         $stm = $MYSQLI->prepare("UPDATE ew_pages_ui_structures SET  ui_structure_id = ?  WHERE path = ?") or die($MYSQLI->error);
         $stm->bind_param("ss", $uis_id, $path);
         if (!$stm->execute())
            $res = array("status" => "error", message => "UIS has NOT been sat, Please try again");
      }

      $stm->close();
      $MYSQLI->close();
      return json_encode($res);
   }

   public function get_path_uis($path = null)
   {
      $path = ($path) ? $path : $_REQUEST["path"];
      $MYSQLI = get_db_connection();
      $result = $MYSQLI->query("SELECT ew_ui_structures.id AS id,name,template,path FROM ew_pages_ui_structures,ew_ui_structures WHERE ew_pages_ui_structures.ui_structure_id = ew_ui_structures.id AND path = '$path'") or die($MYSQLI->error);
      if ($rows = $result->fetch_assoc())
      {
         $MYSQLI->close();
         return json_encode($rows);
      }
      else
      {
         return null;
      }
   }

   public function get_title()
   {
      return "Widgets";
   }

   public function get_description()
   {
      return "Manage the layouts of pages, add or remove widgets";
   }

}
