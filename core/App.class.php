<?php

namespace ew;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of App
 *
 * @author Eeliya
 */
class App {

  public static $EW_APP = "app";
  public static $Core_APP = "core_app";
  public static $app_root_path;
  protected $name = "EW App";
  protected $description = "This is a ew app";
  protected $version = "0.1";
  protected $type = "app";
  protected $namespace = "";
  protected $default_resource = "html";
  private $resource_handlers = [];
  private $loaded_modules = [];
  private $loaded = false;

  public function __construct() {
    if ($this->loaded) {
      return;
    }

    static::$app_root_path = str_replace('_', '-', $this->get_root());

    $this->loaded = true;
    $this->load_assets();
    $this->install_resource_handlers();
  }

  protected function init() {
    
  }

  public function init_app() {
    $this->init();
    for ($in = 0, $len = count($this->loaded_modules); $in < $len; $in++) {
      $this->loaded_modules[$in]->init();
    }
  }

  private function read_modules($dir) {
    $app_root = $this->get_root();
    $path = EW_PACKAGES_DIR . '/' . static::$app_root_path . '/' . $dir;
    if (!file_exists($path)) {
      return [];
    }

    $sections = scandir($path);

    $dependencies = [];

    for ($in = 0, $len = count($sections); $in < $len; $in++) {
      $section_name = $sections[$in];

      if (strpos($section_name, '.') === 0) {
        continue;
      }

      if (!$i = strpos($section_name, '.class.php')) {
        continue;
      }

      include_once $path . '/' . $section_name;

      $section_class_name = substr($section_name, 0, $i);
      $dependencies[] = "$app_root\\$section_class_name";
    }

    return $dependencies;
  }

  protected function load_assets() {
//    $app_root = $this->get_root();
//    $path = EW_PACKAGES_DIR . '/' . $app_root . '/' . $dir;

    try {
      $this->read_modules('api/repositories');
      $this->load_and_populate_modules();
    }
    catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  protected function install_resource_handlers() {
    $this->register_resource_handler('api', 'ew\\APIResourceHandler');
    $this->register_resource_handler($this->default_resource, 'ew\\HTMLResourceHandler');
  }

  public function load_and_populate_modules() {
    $apis = $this->read_modules('api');

    $this->loaded_modules = [];

    foreach ($apis as $api) {
      if (array_key_exists('ew\Module', class_parents($api))) {
        $this->loaded_modules [] = new $api($this);
      }
    }
  }

  public function process_command($package, $resource_type, $module_name, $method_name, $parameters = null) {
    if ($this->resource_handlers[$resource_type]) {
      return $this->get_resource_handler($resource_type)->process($this, $package, $resource_type, $module_name, $method_name, $parameters);
    }
    else {
      $error = \EWCore::log_error(404, "Resource not found: `$resource_type/$module_name/$method_name`", [
                  "package"  => $package,
                  "resource" => $resource_type,
                  "module"   => $module_name,
                  "method"   => $method_name
      ]);

      if ($parameters["_APIResourceHandler_output_array"]) {
        return $error;
      }

      return json_encode($error);
    }
  }

  public function get_root() {
    $ro = new \ReflectionClass($this);
    return $ro->getNamespaceName();
  }

  public function get_name() {
    return $this->name;
  }

  public function get_description() {
    return $this->description;
  }

  public function get_app_version() {
    return $this->version;
  }

  public function get_type() {

    return $this->type;
  }

  public function get_app_details() {
    return array(
        "name"        => $this->name,
        "title"       => $this->name,
        "description" => $this->description,
        "version"     => $this->version,
        "type"        => $this->type,
        "root"        => $this->get_root());
  }

  public function get_path($path) {
    return $this->get_root() . '/' . $path;
  }

//  public function load_view($path, $view_data) {
//    if ($view_data) {
//      extract($view_data);
//    }
//    
//    $path = EW_PACKAGES_DIR . '/' . $this->get_root() . '/' . $path;
//
//    include $path;
//  }

  public function get_view($path, $view_data, $auto_populate = true) {
    $full_path = EW_PACKAGES_DIR . '/' . $this->get_root() . '/' . $path;

    if (!file_exists($full_path)) {
      return \EWCore::log_error(404, "<h4>View: File not found</h4><p>File `$full_path`, not found</p>");
    }

    ob_start();
    include $full_path;
    $view_content = ob_get_clean();

    if ($auto_populate) {
      return preg_replace_callback("/\{\{([\w]*)\}\}/", function($match) use ($view_data) {
        return $view_data[$match[1]];
      }, $view_content);
    }
    else {
      return $view_content;
    }
  }

  public function index() {
    return [
        'module' => 'home',
        'file'   => 'index.php'
    ];
  }

  public function register_resource_handler($name, $func) {
    $this->resource_handlers[$name] = $func;
  }

  private $cached_resource_handlers = [];

  private function get_resource_handler($resource_type) {
    $resource_handler_name = $this->resource_handlers[$resource_type];

    if ($this->cached_resource_handlers[$resource_handler_name]) {
      return $this->cached_resource_handlers[$resource_handler_name];
    }

    $this->cached_resource_handlers[$resource_handler_name] = new $resource_handler_name($this);

    return $this->cached_resource_handlers[$resource_handler_name];
  }

  public function get_app_api_modules() {
    $root = $this->get_root();
    $path = EW_PACKAGES_DIR . '/' . $root . '/api/';

    $modules = opendir($path);
    $sections = array();

    // Search app's root's dir

    while ($module_file = readdir($modules)) {
      if (strpos($module_file, '.') === 0)
        continue;
      //$i = strpos($section_dir, '.class.php');
      $module_full_name = substr($module_file, 0, strpos($module_file, '.class.php'));
      $module_name = $module_full_name;
      $namespace_class_name = str_replace('-', '\\', $root) . "\\" . $module_full_name;
      //echo $namespace_class_name . "<br>";
      if (class_exists($namespace_class_name)) {
        $module_full_name = $namespace_class_name;
      }

      if (class_exists($module_full_name) && get_parent_class($module_full_name) == 'ew\Module') {
        $module = new $module_full_name($this);
        $permission_id = \EWCore::does_need_permission($root, $module_name, $module->get_index());

        if ($permission_id && $permission_id !== FALSE) {
          // Check for user permission
          if (!UsersManagement::user_has_permission($root, $module_name, $permission_id, $_SESSION['EW.USER_GROUP_ID'])) {
            continue;
          }
        }

        if ($module->get_title() && !$module->is_hidden())
          $sections[] = array(
              "title"       => "tr:$appDir" . "{" . $module->get_title() . "}",
              "className"   => $module_name,
              "description" => "tr:$appDir" . "{" . $module->get_description() . "}");
      }
    }
    return ($sections);
  }

}
