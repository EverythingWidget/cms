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

  const PAGE_UIS_HANDLER = 'ew/html-resource-handler/page-uis-handler';

  public function get_mime_type($path) {
    $extension = strtolower(end(explode('.', $path)));
    return $this->mime_types[$extension];
  }

  public function get_extension($path) {
    return strtolower(end(explode('.', $path)));
  }

  protected function handle($app, $package_original, $resource_type, $module_name, $method_name, $parameters = null) {
    $package = str_replace('_', '-', $package_original);
    $matches = [];
    preg_match('/(.*\.[^-@]{2,4})/', $parameters["_file"], $matches);
    $file = isset($matches[1]) ? $matches[1] : $parameters["_file"];
    $path = null;

    $this->set_uis($module_name, $file);
    //return $_REQUEST["_uis"];
    if ($package === 'webroot' && \webroot\WidgetsManagement::get_widget_feeder_by_url($module_name)) {
      // Show index if the URL contains a page feeder
      $app_index = $app->index();
      $path = $package . '/' . $resource_type . '/' . $app_index['module'] . '/' . $app_index['file'];
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
      if (\admin\UsersManagement::user_has_permission($app->get_root(), 'html', $module_name, $method_name)) {
        $result = $this->load_file(EW_PACKAGES_DIR . '/' . $path, $type, $parameters);

        return $result;
      }
      else {
        return \EWCore::log_error(403, "You do not have corresponding permission to access this file", array(
                    "Access Denied" => "$package/$module_name/$method_name"));
      }
    }
    else {
      return \EWCore::log_error(404, "File not found: `$path`", [
                  "app/resource: " . $package . '/' . $resource_type,
                  "module: $module_name",
                  "file: $file"
      ]);
    }
  }

  private function set_uis($module_name, $file) {
    $request_url = strtok($_SERVER['REQUEST_URI'], '?');

    // If root dir is same with the uri then refer to the base url
    if ($root_dir == str_replace('/', '', $request_url))
      $request_url = '/';
//    echo('<h1>' . $request_url . 'aaa- ' . $_REQUEST['_uis'] . '</h1>');
//    print_r($_REQUEST);
//    die($module_name);
    // Check if UI structure is specified
    if (!isset($_GET['_uis'])) {
      if ($module_name) {
        $request_url = "/$module_name/";
      }

      if ($file) {
        $request_url.= $file;
      }

      $uis_data = static::find_url_uis($request_url);
      $_REQUEST['_uis'] = $uis_data['uis_id'];
      $_REQUEST['_uis_template'] = $uis_data['uis_template'];
      if (!isset($_REQUEST['_uis_template_settings']))
        $_REQUEST['_uis_template_settings'] = $uis_data['uis_template_settings'];
    }
    else {
      $uis_data = \webroot\WidgetsManagement::get_uis($_REQUEST['_uis']);
      $_REQUEST['_uis_template'] = $uis_data['template'];
      if (!$_REQUEST['_uis_template_settings']) {
        $_REQUEST['_uis_template_settings'] = json_encode($uis_data['template_settings']);
      }
    }



    if (isset($_REQUEST['_parameters'])) {
      $GLOBALS['page_parameters'] = explode("/", $_REQUEST['_parameters']);
    }
  }

  public static function find_url_uis($url) {
    $pdo = \EWCore::get_db_PDO();
    // if the url is the root, the home layout will be set
    if ($url == "/" || $url === EW_DIR || $url === EW_DIR . $_REQUEST["_language"] . "/") {
      $url = "@HOME_PAGE";
    }

    /*
     * The search priority is as follow:
     * 
     * #1 Search in static links
     * #2 Search in page uis
     * #3 Search in defaults
     */

    $statement = $pdo->prepare("SELECT * FROM ew_pages_ui_structures,ew_ui_structures "
            . "WHERE ew_ui_structures.id = ew_pages_ui_structures.ui_structure_id "
            . "AND path LIKE ?") or die($pdo->error);
    $statement->execute([$url . '%']);
    $row = $statement->fetch(\PDO::FETCH_ASSOC);

    if (!$row) {
      $page_uis_handlers = \EWCore::read_registry_as_array(self::PAGE_UIS_HANDLER);

      foreach ($page_uis_handlers as $handler) {
        if (method_exists($handler["object"], $handler["method"])) {
          $listener_method_object = new \ReflectionMethod($handler["object"], $handler["method"]);
          $arguments = [
              $url,
              array_values(array_filter(explode('/', $url)))
          ];

          $handler_result = $listener_method_object->invokeArgs($handler["object"], $arguments);

          // If a page uis handler return a result
          if (isset($handler_result)) {
            return $handler_result;
          }
        }
      }

      // This is the default case where no page uis handler could return a result
      $statement = $pdo->query("SELECT ui_structure_id, template, template_settings "
              . "FROM ew_pages_ui_structures,ew_ui_structures "
              . "WHERE ew_ui_structures.id = ew_pages_ui_structures.ui_structure_id "
              . "AND path = '@DEFAULT' ");
      $statement->execute();
      $row = $statement->fetch(\PDO::FETCH_ASSOC);
    }

    return [
        'uis_id'                => $row['ui_structure_id'],
        'uis_template'          => $row['template'],
        'uis_template_settings' => $row['template_settings']
    ];
  }

  private function load_file($file_path, $type, $parameters = []) {
    header("Content-Type: $type");

    ob_start();
    include $file_path;
    return ob_get_clean();
  }

}
