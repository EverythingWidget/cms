<?php

namespace webroot;

use Module;
use EWCore;

/**
 * Description of ContentManagement
 *
 * @author Eeliya
 */
class WidgetsManagement extends \ew\Module
{

   private static $panel_index = 0;
   private static $widget_index = 0;
   private static $ui_index = 0;
   private static $current_timestamp = 0;
   private static $title = "";
   private static $html_scripts = array();
   private static $html_keywords;
   private static $widgets_feeders = array();
   protected $resource = "api";
   public static $WIDGET_FEEDER = "ew-widget-feeder";
   private static $registry = [];
   private $link_chooser_form = null;

   protected function install_assets()
   {
      EWCore::register_app("widgets-management", $this);
      ob_start();
      include EW_PACKAGES_DIR . '/webroot/html/widgets-management/link-chooser-uis.php';
      $this->link_chooser_form = ob_get_clean();
   }

   protected function install_permissions()
   {
      EWCore::register_form("ew-link-chooser-form-default", "link-chooser-uis-list", ["title" => "UI Structures",
          "content" => $this->link_chooser_form]);

      $this->register_permission("view", "User can view the widgets section", array(
          "api/get_uis",
          "api/get_uis_list",
          "api/get_widgets_types",
          "api/get_all_pages_uis_list",
          "api/get_path_uis",
          "api/get_template_settings_form",
          "api/get_layout",
          "api/get_templates",
          "api/create_widget",
          "api/update_uis",
          "api/ew_form_uis_tab",
          'html/' . $this->get_index()));

      $this->register_permission("manipulate", "User can create and edit layouts", array(
          "api/get_uis",
          "api/get_uis_list",
          "api/get_widgets_types",
          "api/get_all_pages_uis_list",
          "api/get_path_uis",
          "api/get_template_settings_form",
          "api/get_layout",
          "api/get_templates",
          "api/create_widget",
          "api/update_uis",
          "api/ew_form_uis_tab",
          "api/delete_uis",
          'html/' . $this->get_index()));

      $this->register_form("ew-article-form-tab", "uis-tab", ["title" => "UI"]);
      $this->register_form("ew-category-form-tab", "uis-tab", ["title" => "UI"]);

      //EWCore::register_action("ew-category-action-add", "WidgetsManagement.category_action_add", "category_action_update", $this);
      //EWCore::register_action("ew-category-action-update", "WidgetsManagement.category_action_update", "category_action_update", $this);
      //EWCore::register_action("ew-category-action-get", "WidgetsManagement.category_action_get", "category_action_get", $this);
      $this->add_listener("admin/api/ContentManagement/add_category", "category_action_update");
      $this->add_listener("admin/api/ContentManagement/update_category", "category_action_update");
      $this->add_listener("admin/api/ContentManagement/get_category", "category_action_get");

      //EWCore::register_action("ew-article-action-add", "WidgetsManagement.article_action_add", "article_action_update", $this);
      //EWCore::register_action("ew-article-action-update", "WidgetsManagement.article_action_update", "article_action_update", $this);
      //EWCore::register_action("ew-article-action-get", "WidgetsManagement.article_action_get", "article_action_get", $this);
      $this->add_listener("admin/api/ContentManagement/add_article", "article_action_update");
      $this->add_listener("admin/api/ContentManagement/update_article", "article_action_update");
      $this->add_listener("admin/api/ContentManagement/get_article", "article_action_get");

      //$this->add_listener("admin/api/UsersManagement/update_user", "article_action_get");

      $this->register_permission("export-uis", "User can export UIS", array(
          "api/export_uis",
          "html/ne-uis.php_see"));
      $this->register_permission("import-uis", "User can import UIS", array(
          "api/import_uis",
          "html/ne-uis.php_see"));

      //$this->register_content_label("uis", "");
   }

   public function get_templates()
   {
      $path = EW_PACKAGES_DIR . '/rm/public/templates/';

      $apps_dirs = opendir($path);
      $apps = array();

      while ($template_dir = readdir($apps_dirs))
      {
         if (strpos($template_dir, '.') === 0)
            continue;

         if (!is_dir($path . $template_dir))
            continue;

         $template_dir_content = opendir($path . $template_dir);

         while ($file = readdir($template_dir_content))
         {

            if (strpos($file, '.') === 0)
               continue;
            //$i = strpos($file, 'template.css');

            if ($file == 'template.css')
            {
               $apps[] = array(
                   "templateName" => $template_dir,
                   "templatePath" => "templates/" . $template_dir);
            }
         }
      }
      return json_encode($apps);
   }

   public function category_action_get($_data)
   {
//echo "assssssssssssssssssss".$data["id"];
      if ($_data["id"])
      {
         $page_uis = json_decode($this->get_path_uis("/folder/" . $_data["id"]), true);
         $_data["WidgetManagement_pageUisId"] = ($page_uis["id"]) ? $page_uis["id"] : "";
         $_data["WidgetManagement_name"] = ($page_uis["name"]) ? $page_uis["name"] : "Inherit/Default";
      }
      return json_encode($_data);
   }

