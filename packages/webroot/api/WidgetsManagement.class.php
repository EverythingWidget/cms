<?php

namespace webroot;

use Module;
use EWCore;

/**
 * Description of ContentManagement
 *
 * @author Eeliya
 */
class WidgetsManagement extends \ew\Module {

  private static $panel_index = 0;
  private static $widget_index = 0;
  private static $ui_index = 0;
  private static $current_timestamp = 0;
  private static $title = "";
  private static $html_scripts = array();
  private static $html_included_links = [];
  private static $html_links = array();
  private static $html_keywords = [];
  private static $widgets_feeders = array();
  protected $resource = "api";
  public static $WIDGET_FEEDER = "ew-widget-feeder";
  private static $registry = [];
  private $link_chooser_form = null;

  protected function install_assets() {
    EWCore::register_app("widgets-management", $this);
    ob_start();
    include EW_PACKAGES_DIR . '/webroot/html/widgets-management/link-chooser-uis.php';
    $this->link_chooser_form = ob_get_clean();

    ob_start();
    include EW_PACKAGES_DIR . '/webroot/html/widgets-management/uis-tab.php';
    $uis_content_tab = ob_get_clean();

    EWCore::register_form("ew/ui/forms/content/tabs", "uis-tab", [
        "title" => "UI",
        "form"  => $uis_content_tab
    ]);
    $this->add_listener("admin/api/content-management/update-folder", "call_on_folder_update");
    $this->add_listener("admin/api/content-management/update-article", "call_on_article_update");
    $this->add_listener("admin/api/content-management/get-article", "call_on_folder_get");
    $this->add_listener("admin/api/content-management/contents", "call_on_folder_get");
  }

  protected function install_permissions() {
    EWCore::register_form("ew/ui/components/link-chooser", "uis-chooser", ["title"   => "UI Structures",
        "content" => $this->link_chooser_form]);

    $this->register_permission("view", "User can view the widgets section", array(
        'api/read_layouts',
        "api/get_uis",
        "api/get_uis_list",
        "api/get_widgets_types",
        "api/widgets_types",
        "api/get_all_pages_uis_list",
        "api/get_path_uis",
        "api/get_template_settings_form",
        "api/get_layout",
        "api/get_templates",
        "api/create_widget",
        "api/update-uis",
        "api/ew_form_uis_tab",
        "html/ne-uis.php_see",
        'html/' . $this->get_index()));

    $this->register_permission("manipulate", "User can create and edit layouts", array(
        "api/add_uis",
        "api/get_uis",
        "api/set_uis",
        "api/get_uis_list",
        "api/get_widgets_types",
        "api/get_all_pages_uis_list",
        "api/get_path_uis",
        "api/get_template_settings_form",
        "api/get_layout",
        "api/get_templates",
        "api/create_widget",
        "api/update-uis",
        "api/ew_form_uis_tab",
        "api/delete_uis",
        "api/clone_uis",
        "html/ne-uis.php",
        'html/' . $this->get_index()));


    $this->register_permission("export-uis", "User can export UIS", array(
        "api/export_uis",
        "html/ne-uis.php"));
    $this->register_permission("import-uis", "User can import UIS", array(
        "api/import_uis",
        "html/ne-uis.php"));

    //$this->register_content_label("uis", "");
  }

  public function call_on_folder_update($id, $webroot) {
    $page_uis_id = $webroot['page_uis_id'];
    if (isset($id) && $page_uis_id) {
      $this->set_uis("/folders/" . $id, $page_uis_id);
    }
    else {
      $this->set_uis("/folders/" . $id, null);
    }
  }

  public function call_on_article_update($id, $webroot) {
    $page_uis_id = $webroot['page_uis_id'];
    if (isset($id) && $page_uis_id) {
      $this->set_uis("/articles/" . $id, $page_uis_id);
    }
    else {
      $this->set_uis("/articles/" . $id, null);
    }
  }

  public function call_on_folder_get($_response) {
    $result = [];
    $content_data = $_response->data;

    if (isset($content_data) && $content_data["id"]) {
      $uis_id = $content_data["id"];

      if ($content_data['type'] === 'article') {
        $page_uis = $this->get_path_uis("/articles/$uis_id");
      }
      else if ($content_data['type'] === 'folder') {
        $page_uis = $this->get_path_uis("/folders/$uis_id");
      }
    }
    
    return [
        "webroot/page_uis_id" => ($page_uis["uis_id"]) ? $page_uis["uis_id"] : "",
        "webroot/name"        => ($page_uis["uis_name"]) ? $page_uis["uis_name"] : "Inherit/Default"
    ];
  }

