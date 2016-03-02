<?php

namespace ew;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HTMLResourceHandler
 *
 * @author Eeliya
 */
class HTMLResourceHandler extends ResourceHandler {

  private $mime_types = array(
      "pdf"  => "application/pdf",
      "exe"  => "application/octet-stream",
      "zip"  => "application/zip",
      "docx" => "application/msword",
      "doc"  => "application/msword",
      "xls"  => "application/vnd.ms-excel",
      "ppt"  => "application/vnd.ms-powerpoint",
      "gif"  => "image/gif",
      "png"  => "image/png",
      "jpeg" => "image/jpg",
      "jpg"  => "image/jpg",
      "mp3"  => "audio/mpeg",
      "wav"  => "audio/x-wav",
      "mpeg" => "video/mpeg",
      "mpg"  => "video/mpeg",
      "mpe"  => "video/mpeg",
      "mov"  => "video/quicktime",
      "avi"  => "video/x-msvideo",
      "3gp"  => "video/3gpp",
      "css"  => "text/css",
      "jsc"  => "application/javascript",
      "js"   => "application/javascript",
      "php"  => "text/html",
      "htm"  => "text/html",
      "html" => "text/html");
  protected $directly_accessible = [
      "js",
      "css",
      "html"
  ];

  public function get_mime_type($path) {
    $extension = strtolower(end(explode('.', $path)));
    return $this->mime_types[$extension];
  }

  public function get_extension($path) {
    return strtolower(end(explode('.', $path)));
  }

  protected function handle($app, $package, $resource_type, $module_name, $method_name, $parameters = null) {
    $matches = array();
    preg_match('/(.*\.[^-]{2,4})/', $parameters["_file"], $matches);
    $file = isset($matches[1]) ? $matches[1] : $parameters["_file"];
    $path = null;

    $this->find_uis($module_name, $file);
    //return $_REQUEST["_uis"];
    if (\webroot\WidgetsManagement::get_widget_feeder_by_url($module_name)) {
      // Show index if the URL contains a page feeder
      $app_index = $app->index();
      //$module_name = $app_index['module'];
      //$method_name = $app_index['file'];
      $path = $package . '/' . $resource_type . '/' . $app_index['module'] . '/' . $app_index['file'];
      //return $_REQUEST["_uis"];
    }
    else if ($module_name && $file) {
      $path = $package . '/' . $resource_type . '/' . $module_name . '/' . $file;
    }
    else if (!isset($file)) {
      // if file is not specified, then open the index
      // app index file or package/index.php file should be public and accessible via all the users
      // any sort of permission should implemented by the developer
      $app_index = $app->index();
      $module_name = $app_index['module'];
      $method_name = $app_index['file'];
      $path = $package . '/' . $resource_type . '/' . $module_name . '/' . $method_name;
    }

    if ($path !== null && is_file(EW_PACKAGES_DIR . '/' . $path)) {
      //$ext = $this->get_extension($path);
      $type = $this->get_mime_type($path);

      /* if (in_array($ext, $this->directly_accessible))
        {
        header("content-type: " . $type);
        return file_get_contents(EW_PACKAGES_DIR . '/' . $path, NULL);
        } */
      if (\admin\UsersManagement::user_has_permission($app->get_root(), 'html', $module_name, $method_name)) {
        $res = $this->load_file(EW_PACKAGES_DIR . '/' . $path, $type, $parameters);
        header("Content-Type: text/html");
        return $res;
      }
      else {
        return json_encode(\EWCore::log_error(403, "You do not have corresponding permission to access this file", array(
                    "Access Denied" => "$app_name/$module_name/$method_name")));
      }
    }
    else {
      //echo $app_resource_path[1];
      return json_encode(\EWCore::log_error(404, "File not found: `$path`", [
                  "app/resource: " . $package . '/' . $resource_type,
                  "module: $module_name",
                  "file: $file"
      ]));
    }
  }

  private function find_uis($module_name, $file) {
    $r_uri = strtok($_SERVER["REQUEST_URI"], "?");

    // If root dir is same with the uri then refer to the base url
    if ($root_dir == str_replace("/", "", $r_uri))
      $r_uri = "/";

    // Check if UI structure is specified
    if (!isset($_REQUEST["_uis"])) {
      if ($module_name) {
        $r_uri = "/$module_name/";
      }
      if ($file) {
        $r_uri.= $file;
      }
      //$r_uri = str_replace('/' . $root_dir, "", $r_uri);

      $uis_data = static::get_url_uis($r_uri);
      $_REQUEST["_uis"] = $uis_data["uis_id"];
      $_REQUEST["_uis_template"] = $uis_data["uis_template"];
      if (!isset($_REQUEST["_uis_template_settings"]))
        $_REQUEST["_uis_template_settings"] = $uis_data["uis_template_settings"];
    }
    else {
      $uis_data = \webroot\WidgetsManagement::get_uis($_REQUEST["_uis"]);
      $_REQUEST["_uis_template"] = $uis_data["template"];
      if (!$_REQUEST["_uis_template_settings"])
        $_REQUEST["_uis_template_settings"] = $uis_data["template_settings"];
    }

    //var_dump($uis_data);

    if (isset($_REQUEST["_parameters"])) {
      $GLOBALS["page_parameters"] = explode("/", $_REQUEST["_parameters"]);
    }
  }

  public static function get_url_uis($url) {
    $dbc = \EWCore::get_db_PDO();
    // if the url is the root, the home layout will be set
    if ($url == "/" || $url === EW_DIR || $url === EW_DIR . $_REQUEST["_language"] . "/") {
      $url = "@HOME_PAGE";
    }
    $url.='%';
    //echo EW_DIR.$_REQUEST["_language"]."ssss";
    $stm = $dbc->prepare("SELECT * FROM ew_pages_ui_structures,ew_ui_structures "
            . "WHERE ew_ui_structures.id = ew_pages_ui_structures.ui_structure_id AND path LIKE ?") or die($dbc->error);
    $stm->execute([$url]);

    if ($row = $stm->fetch(\PDO::FETCH_ASSOC)) {
      
    }
    else {
      $dbc = \EWCore::get_db_PDO();
      $stm = $dbc->query("SELECT ui_structure_id, template, template_settings "
              . "FROM ew_pages_ui_structures,ew_ui_structures "
              . "WHERE ew_ui_structures.id = ew_pages_ui_structures.ui_structure_id "
              . "AND path = '@DEFAULT' ");
      $stm->execute();
      $row = $stm->fetch(\PDO::FETCH_ASSOC);
    }
    return [
        "uis_id"                => $row["ui_structure_id"],
        "uis_template"          => $row["template"],
        "uis_template_settings" => $row["template_settings"]
    ];
  }

  private function load_file($file_path, $type, $parameters) {
    header("Content-Type: " . $type);
    ob_start();
    //var_dump($parameters);
    include $file_path;
    return ob_get_clean();
  }

}