   public function category_action_update($_input, $id, $WidgetManagement_pageUisId)
   {
      if ($WidgetManagement_pageUisId)
      {
         $this->set_uis("/folder/" . $id, $WidgetManagement_pageUisId);
      }
      else
      {
         $this->set_uis("/folder/" . $id);
      }
   }

   public function article_action_get($_input, $_language)
   {
      if ($_input["id"])
      {
         //echo $_data;
         $page_uis = json_decode(($this->get_path_uis("/article/" . $_input["id"])), true);
         $_input["WidgetManagement_pageUisId"] = ($page_uis["id"]) ? $page_uis["id"] : "";
         $_input["WidgetManagement_name"] = ($page_uis["name"]) ? $page_uis["name"] : "Inherit/Default";
         //return $_input;
      }
      //return $_input;
      //return $_input;
   }

   public function article_action_update($_data, $id, $WidgetManagement_pageUisId)
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

   public function ew_form_uis_tab($form_config)
   {
      return json_encode(["html" => $this->get_app()->get_view('html/widgets-management/uis-tab.php', $form_config)]);
   }

   public function get_uis_list($token = 0, $size = 99999999999999)
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

      $totalRows = $db->query("SELECT COUNT(*)  FROM ew_ui_structures ") or die(error_reporting());
      $totalRows = $totalRows->fetch_assoc();
      $result = $db->query("SELECT *  FROM ew_ui_structures ORDER BY name LIMIT $token,$size") or die(error_reporting());

//$out = array();
      $rows = array();
      while ($r = $result->fetch_assoc())
      {
         $rows[] = $r;
      }
      $db->close();
      $out = array(
          "totalRows" => $totalRows['COUNT(*)'],
          "result" => $rows);
      return ($out);
   }

   public function get_path_uis_list()
   {
      $db = \EWCore::get_db_connection();

      $result = $db->query("SELECT ew_pages_ui_structures.path,ew_ui_structures.id, ew_ui_structures.name, ew_ui_structures.template  FROM ew_pages_ui_structures,ew_ui_structures WHERE ew_pages_ui_structures.ui_structure_id = ew_ui_structures.id") or die(error_reporting());

//$out = array();
      $rows = array();
      while ($r = $result->fetch_assoc())
      {
         $rows[] = array(
             $r["path"] . "_uisId" => $r["id"],
             $r["path"] => $r["name"]);
      }
      $db->close();
      //$out = array(;
      return json_encode($rows);
   }

   public function get_all_pages_uis_list()
   {
      $db = \EWCore::get_db_connection();
      $result = $db->query("SELECT ew_pages_ui_structures.id AS id, ew_pages_ui_structures.path AS path, ew_ui_structures.name AS name FROM ew_pages_ui_structures,ew_ui_structures WHERE ew_pages_ui_structures.ui_structure_id = ew_ui_structures.id AND ew_pages_ui_structures.path LIKE '%'") or die(error_reporting());
      $rows = array();
      while ($r = $result->fetch_assoc())
      {
         $rows[] = $r;
      }
      $db->close();
      //$out = array(;
      return json_encode(array(
          "totalRows" => $result->num_rows,
          "result" => $rows));
   }

   public function add_uis($name = null, $template = null, $template_settings = null, $structure = null)
   {
      $db = \EWCore::get_db_connection();

      if (!$name)
      {
         $res = array(
             "status" => "unsuccess",
             "message" => "The field name is mandatory");
         $db->close();
         return json_encode($res);
      }
      $stm = $db->prepare("INSERT INTO ew_ui_structures(name,template,template_settings,structure) VALUES (?,?,?,?)");
      $stm->bind_param("ssss", $name, $template, ($template_settings), ($structure));
      $stm->execute();
      if ($_REQUEST['defaultUIS'] == "true")
      {
         $this->set_uis("@DEFAULT", $stm->insert_id);
      }
      if ($_REQUEST['homeUIS'] == "true")
      {
         $this->set_uis("@DEFAULT", $stm->insert_id);
      }
      $res = array(
          "status" => "success",
          "uisId" => $stm->insert_id,
          "name" => $name);
      $stm->close();
      $db->close();
      return json_encode($res);
   }

   public function import_uis()
   {
      $db = \EWCore::get_db_connection();

      $fileContent = json_decode(file_get_contents($_FILES['uis_file']['tmp_name']), true);

      if (!$fileContent["name"])
      {
         $res = array(
             "status" => "unsuccess",
             "message" => "The field name is mandatory");
         $db->close();
         return json_encode($res);
      }
      $stm = $db->prepare("INSERT INTO ew_ui_structures(name,template, template_settings,structure) VALUES (?,?,?,?)");
      $stm->bind_param("ssss", $fileContent["name"], $fileContent["template"], $fileContent["template_settings"], $fileContent["structure"]);
      $stm->execute();
      /* if ($_REQUEST['defaultUIS'] == "true")
        {
        $this->set_uis("@DEFAULT", $stm->insert_id);
        }
        if ($_REQUEST['homeUIS'] == "true")
        {
        $this->set_uis("@DEFAULT", $stm->insert_id);
        } */
      $res = array(
          "status" => "success",
          "uisId" => $stm->insert_id,
          "message" => "tr{The UIS has been imported succesfully}");
      $stm->close();
      $db->close();
      return json_encode($res);
   }

   public function export_uis($uis_id)
   {
      $db = \EWCore::get_db_connection();
      $table = "ew_ui_structures";
      if (!$uis_id)
         return \EWCore::log_error(400, "Please specify layout ID");

      // load the original record into an array
      $result = $db->query("SELECT * FROM {$table} WHERE id={$uis_id}");
      if (!$result)
         return \EWCore::log_error(400, "Layout not found");

      $original_record = $result->fetch_assoc();
      $name = $original_record["name"] . "-exported-" . date('Y-m-d H:i');
      $template = $original_record["template"];
      $structure = $original_record["structure"];
      $template_settings = $original_record["template_settings"];
      $user_interface_structure = array(
          "name" => $name,
          "template" => $template,
          "template_settings" => $template_settings,
          "structure" => $structure);
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

   public function clone_uis($uisId = null)
   {
      $db = \EWCore::get_db_connection();
      $table = "ew_ui_structures";

      // load the original record into an array
      $result = $db->query("SELECT * FROM $table WHERE id=$uisId");
      //print_r($id);
      $original_record = $result->fetch_assoc();
      $name = $original_record["name"] . " - clone";
      $template = $original_record["template"];
      $template_settings = $original_record["template_settings"];
      $structure = ($original_record["structure"]);
      //$this->add_uis($name);
      // insert the new record and get the new auto_increment id
      /* $db->query("INSERT INTO {$table} (`{$id_field}`) VALUES (NULL)");
        $newid = $db->insert_id;

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
        $db->query($query); */

      // return the new id
      $res = $this->add_uis($name, $template, $template_settings, $structure);
      return $res;
   }

   public function update_uis($uisId = null, $name = null, $template = null, $template_settings = null, $perview_url = null, $structure = null)
   {
      $db = \EWCore::get_db_connection();

      if (!$name)
      {
         $res = array(
             "status" => "unsuccess",
             "message" => "The field name is mandatory");
         $db->close();
         return json_encode($res);
      }
      $stm = $db->prepare("UPDATE ew_ui_structures SET name = ?, template= ?, template_settings= ?, perview_url = ?, structure = ? WHERE id = ?") or die($db->error);
      $stm->bind_param("ssssss", $name, $template, ($template_settings), $perview_url, ($structure), $uisId);
      $error = $db->errno;
      if ($stm->execute())
      {
         if ($_REQUEST['defaultUIS'] == "true")
         {
            $this->set_uis("@DEFAULT", $uisId);
         }
         if ($_REQUEST['homeUIS'] == "true")
         {
            $this->set_uis("@HOME_PAGE", $uisId);
         }
         $stm->close();
         $db->close();
         echo json_encode(array(
             status => "success",
             "message" => "tr{The layout has been saved successfully}",
             "data" => [title => $name]));
      }
      else
      {
         echo json_encode(array(
             status => "unsuccess",
             message => $error));
      }
   }

   public static function get_uis($uisId = null)
   {

      $db = \EWCore::get_db_connection();

      if (!$uisId)
         return;
      $result = $db->query("SELECT * FROM ew_ui_structures WHERE id = '$uisId'") or die(null);
      $default_uis = json_decode(WidgetsManagement::get_path_uis("@DEFAULT"), true);
      $home_uis = json_decode(WidgetsManagement::get_path_uis("@HOME_PAGE"), true);

      if ($rows = $result->fetch_assoc())
      {
         if ($default_uis["id"] == $uisId)
            $rows["uis-default"] = "true";
         if ($home_uis["id"] == $uisId)
            $rows["uis-home-page"] = "true";
         return json_encode($rows);
      }
      else
      {
         return null;
      }
   }

   public function delete_uis($uisId)
   {
      $db = \EWCore::get_db_connection();

      $result = $db->query("DELETE FROM ew_ui_structures WHERE id = '$uisId'");
      $db->close();
      if ($result)
      {
         echo json_encode(array(
             status => "success"));
      }
      else
      {
         echo json_encode(array(
             status => "unsuccess"));
      }
   }

   public function add_panel($uisId = null, $styleId = null, $styleClass = null)
   {
      $db = \EWCore::get_db_connection();

      $parameters = $db->real_escape_string($_REQUEST["parameters"]);
      $container_id = $db->real_escape_string($_REQUEST["containerId"]);

      if (!$uisId)
      {
         $res = array(
             "status" => "unsuccess",
             "message" => "The field UIS ID is mandatory");
         $db->close();
         return json_encode($res);
      }
      $stm = $db->prepare("INSERT INTO ui_structures_parts (ui_structure_id
  , item_type ,style_id, style_class,widgets_parameters,container_id,ui_structures_parts.order)
  SELECT ? , 'panel' , ? , ?, ? ,?,  count(*) FROM ui_structures_parts WHERE item_type = 'panel' AND ui_structure_id = $uisId") or die($db->error);
      $stm->bind_param("sssss", $uisId, $styleId, $styleClass, $parameters, $container_id);
      if ($stm->execute())
      {
         $res = array(
             "status" => "success",
             message => "Panel has been added successfully",
             "uisId" => $stm->insert_id);
      }
      else
      {
         $res = array(
             "status" => "error",
             message => "Panel has NOT been added, Please try again");
      }
      $stm->close();
      $db->close();
      return json_encode($res);
   }

   public function update_panel()
   {
      $db = \EWCore::get_db_connection();

      $panelId = $db->real_escape_string($_REQUEST['panelId']);
      $styleId = $db->real_escape_string($_REQUEST['styleId']);
      $styleClass = $db->real_escape_string($_REQUEST['styleClass']);
      $parameters = $db->real_escape_string($_REQUEST["parameters"]);
      if (!$panelId)
      {
         $res = array(
             "status" => "unsuccess",
             "message" => "The field Panel ID is mandatory");
         $db->close();
         return json_encode($res);
      }
      $stm = $db->prepare("UPDATE ui_structures_parts SET style_id = ?, style_class = ? , widgets_parameters = ? WHERE id = ?");
      $stm->bind_param("ssss", $styleId, $styleClass, $parameters, $panelId);
      if ($stm->execute())
      {
         $res = array(
             "status" => "success",
             message => "Panel has been updated successfully");
      }
      else
      {
         $res = array(
             "status" => "error",
             message => "Panel has NOT been updated, Please try again");
      }
      $stm->close();
      $db->close();
      return json_encode($res);
   }

   public static function create_panel_content($panel = array(), $container_id, $no_data = null)
   {
      $result_html = '';
      foreach ($panel as $key => $value)
      {
//echo $value["type"];
         if ($value["type"] == "panel")
         {
//echo $value["type"] . " - " . $value["class"] . "<br>";
            self::$panel_index++;
            $result_html.=self::open_panel("panel-" . self::$current_timestamp . '-' . self::$ui_index . '-' . self::$panel_index, $container_id, $value["class"], $value["id"], $value["panelParameters"], FALSE);
            $result_html.=self::create_panel_content($value["children"], "panel-" . self::$ui_index . '-' . self::$panel_index);
            $result_html.=self::close_panel();
         }
         else
         {
            self::$widget_index++;
            $result_html.=self::open_widget("widget-" . self::$current_timestamp . '-' . self::$ui_index . '-' . self::$widget_index, $value["widgetType"], $value["class"], $value["widgetClass"], $value["id"], $value["widgetParameters"], $no_data);
            $result_html.=self::close_widget();

//echo $value["type"] . " - " . $value["class"] . "<br>";
         }
      }
      return $result_html;
   }

   public static function open_panel($panel_id, $container_id, $style_class, $style_id, $parameters, $row = TRUE, $block_name = null)
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
      $result_html.= "<div class='panel $style_class'  $style_id_text  data-panel-id=\"$panel_id\"  data-container-id=\"$container_id\"  style='$style' data-panel-parameters='" . stripcslashes($param_json) . "' data-panel='true'><div class='row'>";

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

      $style = $style ? "style='$style'" : "";
      $container_id = $container_id ? "data-container-id='$container_id'" : "";

      if ($style_id)
         $style_id_text = "id='$style_id'";

      /* if ($block_name)
        {
        require_once EW_TEMPLATES_DIR . "/blocks/" . $block_name . ".php";
        $block_name::initiate();
        } */
      $result_html.= "<div class='block $style_class'  $style_id_text  data-panel-id=\"$panel_id\"  $container_id  $style data-panel-parameters='" . stripcslashes($param_json) . "' data-block='true'>";
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
      if (!$class)
         return false;
      self::$widget_style_class.="$class ";
   }

   public static function get_widget_style_class()
   {
      return trim(self::$widget_style_class);
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
   public static function open_widget($widget_id, $widget_type, $style_class, $widget_style_class, $style_id, $params, $no_data = false)
   {

      // Empty widget style class when creating a widget
      $result_html = '';
      if ($style_id)
         $WIDGET_STYLE_ID = "id='$style_id'";
      //echo $params;

      if (is_array($params))
      {
         $widget_parameters = $params;
         $params = json_encode($params);
      }
      else if ($params)
         $widget_parameters = json_decode($params, true);

      //$widget_parameters = json_encode($params);
      $widget_title = WidgetsManagement::get_widget_details($widget_type)["title"];
      // Include widget content
      if (file_exists(EW_WIDGETS_DIR . '/' . $widget_type . '/index.php'))
      {
         ob_start();
         include EW_WIDGETS_DIR . '/' . $widget_type . '/index.php';
         $widget_content = ob_get_clean();
         $widget_content = preg_replace('/\{\$widget_id\}/', $widget_id, $widget_content);
      }
      // Add widget style class which specified with UIS editor to the widget
      self::set_widget_style_class($widget_style_class);
      $WIDGET_STYLE_CLASS = self::get_widget_style_class();

      //if ($no_data)
      //{
      //$parameters_string = "data-widget-parameters='$widget_parameters'";
      $widget_type_string = "data-widget-type='$widget_type'";
      $widget_title_string = "data-widget-title='$widget_title'";
      //}
      $result_html.= "<div class='widget-container $style_class' data-widget-container='true'>";
      $result_html.= "<div class='widget $WIDGET_STYLE_CLASS' $WIDGET_STYLE_ID data-widget-id='$widget_id' $widget_type_string $widget_title_string data-widget='true'>";
      $result_html.= $widget_content;
      self::$widget_style_class = "";
      self::add_widget_data($widget_id, $params);
      return $result_html;
   }

   public static function close_widget()
   {
      return '</div></div>';
   }

   public function create_widget($widget_type, $style_class, $widget_style_class, $style_id, $widget_parameters)
   {
      //echo (stripcslashes($widget_parameters));
      //$widget_parameters = html_entity_decode($widget_parameters);
      $timestamp = time();
      if ($_SESSION["_ew_gw_ts"] == $timestamp)
      {
         self::$ui_index++;
      }
      else
      {
         $_SESSION["_ew_gw_ts"] = $timestamp;
      }
      self::$current_timestamp = strval($timestamp);
      $widget_id = "widget-" . self::$current_timestamp . '-' . self::$ui_index . '-' . self::$widget_index;
      $widget_html = '';
      $widget_html .=self::open_widget($widget_id, $widget_type, $style_class, $widget_style_class, $style_id, ($widget_parameters));
      $widget_html .=self::close_widget();
      /* if (self::get_widget_data_object())
        {
        $widget_data = reset(self::get_widget_data_object());
        } */
      $widget_script = self::get_html_scripts($widget_id);
      return ["widget_html" => $widget_html,
          "widget_data" => ($widget_parameters),
          "widget_id" => $widget_id,
          "widget_script" => $widget_script,
          "widget_style" => ""];
   }

   public function get_widget($widgetId)
   {
      $db = \EWCore::get_db_connection();
      if (!isset($widgetId))
         $widgetId = $db->real_escape_string($_REQUEST["wId"]);
      if (!$widgetId)
         return;
      $result = $db->query("SELECT * FROM ui_structures_parts WHERE id = '$widgetId'") or die(null);

      if ($rows = $result->fetch_assoc())
      {
         $db->close();
         return json_encode($rows);
      }
      else
      {
         return null;
      }
   }

   public function update_widget($widgetId = null, $widgetType = null, $widgetParameters = null, $styleId = null, $styleClass = null, $style = null)
   {
      $db = \EWCore::get_db_connection();

      $stm = $db->prepare("UPDATE ui_structures_parts SET  widget_type = ?, widgets_parameters = ? ,style_id = ?, style_class= ?, style = ? 
            WHERE id = ?") or die($db->error);
      $stm->bind_param("ssssss", $widgetType, $widgetParameters, $styleId, $styleClass, $style, $widgetId);

      if ($stm->execute())
      {
         $res = array(
             "status" => "success",
             message => "Widget has been updated successfully",
             "widgetType" => $widgetType);
      }
      else
      {
         $res = array(
             "status" => "error",
             message => "Widget has NOT been updated, Please try again");
      }
      $stm->close();
      $db->close();
      return json_encode($res);
   }

   function get_template_settings_form($path)
   {
      if (file_exists(EW_PACKAGES_DIR . '/rm/public/' . $path . '/template.php'))
      {
         header("Content-Type: text/html");
         require_once EW_PACKAGES_DIR . '/rm/public/' . $path . '/template.php';
         $template = new \template();
         return $template->get_template_settings_form();
      }
      else
         return "tr{Nothing to configure}";
   }

   /* public function get_blocks()
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
     } */

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

         /* $title = null;
           $description = "";
           //print_r($tokens);
           $title = EWCore::get_comment_parameter("title", $path . $widget_dir . '/admin.php');
           $description = EWCore::get_comment_parameter("description", $path . $widget_dir . '/admin.php'); */

         $count++;
         $apps[] = WidgetsManagement::get_widget_details($widget_dir);
      }
      $out = array(
          "totalRows" => $count,
          "result" => $apps);
      return json_encode($out);
   }

   public static function get_widget_details($widget_type)
   {
      $path = EW_WIDGETS_DIR . '/' . $widget_type . '/admin.php';

      $title = "";
      $description = "";
      //print_r($tokens);
      $title = EWCore::get_comment_parameter("title", $path);
      $description = EWCore::get_comment_parameter("description", $path);
      $feeder_type = EWCore::get_comment_parameter("feeder_type", $path);

      return array(
          "name" => $widget_type,
          "path" => $widget_type,
          "title" => $title,
          "description" => $description,
          "feeder_type" => $feeder_type);
   }

   public static function get_widget_cp($widgetName = null)
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
      $db = \EWCore::get_db_connection();

      $stm = $db->prepare("INSERT INTO ui_structures_parts (ui_structure_id , item_type, widget_type, widgets_parameters , container_id ,style_id,style_class,style, ui_structures_parts.order) 
            SELECT ?,'widget',?,?,?,?,?,?,count(*) FROM ui_structures_parts WHERE container_id = $panelId") or die($db->error);
      $stm->bind_param("sssssss", $uisId, $widgetType, $widgetParameters, $panelId, $styleId, $styleClass, $style);

      if ($stm->execute())
      {
         $res = array(
             "status" => "success",
             message => "Widget has been added successfully",
             "wId" => $stm->insert_id,
             "widgetType" => $widgetType);
      }
      else
      {
         $res = array(
             "status" => "error",
             message => "Widget has NOT been added, Please try again");
      }
      $stm->close();
      $db->close();
      return json_encode($res);
   }

   public function remove_from_panel()
   {
      $db = \EWCore::get_db_connection();
      $uisId = $db->real_escape_string($_REQUEST['uisId']);
      $widgetId = $db->real_escape_string($_REQUEST['widgetId']);

      $stm = $db->prepare("DELETE FROM ui_structures_parts WHERE id = ? AND ui_structure_id = ? AND item_type = 'widget'") or die($db->error);
      $stm->bind_param("ss", $widgetId, $uisId);

      if ($stm->execute())
      {
         $res = array(
             "status" => "success",
             message => "Widget has been removed successfully",);
      }
      else
      {
         $res = array(
             "status" => "error",
             message => "Widget has NOT been removed, Please try again");
      }
      return json_encode($res);
   }

   public function remove_panel()
   {
      $db = \EWCore::get_db_connection();
      $uisId = $db->real_escape_string($_REQUEST['uisId']);
      $widgetId = $db->real_escape_string($_REQUEST['panelId']);

      $stm = $db->prepare("DELETE FROM ui_structures_parts WHERE (id = ? OR container_id = ?) AND ui_structure_id = ?") or die($db->error);
      $stm->bind_param("sss", $widgetId, $widgetId, $uisId);

      if ($stm->execute())
      {
         $res = array(
             "status" => "success",
             message => "Panel has been removed successfully",);
      }
      else
      {
         $res = array(
             "status" => "error",
             message => "Panel has NOT been removed, Please try again");
      }
      return json_encode($res);
   }

   private static $widget_data = array();

   private static function add_widget_data($widget_id, $data)
   {
      self::$widget_data[$widget_id] = $data;
   }

   private static function get_widget_data()
   {
      foreach (self::$widget_data as $wi => $data)
      {
         $data = ($data) ? $data : "{}";
         $data_string.="EW.widget_data['$wi'] = $data;\n";
      }
      return $data_string;
   }

   private static function get_widget_data_object()
   {
      return self::$widget_data;
   }

   public static function generate_view($uisId, $index = 0, $no_data = false)
   {
      $RESULT_HTML = '';
      $db = \EWCore::get_db_connection();
      if (!$no_data)
      {
         $no_data = false;
      }

      $panels = $db->query("SELECT * FROM ew_ui_structures WHERE id = '$uisId' ") or die($db->error);
      // Create unigue set of ID's every time when generate_view is called
      $timestamp = time();
      if ($_SESSION["_ew_gw_ts"] == $timestamp)
      {
         self::$ui_index++;
      }
      else
      {
         $_SESSION["_ew_gw_ts"] = $timestamp;
      }
      self::$current_timestamp = strval($timestamp);
      self::$panel_index = 0;
      self::$widget_index = 0;

      while ($rows = $panels->fetch_assoc())
      {

         $res = json_decode(($rows["structure"]), true);
         //echo json_encode($rows["structure"]);
         //echo json_decode(stripslashes($rows["structure"]));
         if (json_last_error() != JSON_ERROR_NONE)
         {
            $res = json_decode(stripslashes($rows["structure"]), true);
            //var_dump(json_last_error_msg() );
         }
      }
      //ob_start();
      //print_r($rows["structure"]);
      if (isset($res))
      {
         foreach ($res as $key => $value)
         {
            $RESULT_HTML.=self::open_block("panel-" . self::$current_timestamp . "-" . self::$ui_index . "-" . self::$panel_index, "", $value["class"], $value["id"], $value["panelParameters"], FALSE, $value["blockName"]);
            $RESULT_HTML.=self::create_panel_content($value["children"], "panel-" . self::$current_timestamp . '-' . self::$ui_index . '-' . self::$panel_index, $no_data);
            $RESULT_HTML.=self::close_block();
            self::$panel_index++;
         }
      }
      //$html = ob_get_clean();
      return ["body_html" => $RESULT_HTML,
          "widget_data" => self::get_widget_data()];
   }

   public static function get_html_styles()
   {
      return "";
   }

   public static function add_html_script($src, $script)
   {
      self::$html_scripts[] = array(
          "src" => $src,
          "script" => $script);
   }

   public static function get_html_scripts($element_id = '')
   {
      $result = "";
      foreach (self::$html_scripts as $script)
      {
         if ($script["src"])
            $result.="<script id='$element_id' src='{$script["src"]}'>{$script["script"]}</script>";
         else if ($script["script"])
            $result.="<script id='$element_id'>{$script["script"]}</script>";
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

   public static function get_html_keywords()
   {
      return self::$html_keywords;
   }

   public function show_container($container_id)
   {
      $db = \EWCore::get_db_connection();
      $items = $db->query("SELECT * FROM ui_structures_parts WHERE container_id = '$container_id'  ORDER BY ui_structures_parts.order") or die($db->error);

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
      $db = \EWCore::get_db_connection();
      $res = array(
          "status" => "success",
          message => "UIS has been set successfully for $path");
      if (!$uis_id)
      {
         $result = $db->query("DELETE FROM ew_pages_ui_structures WHERE path = '$path'");
         if ($result)
         {
            return json_encode($res);
         }
      }
      $db->query("SELECT * FROM ew_pages_ui_structures WHERE path = '$path'") or die($db->error);


      if ($db->affected_rows == 0)
      {
         $stm = $db->prepare("INSERT INTO ew_pages_ui_structures(path ,ui_structure_id ) VALUES(?,?)") or die($db->error);
         $stm->bind_param("ss", $path, $uis_id);
         if ($stm->execute())
            $res = array(
                "status" => "success",
                message => "UIS has been set successfully for $path ",
                "puisId" => $stm->insert_id);
         else
            $res = array(
                "status" => "error",
                message => "UIS has NOT been sat, Please try again");
      }
      else
      {
         $stm = $db->prepare("UPDATE ew_pages_ui_structures SET  ui_structure_id = ?  WHERE path = ?") or die($db->error);
         $stm->bind_param("ss", $uis_id, $path);
         if (!$stm->execute())
            $res = array(
                "status" => "error",
                message => "UIS has NOT been sat, Please try again");
      }

      $stm->close();
      $db->close();
      return json_encode($res);
   }

   public static function get_path_uis($path = null)
   {
      $path = ($path) ? $path : $_REQUEST["path"];
      $db = \EWCore::get_db_connection();
      $result = $db->query("SELECT ew_ui_structures.id AS id,name,template,path FROM ew_pages_ui_structures,ew_ui_structures WHERE ew_pages_ui_structures.ui_structure_id = ew_ui_structures.id AND path = '$path'") or die($db->error);
      if ($rows = $result->fetch_assoc())
      {
         //$db->close();
         return json_encode($rows);
      }
      else
      {
         return null;
      }
   }

   public static function get_layout($uisId, $template = null, $template_settings = null)
   {
      $layout = WidgetsManagement::generate_view($uisId);
      //echo "asd$uisId";
      $template_body = $layout["body_html"];
      $widget_data = $layout["widget_data"];
      if (!$template)
      {
         $uis_info = json_decode(WidgetsManagement::get_uis($uisId), true);
         $template = $uis_info["template"];
         $template_settings = $uis_info["template_settings"];
      }
      if (file_exists(EW_ROOT_DIR . $template . '/template.php'))
      {
         require_once EW_ROOT_DIR . $template . '/template.php';
         $template = new \template();
         //echo $template_settings;

         $settings = json_decode($template_settings, true);
         if (json_last_error() != JSON_ERROR_NONE)
         {
            $settings = json_decode(stripslashes($template_settings), true);
         }
         //$template_settings = json_decode(stripslashes($template_settings), true);
         $template_body = $template->get_template_body($template_body, $settings);
         $template_script = $template->get_template_script($settings);
      }

      return ["template_body" => $template_body,
          "template_script" => $template_script,
          "widget_data" => $widget_data];
   }

   public static function add_widget_feeder($type, $app, $id, $function)
   {
      if (!isset(self::$registry[static::$WIDGET_FEEDER]) || !array_key_exists($app, self::$registry[static::$WIDGET_FEEDER]))
      {
         self::$registry[static::$WIDGET_FEEDER][$app] = array();
      }

      if (!isset(self::$registry[static::$WIDGET_FEEDER][$app][$type]))
      {
         self::$registry[static::$WIDGET_FEEDER][$app][$type] = array();
      }

      self::$registry[static::$WIDGET_FEEDER][$app][$type][$id] = $function;

      //EWCore::register_object(static::$WIDGET_FEEDER, $app, self::$registry[static::$WIDGET_FEEDER][$app]);
   }

   /**
    * Check whether widget feeder exists
    * @param type $type
    * @param string $app
    * @param type $id
    * @return boolean returns app name if the $app parameter is set to * or true if the app name is specefied and false in other cases
    */
   public static function is_widget_feeder($type, $app, $id)
   {
      if (!$app && $app != '*')
         $app = 'admin';
      $func = null;
      $feederApp = true;
      $result = false;
      if (!isset(self::$registry[static::$WIDGET_FEEDER]))
      {
         return false;
      }

      array_walk(self::$registry[static::$WIDGET_FEEDER], function($item, $key)use ($type, $app, $id, &$feederApp, &$result)
      {
         if ($app == "*" || $app == $key)
         {
            if ($type == '*')
            {
               foreach ($item as $feeder => $p)
               {
                  if (isset($p[$id]))
                  {
                     //echo $key." ".$feeder."  ".$id;
                     $result = true;
                  }
               }
            }
            else if ($item[$type][$id])
            {
               $feederApp = $key;
               $result = true;
            }
         }
      });
      if ($result)
         return $feederApp;
      // Check all thge apps for specified feeder
      $all_feeders = self::$registry[static::$WIDGET_FEEDER];
      if ($app == "*")
      {
         foreach ($all_feeders as $feeder => $p)
         {
            if (isset($p[$type][$id]))
               return $feeder;
         }
         return FALSE;
      }
      if (!$app)
         $app = 'admin';


      //$feeder = EWCore::read_registry(static::$WIDGET_FEEDER);
      if ($all_feeders[$app][$type][$id])
      {
         //$func = EWCore::read_registry("ew-widget-feeder");
         $func = $feeder[$app][$type][$id];
      }

      if ($func)
         return TRUE;
      else
         return FALSE;
   }

   /**
    * 
    * @param String $id Id of feeder
    * @return \ew\WidgetFeeder
    */
   public static function get_widget_feeder($id)
   {
      /* $feeder = null;
        if (!$app)
        $app = 'admin';
        var_dump(static::$widgets_feeders);

        if (static::$widgets_feeders[$id] )
        {
        $feeder = static::$widgets_feeders[$type][$id];
        }

        if (is_string($feeder) && substr($feeder, -strlen(".php")) === ".php")
        {
        if (!file_exists($feeder))
        return json_encode(array(
        "html" => "$type/$id: File not found"));
        ob_start();
        include $feeder;
        $html = ob_get_clean();
        return json_encode(array(
        "html" => $html));
        //print_r($func);
        }
        if (!is_callable($feeder))
        {
        echo "$app/$type/$id: Function is not valid or callable";
        }
        if (!$arg)
        return call_user_func($feeder);
        else
        return call_user_func_array($feeder, $arg); */
      //echo $id.'<br/>';
      //return \EWCore::call($id, $arg);
      return static::$widgets_feeders[$id];
   }

   /**
    * 
    * @param \ew\Module $module
    * @param \ew\WidgetFeeder $feeder
    */
   public static function register_widget_feeder($feeder/* $module, $type, $function_name, $resource_type = "api" */)
   {
      //parent::register_widget_feeder($type, $id, $function_name);
      /* if (!isset(static::$widgets_feeders[$feeder->widget_type]))
        {
        static::$widgets_feeders[$feeder->widget_type] = [];
        } */
      //$id = $feeder->module->get_app()->get_root() . '/' . $resource_type . '/' . EWCore::camelToHyphen($module->get_name() . '/' . $function_name);
      //$feeder = new \stdClass();
      /* $feeder->package = $module->get_app()->get_root();
        $feeder->$resource_type = $resource_type;
        $feeder->module = EWCore::camelToHyphen($module->get_name());
        $feeder->function = str_replace('_', '-', $function_name); */
      static::$widgets_feeders[$feeder->id] = $feeder;
      //print_r(static::$widgets_feeders);
   }

   /**
    * 
    * @type string type of widget feeder
    * @return mixed
    */
   public static function get_widget_feeders($type = "all")
   {

      $list = array(
          "totalRows" => count(EWCore::read_registry(static::$WIDGET_FEEDER)),
          "result" => array());
//      print_r(EWCore::read_registry("ew-widget-feeder"));
      foreach (EWCore::read_registry(static::$WIDGET_FEEDER) as $app_name => $feeder_type)
      {
         //$parts = explode(":", $wf);
         //if (!$type || $type == "all" || $type == $parts[0])
         //print_r($wf);

         foreach ($feeder_type as $feeder_type_name => $id)
         {
            foreach ($id as $feeder => $f)
            {
               if (!$type || $type == "all" || $type == $feeder_type_name)
                  $list["result"][] = array(
                      "name" => $feeder,
                      "type" => $feeder_type_name,
                      "app" => $app_name);
            }
         }
      }
      return json_encode($list);
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