  public function get_templates() {
    $path = EW_PACKAGES_DIR . '/rm/public/templates/';

    $apps_dirs = opendir($path);
    $apps = array();

    while ($template_dir = readdir($apps_dirs)) {
      if (strpos($template_dir, '.') === 0)
        continue;

      if (!is_dir($path . $template_dir))
        continue;

      $template_dir_content = opendir($path . $template_dir);

      while ($file = readdir($template_dir_content)) {

        if (strpos($file, '.') === 0)
          continue;
        //$i = strpos($file, 'template.css');

        if ($file == 'template.css') {
          $apps[] = array(
              "templateName" => $template_dir,
              "templatePath" => "templates/" . $template_dir);
        }
      }
    }
    return json_encode($apps);
  }

  public function category_action_get($_data) {
    if ($_data["id"]) {
      $page_uis = $this->get_path_uis("/folder/" . $_data["id"]);
      $_data["WidgetManagement_pageUisId"] = ($page_uis["id"]) ? $page_uis["id"] : "";
      $_data["WidgetManagement_name"] = ($page_uis["name"]) ? $page_uis["name"] : "Inherit/Default";
    }
    return json_encode($_data);
  }

  public function category_action_update($_input, $id, $WidgetManagement_pageUisId) {
    if ($WidgetManagement_pageUisId) {
      $this->set_uis("/folder/" . $id, $WidgetManagement_pageUisId);
    }
    else {
      $this->set_uis("/folder/" . $id);
    }
  }

  public function article_action_get($_input, $_language) {
    if ($_input["id"]) {
      //echo $_data;
      $page_uis = $this->get_path_uis("/article/" . $_input["id"]);
      $_input["WidgetManagement_pageUisId"] = ($page_uis["id"]) ? $page_uis["id"] : "";
      $_input["WidgetManagement_name"] = ($page_uis["name"]) ? $page_uis["name"] : "Inherit/Default";
      //return $_input;
    }
    //return $_input;
    //return $_input;
  }

  public function article_action_update($_data, $id, $WidgetManagement_pageUisId) {
    //echo $id;
    if ($WidgetManagement_pageUisId) {
      $this->set_uis("/article/" . $id, $WidgetManagement_pageUisId);
    }
    else {
      $this->set_uis("/article/" . $id);
    }
  }

  public function ew_form_uis_tab($form_config) {
    return json_encode(["html" => $this->get_app()->get_view('html/widgets-management/uis-tab.php', $form_config)]);
  }

  public function get_uis_list($token = 0, $size = 99999999999999) {
    $db = \EWCore::get_db_connection();

    if (!isset($token)) {
      $token = 0;
    }
    if (!$size) {
      $size = 99999999999999;
    }

    $totalRows = $db->query("SELECT COUNT(*)  FROM ew_ui_structures ") or die(error_reporting());
    $totalRows = $totalRows->fetch_assoc();
    $result = $db->query("SELECT *  FROM ew_ui_structures ORDER BY name LIMIT $token,$size") or die(error_reporting());

//$out = array();
    $rows = array();
    while ($r = $result->fetch_assoc()) {
      $rows[] = $r;
    }
    $db->close();
    $out = array(
        "totalRows" => $totalRows['COUNT(*)'],
        "result"    => $rows);
    return ($out);
  }

  public function get_path_uis_list() {
    $db = \EWCore::get_db_connection();

    $result = $db->query("SELECT ew_pages_ui_structures.path,ew_ui_structures.id, ew_ui_structures.name, ew_ui_structures.template  FROM ew_pages_ui_structures,ew_ui_structures WHERE ew_pages_ui_structures.ui_structure_id = ew_ui_structures.id") or die(error_reporting());

//$out = array();
    $rows = array();
    while ($r = $result->fetch_assoc()) {
      $rows[] = array(
          $r["path"] . "_uisId" => $r["id"],
          $r["path"]            => $r["name"]);
    }
    $db->close();
    //$out = array(;
    return json_encode($rows);
  }

  public function get_all_pages_uis_list() {
    $db = \EWCore::get_db_connection();
    $result = $db->query("SELECT ew_pages_ui_structures.id AS id, ew_pages_ui_structures.path AS path, ew_ui_structures.name AS name FROM ew_pages_ui_structures,ew_ui_structures WHERE ew_pages_ui_structures.ui_structure_id = ew_ui_structures.id AND ew_pages_ui_structures.path LIKE '%'") or die(error_reporting());
    $rows = array();
    while ($r = $result->fetch_assoc()) {
      $rows[] = $r;
    }
    $db->close();
    //$out = array(;
    return json_encode(array(
        "totalRows" => $result->num_rows,
        "result"    => $rows));
  }

  public function add_uis($name = null, $template = null, $template_settings = null, $structure = null) {
    $db = \EWCore::get_db_connection();

    if (!$name) {
      return EWCore::log_error(400, "The field name is mandatory");
    }
    $stm = $db->prepare("INSERT INTO ew_ui_structures(name,template,template_settings,structure) VALUES (?,?,?,?)");
    $stm->bind_param("ssss", $name, $template, $template_settings, $structure);
    $stm->execute();
    if ($_REQUEST['defaultUIS'] == "true") {
      $this->set_uis("@DEFAULT", $stm->insert_id);
    }
    if ($_REQUEST['homeUIS'] == "true") {
      $this->set_uis("@DEFAULT", $stm->insert_id);
    }
    $res = array(
        "status" => "success",
        "uisId"  => $stm->insert_id,
        "name"   => $name);
    $stm->close();
    $db->close();
    return $res;
  }

  public function import_uis() {
    $db = \EWCore::get_db_connection();

    $fileContent = json_decode(file_get_contents($_FILES['uis_file']['tmp_name']), true);

    if (!$fileContent["name"]) {
      $res = array(
          "status"  => "unsuccess",
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
        "status"  => "success",
        "uisId"   => $stm->insert_id,
        "message" => "tr{The UIS has been imported succesfully}");
    $stm->close();
    $db->close();
    return json_encode($res);
  }

  public function export_uis($uis_id) {
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
        "name"              => $name,
        "template"          => $template,
        "template_settings" => $template_settings,
        "structure"         => $structure);
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

  public function clone_uis($uisId = null) {
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

  public function update_uis($uisId = null, $name = null, $template = null, $template_settings = null, $perview_url = null, $structure = null) {
    $db = \EWCore::get_db_connection();
//echo json_encode($structure);
    if (!$name) {
      $res = [
          "status"  => "unsuccess",
          "message" => "The field name is mandatory"
      ];
      $db->close();
      return json_encode($res);
    }
    if (is_array($structure)) {
      $structure = json_encode($structure);
    }
    $stm = $db->prepare("UPDATE ew_ui_structures SET name = ?, template= ?, template_settings= ?, perview_url = ?, structure = ? WHERE id = ?") or die($db->error);
    $stm->bind_param("ssssss", $name, $template, $template_settings, $perview_url, $structure, $uisId);
    $error = $db->errno;
    if ($stm->execute()) {
      if ($_REQUEST['defaultUIS'] == "true") {
        $this->set_uis("@DEFAULT", $uisId);
      }
      if ($_REQUEST['homeUIS'] == "true") {
        $this->set_uis("@HOME_PAGE", $uisId);
      }
      $stm->close();
      $db->close();
      return json_encode(array(
          status    => "success",
          "message" => "tr{The layout has been saved successfully}",
          "data"    => [title => $name]));
    }
    else {
      return json_encode(array(
          status  => "unsuccess",
          message => $error));
    }
  }

  public static function get_uis($uisId = null) {
    $db = \EWCore::get_db_PDO();

    if (!$uisId)
      return;

    $stm = $db->prepare("SELECT id, name, template, template_settings, perview_url, structure FROM ew_ui_structures WHERE id = ?");
    $stm->execute([$uisId]);

    $default_uis = WidgetsManagement::get_path_uis("@DEFAULT");
    $home_uis = WidgetsManagement::get_path_uis("@HOME_PAGE");


    if ($row = $stm->fetch(\PDO::FETCH_ASSOC)) {
      $row['template_settings'] = json_decode($row['template_settings'], true);

      if ($default_uis["id"] == $uisId)
        $row["uis-default"] = "true";
      if ($home_uis["id"] == $uisId)
        $row["uis-home-page"] = "true";
      return $row;
    }
    else {
      return [];
    }
  }

  public function delete_uis($uisId) {
    $db = \EWCore::get_db_connection();

    $statement = $db->prepare("DELETE FROM ew_ui_structures WHERE id = ?");
    $statement->bind_param("s", $uisId);
    if ($statement->execute()) {
      return json_encode(array(
          status => "success"));
    }
    else {
      return json_encode(array(
          status => "unsuccess"));
    }
  }

  public static function create_panel_content($panel = array(), $container_id, $no_data = null) {
    $result_html = '';
    if (isset($panel)) {
      foreach ($panel as $key => $value) {
        if ($value["type"] == "panel") {
          self::$panel_index++;
          $result_html.=self::open_panel("panel-" . self::$current_timestamp . '-' . self::$ui_index . '-' . self::$panel_index, $container_id, $value["class"], $value["id"], $value["panelParameters"], FALSE);
          $result_html.=self::create_panel_content($value["children"], "panel-" . self::$ui_index . '-' . self::$panel_index);
          $result_html.=self::close_panel();
        }
        else {
          self::$widget_index++;
          $result_html.=self::open_widget("widget-" . self::$current_timestamp . '-' . self::$ui_index . '-' . self::$widget_index, $value["widgetType"], $value["class"], $value["widgetClass"], $value["id"], $value["widgetParameters"], $no_data);
          $result_html.=self::close_widget();
        }
      }
    }
    return $result_html;
  }

  public static function open_panel($panel_id, $container_id, $style_class, $style_id, $parameters, $row = TRUE, $block_name = null) {
    $result_html = '';
    $parameters_array = json_decode($parameters, TRUE);

    if ($style_id) {
      $style_id_text = "id='$style_id'";
    }

    $result_html.= "<div class='panel $style_class'  $style_id_text  data-panel-id=\"$panel_id\"  data-container-id=\"$container_id\"  data-panel='true'>";

    /* if ($parameters_array["title"] && $parameters_array["title"] != "none")
      {
      $result_html.= "<div class='col-xs-12 panel-header'><{$parameters_array["title"]}>" . $parameters_array["title-text"] . "</{$parameters_array["title"]}></div>";
      } */

    return $result_html;
  }

  public static function close_panel() {
    return '</div>';
  }

  public static function open_block($panel_id, $container_id, $style_class, $style_id, $parameters, $row = TRUE, $block_name) {
    $result_html = '';
    $param_json = $parameters;

    $html_container_id = $container_id ? "data-container-id='$container_id'" : "";

    if ($style_id) {
      $style_id_text = "id='$style_id'";
    }

    $result_html.= "<div class='block $style_class'  $style_id_text  data-panel-id=\"$panel_id\"  $html_container_id data-panel-parameters='" . stripcslashes($param_json) . "' data-block='true'>";

    return $result_html;
  }

  public static function close_block() {
    return '</div>';
  }

  private static $widget_style_class;

  /**
   * Set style class for the widget which is currently being initialized
   * 
   * @param String $class class name
   */
  public static function set_widget_style_class($class) {
    if (!$class) {
      return false;
    }
    self::$widget_style_class.="$class ";
  }

  public static function get_widget_style_class() {
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
  public static function open_widget($widget_id, $widget_type, $style_class, $widget_style_class, $style_id, $params, $no_data = false) {
    // Empty widget style class when creating a widget
    $__widget_html_output = '';
    if ($style_id) {
      $WIDGET_STYLE_ID = "id='$style_id'";
    }
    //echo $params;

    if (is_array($params)) {
      $widget_parameters = $params;
      $params = json_encode($params);
    }
    else if ($params) {
      $widget_parameters = json_decode($params, true);
    }
    //$widget_parameters = json_encode($params);
    $widge_class = EWCore::hyphenToCamel($widget_type);

    if (file_exists(EW_WIDGETS_DIR . '/' . $widget_type . "/$widge_class.class.php")) {
      require_once EW_WIDGETS_DIR . '/' . $widget_type . "/$widge_class.class.php";
      $widget_class_name = "webroot\\$widge_class";
      $widget_class_instance = (new $widget_class_name());
      $widget_title = $widget_class_instance->get_title();
      $widget_content_raw = $widget_class_instance->render($widget_parameters, $widget_id, $style_id, $style_class);
      $widget_content = preg_replace('/\{\$widget_id\}/', $widget_id, $widget_content_raw);
    }
    else {

      $widget_title = WidgetsManagement::get_widget_details($widget_type)["title"];
      // Include widget content
      if (file_exists(EW_WIDGETS_DIR . '/' . $widget_type . '/index.php')) {
        ob_start();
        include EW_WIDGETS_DIR . '/' . $widget_type . '/index.php';
        $widget_content_raw = ob_get_clean();
        $widget_content = preg_replace('/\{\$widget_id\}/', $widget_id, $widget_content_raw);
      }
    }
    // Add widget style class which specified with UIS editor to the widget
    self::set_widget_style_class($widget_style_class);
    $WIDGET_STYLE_CLASS = self::get_widget_style_class();

    $widget_type_string = "data-widget-type='$widget_type' ";
    $widget_title_string = "data-widget-title='$widget_title' ";
    $widget_editor_title_string = "data-widget-editor-title='{$widget_parameters['editor_title']}' ";
    //}
    $__widget_html_output .= "<div class='widget-container $style_class' data-widget-container='true'>";
    $__widget_html_output .= "<div class='widget $WIDGET_STYLE_CLASS' $WIDGET_STYLE_ID data-widget-id='$widget_id' ";
    $__widget_html_output .= $widget_type_string . $widget_title_string . $widget_editor_title_string;
    $__widget_html_output .= "data-widget='true'>";
    $__widget_html_output .= $widget_content;
    self::$widget_style_class = "";
    self::add_widget_data($widget_id, $params);

    return $__widget_html_output;
  }

  public static function close_widget() {
    return '</div></div>';
  }

  public function create_widget($widget_type, $style_class, $widget_style_class, $style_id, $widget_parameters) {
    $timestamp = time();
    if ($_SESSION["_ew_gw_ts"] == $timestamp) {
      self::$ui_index++;
    }
    else {
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
    return ["widget_html"   => $widget_html,
        "widget_data"   => $widget_parameters,
        "widget_id"     => $widget_id,
        "widget_script" => $widget_script,
        "widget_style"  => ""];
  }

  function get_template_settings_form($path) {
    header("Content-Type: text/html");
    if (file_exists(EW_PACKAGES_DIR . '/rm/public/' . $path . '/template.php')) {
      header("Content-Type: text/html");
      require_once EW_PACKAGES_DIR . '/rm/public/' . $path . '/template.php';
      $template = new \template();
      return $template->get_template_settings_form();
    }
    else {
      return "tr{Nothing to configure}";
    }
  }

  public function get_widgets_types() {
    $path = EW_WIDGETS_DIR . '/';

    $apps_dirs = opendir($path);
    $apps = array();
    $count = 0;
    while ($widget_dir = readdir($apps_dirs)) {
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
    $out = [
        "totalRows" => $count,
        "result"    => $apps
    ];

    return json_encode($out);
  }

  public function widgets_types() {
    $path = EW_WIDGETS_DIR . '/';

    $apps_dirs = opendir($path);
    $apps = array();
    $count = 0;
    while ($widget_dir = readdir($apps_dirs)) {
      if (strpos($widget_dir, '.') === 0)
        continue;

      $count++;

      $widge_class = EWCore::hyphenToCamel($widget_dir);
      if (file_exists(EW_WIDGETS_DIR . '/' . $widget_dir . "/$widge_class.class.php")) {
        require_once EW_WIDGETS_DIR . '/' . $widget_dir . "/$widge_class.class.php";
        $widget_class_name = "webroot\\$widge_class";
        $widget_class_instance = new $widget_class_name();
        $widget_details = [
            "name"        => $widget_class_instance->get_title(),
            "path"        => $widget_dir,
            "title"       => $widget_class_instance->get_title(),
            "description" => $widget_class_instance->get_description(),
            "feeder_type" => $widget_class_instance->get_feeder_type()
        ];
        $apps[] = $widget_details;
      }
      else {
        $apps[] = WidgetsManagement::get_widget_details($widget_dir);
      }
    }

    return \ew\APIResourceHandler::to_api_response($apps, ["totalRows" => $count]);
  }

  public static function get_widget_details($widget_type) {
    $path = EW_WIDGETS_DIR . '/' . $widget_type . '/admin.php';

    $title = "";
    $description = "";
    $source = file_get_contents($path);

    $title = \EWCore::get_comment_parameters("title", $source);
    $description = \EWCore::get_comment_parameters("description", $source);
    $feeder_type = \EWCore::get_comment_parameters("feeder_type", $source);

    return [
        "name"        => $widget_type,
        "path"        => $widget_type,
        "title"       => $title,
        "description" => $description,
        "feeder_type" => $feeder_type
    ];
  }

  public static function get_widget_cp($widget_path = null) {
    $widget_configurator_form = '<form id="uis-widget" onsubmit="return false;">';

    if ($widget_path) {
      $widge_class = EWCore::hyphenToCamel($widget_path);
      if (file_exists(EW_WIDGETS_DIR . '/' . $widget_path . "/$widge_class.class.php")) {
        require_once EW_WIDGETS_DIR . '/' . $widget_path . "/$widge_class.class.php";
        $widget_class_name = "webroot\\$widge_class";
        $widget_class_instance = new $widget_class_name();
        $widget_configurator_form .= $widget_class_instance->get_configuration_form();
      }
      else {
        ob_start();
        include EW_WIDGETS_DIR . '/' . $widget_path . '/admin.php';
        $widget_configurator_form.= ob_get_clean();
      }
    }

    $widget_configurator_form.= '</form>';
    return $widget_configurator_form;
  }

  function get_widget_cp_full() {
    $widgetName = $_REQUEST["widgetName"];
    ob_start();
    echo '<form id="uis-widget" onsubmit="return false;">';
    include EW_WIDGETS_DIR . '/' . $widgetName . '/admin.php';
    if (function_exists("get_content"))
      echo get_content();
    echo '</form>';
    if (function_exists("get_script"))
      echo get_script();
    return ob_get_clean();
  }

  private static $widget_data = array();

  private static function add_widget_data($widget_id, $data) {
    self::$widget_data[$widget_id] = $data;
  }

  private static function get_widget_data() {
    foreach (self::$widget_data as $wi => $data) {
      $data = ($data) ? $data : "{}";
      $data_string.="ew_widget_data['$wi'] = $data;\n";
    }
    return $data_string;
  }

  private static function get_widget_data_object() {
    return self::$widget_data;
  }

  public static function generate_view($uisId, $index = 0, $no_data = false) {
    $RESULT_HTML = '';
    $db = \EWCore::get_db_PDO();
    if (!$no_data) {
      $no_data = false;
    }

    $statement = $db->prepare("SELECT structure FROM ew_ui_structures WHERE id = ? ") or die($db->error);


    //$statement->bind_result($structure);
    //$rows = $blocks->fetch_assoc();
    // Create unigue set of ID's every time when generate_view is called
    $timestamp = time();
    if ($_SESSION["_ew_gw_ts"] == $timestamp) {
      self::$ui_index++;
    }
    else {
      $_SESSION["_ew_gw_ts"] = $timestamp;
    }
    self::$current_timestamp = strval($timestamp);
    self::$panel_index = 0;
    self::$widget_index = 0;
    if ($statement->execute([$uisId])) {
      $structure = $statement->fetch(\PDO::FETCH_NUM);
      $structure_array = json_decode($structure[0], true);
    }

    if (isset($structure_array)) {
      foreach ($structure_array as $key => $value) {
        $RESULT_HTML.=self::open_block("panel-" . self::$current_timestamp . "-" . self::$ui_index . "-" . self::$panel_index, "", $value["class"], $value["id"], $value["blockParameters"], FALSE, $value["blockName"]);
        $RESULT_HTML.=self::create_panel_content($value["children"], "panel-" . self::$current_timestamp . '-' . self::$ui_index . '-' . self::$panel_index, $no_data);
        $RESULT_HTML.=self::close_block();
        self::$panel_index++;
      }
    }

    return [
        "body_html"   => $RESULT_HTML,
        "widget_data" => self::get_widget_data()
    ];
  }

  /* private static function cache_file($file)
    {

    } */

  public static function add_html_script($src, $script = "") {
    if (is_array($src)) {
      self::$html_scripts[] = $src;
      return;
    }

    self::$html_scripts[] = array(
        "src"    => $src,
        "script" => $script);
  }

  public static function get_html_scripts($element_id = '') {
    $script_tags = "";
    $includes = [];
    $duplicates = [];

    foreach (self::$html_scripts as $script) {
      //if ($script["src"]) {
      if ($script["id"]) {
        $element_id = "id='{$script["id"]}'";
      }

      if (isset($script["src"])) {
        if (in_array($script["src"], $duplicates))
          continue;

        $duplicates[] = $script["src"];
        $element_src = "src='{$script["src"]}'";
      }

      if ($script["include"]) {
        if (in_array($script["include"], $includes))
          continue;

        $includes[] = $script["include"];
      }
      else {


        $script_tags.="<script $element_id $element_src defer>{$script["script"]}</script>\n";
      }

      $element_id = "";
      $element_src = "";
    }

    $file_times = '';
    $minified_code = null;
    foreach ($includes as $source) {
      $src = EW_PACKAGES_DIR . '/' . $source;
      if (!file_exists($src)) {
        continue;
      }

      $file_times .= filemtime($src);
    }

    $cache_file_name = md5($file_times) . '.js';
    $cache_path = EW_PACKAGES_DIR . '/rm/public/cache/' . $cache_file_name;
    $cache_path_url = EW_ROOT_URL . "~rm/public/cache/$cache_file_name";

    if (file_exists($cache_path)) {
      //$minified_code .= file_get_contents($cache_path);
      $cache_path = EW_ROOT_URL . "~rm/public/cache/$cache_file_name";
      $minified_code = true;
    }
    else {
      //echo "$cache_file_name";
      //array_map('unlink', glob(EW_PACKAGES_DIR . '/rm/public/cache/' . "*"));

      foreach ($includes as $source) {
        $src = EW_PACKAGES_DIR . '/' . $source;

        if (!file_exists($src)) {
          $minified_code .= "\n\n\n // NOT FOUND: $src \n\n\n";
          //die('//NOT FOUND: ' . $src);
        }
        else {
          $file_content = file_get_contents($src);

          $minified_code .= \JShrink\Minifier::minify($file_content);
        }
      }

      EWCore::file_force_contents($cache_path, $minified_code);
    }

    if ($minified_code) {
      $script_tags.="<script id='ew-compiled-scripts' src='$cache_path_url' defer></script>";
    }

    return $script_tags;
  }

  public static function add_html_link($href) {
    self::$html_links[] = $href;
  }

  public static function include_html_link($href) {
    self::$html_included_links = array_unique(array_merge(self::$html_included_links, $href));
  }

  public static function get_html_links() {
    $link_tags = "";
    foreach (self::$html_links as $href) {
      $link_tags .= "<link rel='stylesheet' property='stylesheet' type='text/css' href='$href' />";
    }
    return $link_tags;
  }

  public static function get_html_links_concatinated($element_id = '') {
    $css_tag = "";

    $file_times = '';
    $minified_css = null;
    foreach (self::$html_included_links as $source) {
      $src = EW_PACKAGES_DIR . '/' . $source;
      $file_times .= filemtime($src);
    }

    $cache_file_name = md5($file_times) . '.css';
    $cache_path = EW_PACKAGES_DIR . '/rm/public/cache/' . $cache_file_name;
    $cache_path_url = EW_ROOT_URL . "~rm/public/cache/$cache_file_name";

    if (file_exists($cache_path)) {
      $cache_path = EW_ROOT_URL . "~rm/public/cache/$cache_file_name";
      $minified_css = true;
    }
    else {
      foreach (self::$html_included_links as $source) {
        $src = EW_PACKAGES_DIR . '/' . $source;
        $buffer = file_get_contents($src);
        // Remove comments
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        // Remove space after colons
        $buffer = str_replace(': ', ':', $buffer);
        // Remove whitespace
        $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);

        $minified_css .= $buffer;
      }

      EWCore::file_force_contents($cache_path, $minified_css);
    }

    if ($minified_css) {
      $css_tag.="<link rel='stylesheet' property='stylesheet' type='text/css' id='ew-compiled-css' href='$cache_path_url' />";
    }

    return $css_tag;
  }

  public static function set_html_title($title) {
    self::$title = $title;
  }

  public static function get_html_title() {
    return self::$title;
  }

  public static function set_html_keywords($value) {
    if (is_array($value)) {
      self::$html_keywords = array_merge(self::$html_keywords, $value);
      return;
    }

    $keywords = explode(',', $value);
    $trimmed_keywords = array_map('trim', $keywords);

    self::$html_keywords = array_merge(self::$html_keywords, $trimmed_keywords);
  }

  public static function get_html_keywords() {
    return implode(',', array_filter(array_unique(self::$html_keywords)));
  }

  public function show_container($container_id) {
    $db = \EWCore::get_db_connection();
    $items = $db->query("SELECT * FROM ui_structures_parts WHERE container_id = '$container_id'  ORDER BY ui_structures_parts.order") or die($db->error);

    while ($rows = $items->fetch_assoc()) {
      if ($rows["item_type"] == 'panel') {
        $this->open_panel($rows["id"], $rows["container_id"], $rows["style_class"], $rows["style_id"], $rows["widgets_parameters"], FALSE);
        $this->show_container($rows["id"]);
        $this->close_panel();
      }
      else if ($rows["item_type"] == 'widget') {
        $this->open_widget($rows["id"], $rows["widget_type"], $rows["style_class"], $rows["style_id"], $rows["widgets_parameters"]);
        $this->close_widget();
      }
    }
  }

  public function set_uis($path = null, $uis_id = null) {
    $path = ($path) ? $path : $_REQUEST["path"];
    $uis_id = ($uis_id) ? $uis_id : $_REQUEST["uisId"];
    $db = \EWCore::get_db_connection();
    $res = array(
        "status" => "success",
        message  => "UIS has been set successfully for $path");
    if (!$uis_id) {
      $result = $db->query("DELETE FROM ew_pages_ui_structures WHERE path = '$path'");
      if ($result) {
        return json_encode($res);
      }
    }
    $db->query("SELECT * FROM ew_pages_ui_structures WHERE path = '$path'") or die($db->error);


    if ($db->affected_rows == 0) {
      $stm = $db->prepare("INSERT INTO ew_pages_ui_structures(path ,ui_structure_id ) VALUES(?,?)") or die($db->error);
      $stm->bind_param("ss", $path, $uis_id);
      if ($stm->execute())
        $res = array(
            "status" => "success",
            message  => "UIS has been set successfully for $path ",
            "puisId" => $stm->insert_id);
      else
        $res = array(
            "status" => "error",
            message  => "UIS has NOT been sat, Please try again");
    }
    else {
      $stm = $db->prepare("UPDATE ew_pages_ui_structures SET  ui_structure_id = ?  WHERE path = ?") or die($db->error);
      $stm->bind_param("ss", $uis_id, $path);
      if (!$stm->execute())
        $res = array(
            "status" => "error",
            message  => "UIS has NOT been sat, Please try again");
    }

    $stm->close();
    $db->close();
    return json_encode($res);
  }

  public static function get_path_uis($path = null) {
    $path = ($path) ? $path : $_REQUEST["path"];
    $db = \EWCore::get_db_PDO();
    $result = $db->prepare("SELECT ew_ui_structures.id AS id,name,template, template_settings,path FROM ew_pages_ui_structures,ew_ui_structures"
            . " WHERE ew_pages_ui_structures.ui_structure_id = ew_ui_structures.id"
            . " AND path = ?") or die($db->error);
    $result->execute([$path]);

    if ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
      return [
          "uis_id"                => $row["id"],
          "uis_name"              => $row["name"],
          "uis_template"          => $row["template"],
          "uis_template_settings" => $row["template_settings"]
      ];
    }
    else {
      return null;
    }
  }

  public static function get_layout($uisId, $template = null, $template_settings = null) {
    $layout = WidgetsManagement::generate_view($uisId);
    $template_body = $layout["body_html"];
    $widget_data = $layout["widget_data"];
    $settings = json_decode($template_settings, true);

    if (!$template) {
      $uis_info = WidgetsManagement::get_uis($uisId);
      $template = $uis_info["template"];
      $template_settings = $uis_info["template_settings"];
    }

    if (!isset($template_settings)) {
      $template_settings = '{}';
    }

//    if(is_array($template_settings)){
//      $template_settings = json_encode($template_settings);
//    }

    if (file_exists(EW_TEMPLATES_DIR . $template . '/template.php')) {
      require_once EW_TEMPLATES_DIR . $template . '/template.php';
      $template = new \template();
      $template_body = $template->get_template_body($template_body, $settings);
      $template_script = $template->get_template_script($settings);
      $template_script = preg_replace('/\'json\|\$template_settings\'/', $template_settings, $template_script);
    }

    return [
        "template_body"   => $template_body,
        "template_script" => $template_script,
        "widget_data"     => $widget_data
    ];
  }

  public static function add_widget_feeder($type, $app, $id, $function) {
    if (!isset(self::$registry[static::$WIDGET_FEEDER]) || !array_key_exists($app, self::$registry[static::$WIDGET_FEEDER])) {
      self::$registry[static::$WIDGET_FEEDER][$app] = array();
    }

    if (!isset(self::$registry[static::$WIDGET_FEEDER][$app][$type])) {
      self::$registry[static::$WIDGET_FEEDER][$app][$type] = array();
    }

    self::$registry[static::$WIDGET_FEEDER][$app][$type][$id] = $function;
  }

  /**
   * Check whether widget feeder exists
   * @param type $type
   * @param string $app
   * @param type $id
   * @return boolean returns app name if the $app parameter is set to * or true if the app name is specefied and false in other cases
   */
  public static function is_widget_feeder($type, $app, $id) {
    if (!$app && $app != '*')
      $app = 'admin';
    $func = null;
    $feederApp = true;
    $result = false;
    if (!isset(self::$registry[static::$WIDGET_FEEDER])) {
      return false;
    }

    array_walk(self::$registry[static::$WIDGET_FEEDER], function($item, $key)use ($type, $app, $id, &$feederApp, &$result) {
      if ($app == "*" || $app == $key) {
        if ($type == '*') {
          foreach ($item as $feeder => $p) {
            if (isset($p[$id])) {
              //echo $key." ".$feeder."  ".$id;
              $result = true;
            }
          }
        }
        else if ($item[$type][$id]) {
          $feederApp = $key;
          $result = true;
        }
      }
    });
    if ($result)
      return $feederApp;
    // Check all thge apps for specified feeder
    $all_feeders = self::$registry[static::$WIDGET_FEEDER];
    if ($app == "*") {
      foreach ($all_feeders as $feeder => $p) {
        if (isset($p[$type][$id]))
          return $feeder;
      }
      return FALSE;
    }
    if (!$app)
      $app = 'admin';


    //$feeder = EWCore::read_registry(static::$WIDGET_FEEDER);
    if ($all_feeders[$app][$type][$id]) {
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
  public static function get_widget_feeder($id) {
    return static::$widgets_feeders[$id];
  }

  /**
   * 
   * @param String $id Id of feeder
   * @return \ew\WidgetFeeder
   */
  public static function get_widget_feeder_by_url($id) {
    $id = (substr($id, -1) === "/") ? $id : "$id/";
    $id = EWCore::camel_to_hyphen($id);

    if (isset(static::$widgets_feeders)) {
      foreach (static::$widgets_feeders as $feeder_id => $feeder_conf) {
        if ($feeder_conf->url === $id) {
          return $feeder_conf;
        }
      }
    }
    return null;
  }

  /**
   * 
   * @param \ew\Module $module
   * @param \ew\WidgetFeeder $feeder
   */
  public static function register_widget_feeder($feeder) {
    static::$widgets_feeders[$feeder->id] = $feeder;
  }

  /**
   * 
   * @type string type of widget feeder
   * @return mixed
   */
  public static function get_widget_feeders($type = "all") {
    $feeders = [];
    foreach (static::$widgets_feeders as $feeder_id => $feeder_config) {
      if ($feeder_config->feeder_type === $type || $type === "all") {
        $feeders[] = $feeder_config;
      }
    }

    return \ew\APIResourceHandler::to_api_response($feeders, ["totalRows" => count($feeders)]);
  }

  public static function parse_html_to_parts($html_string) {
    $doc = new \DOMDocument();
    $doc->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $html_string);

    $xpath = new \DOMXpath($doc);
    $parts = $xpath->query('//body/*');
    $links = array();
    foreach ($parts as $part) {
      $tag_name = $part->tagName;
      $text = trim($part->nodeValue);
      $links[] = [
          'content-field' => $part->getAttribute('content-field'),
          'tag'           => $tag_name,
          'text'          => $text,
          'html'          => $doc->saveHTML($part),
      ];
    }

    return $links;
  }

  public function create_layouts($_input, $_response) {
    
  }

  public function read_layouts($_input, $_response, $_parts__id) {
    $_input->id = $_parts__id;
    $result = (new LayoutsRepository())->read($_input);

    if ($result->error) {
      $_response->set_status_code($result->error);
      return $result;
    }

    $_response->properties['total'] = $result->total;
    $_response->properties['page_size'] = $result->size;

    return $result->data;
  }

  public function update_layouts($_input, $_response) {
    
  }

  public function delete_layouts($_input, $_response) {
    
  }

  public function get_title() {
    return "Widgets";
  }

  public function get_description() {
    return "Manage the layouts of pages, add or remove widgets";
  }

}
