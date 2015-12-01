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
class App
{

   public static $EW_APP = "app";
   public static $Core_APP = "core_app";
   protected $name = "EW App";
   protected $description = "This is a ew app";
   protected $version = "0.1";
   protected $type = "app";
   protected $namespace = "";
   protected $default_resource = "html";
   private $resources = [];
   private $loaded_modules = [];

   public function __construct()
   {
      $this->load_assets();
      $this->install_resource_handlers();
   }

   protected function load_assets()
   {
      $this->load_modules("api");
   }

   protected function install_resource_handlers()
   {
      $this->addResource("api", new APIResourceHandler($this));
      $this->addResource($this->default_resource, new HTMLResourceHandler($this));
   }

   public function init_app()
   {
      for ($in = 0, $len = count($this->loaded_modules); $in < $len; $in++)
      {
         $this->loaded_modules[$in]->init();
      }
   }

   public function load_modules($dir)
   {
      $app_root = $this->get_root();
      $path = EW_PACKAGES_DIR . '/' . $app_root . '/' . $dir;
      if (!file_exists($path))
         return;
      $sections = scandir($path);

      $this->loaded_modules = [];

      for ($in = 0, $len = count($sections); $in < $len; $in++)
      {
         $section_name = $sections[$in];
         if (strpos($section_name, '.') === 0)
         {
            continue;
         }

         if (!$i = strpos($section_name, '.class.php'))
         {
            continue;
         }

         require_once $path . '/' . $section_name;

         $section_class_name = substr($section_name, 0, $i);
         $real_class_name = "$app_root\\$section_class_name";

         if (array_key_exists('ew\Module', class_parents($real_class_name)))
         {
            $this->loaded_modules [] = new $real_class_name($this);
         }
      }
   }

   public function process_command($package,$resource_type, $module_name, $method_name, $parameters = null)
   {
      //session_destroy();
      //$app_name = $this->get_root();
//      $permission_id = \EWCore::does_need_permission($app_name);
//      //var_dump($app_name);
//      // Get permission id for the requested method or FALSE in the case of no permission id available
//      if ($permission_id === true)
//      {
//         if (\admin\UsersManagement::user_has_permission_for_resource($app_name, $app_resource_path[1], $_SESSION['EW.USER_GROUP_ID']))
//         {
      if ($this->resources[$resource_type])
      {
         return $this->resources[$resource_type]->process($this, $package,$resource_type, $module_name, $method_name, $parameters);
      }
      else
      {
         return \EWCore::log_error(404, "Resource not found: `$resource_type/$module_name/$method_name`", [
                     "package"  => $package,
                     "resource" => $resource_type,
                     "module"   => $module_name,
                     "method"   => $method_name
         ]);
      }
//         }
//         else
//         {
//            return \EWCore::log_error(403, "tr{You do not have permission for this resource}", array(
//                        "Access Denied" => "$app_name/$module_name/$method_name"));
//         }
//      }

      if ($this->resources[$package[1]])
      {
         return $this->resources[$package[1]]->process($this, $package, $module_name, $method_name, $parameters);
      }
      else
      {
         return \EWCore::log_error(404, "<h4>Resource not found</h4><p>Resource `$package[1]/$module_name/$method_name`, not found</p>");
      }
   }

   public function get_root()
   {
      $ro = new \ReflectionClass($this);
      return $ro->getNamespaceName();
   }

   public function get_name()
   {
      return $this->name;
   }

   public function get_description()
   {
      return $this->description;
   }

   public function get_app_version()
   {
      return $this->version;
   }

   public function get_type()
   {

      return $this->type;
   }

   public function get_app_details()
   {
      return array(
          "name"        => $this->name,
          "description" => $this->description,
          "version"     => $this->version,
          "type"        => $this->type,
          "root"        => $this->get_root());
   }

   public function get_path($path)
   {
      return $this->get_root() . '/' . $path;
   }

   public function load_view($path, $view_data)
   {
      if ($view_data)
         extract($view_data);
      $path = EW_PACKAGES_DIR . '/' . $this->get_root() . '/' . $path;

      include $path;
   }

   public function get_view($path, $view_data)
   {
      $full_path = EW_PACKAGES_DIR . '/' . $this->get_root() . '/' . $path;

      if (!file_exists($full_path))
      {
         return \EWCore::log_error(404, "<h4>View: File not found</h4><p>File `$full_path`, not found</p>");
      }
      ob_start();
      include $full_path;
      $res = ob_get_clean();

      return preg_replace_callback("/\{\{([\w]*)\}\}/", function($match) use ($view_data) {
         return $view_data[$match[1]];
      }, $res);
   }

   public function index()
   {
      return [
          'module' => 'html',
          'file'   => 'index.php'
      ];
   }

   public function addResource($name, $func)
   {
      $this->resources[$name] = $func;
   }

   public function get_app_api_modules()
   {
      $root = $this->get_root();
      $path = EW_PACKAGES_DIR . '/' . $root . '/api/';

      $modules = opendir($path);
      $sections = array();

      // Search app's root's dir

      while ($module_file = readdir($modules))
      {
         if (strpos($module_file, '.') === 0)
            continue;
         //$i = strpos($section_dir, '.class.php');
         $module_full_name = substr($module_file, 0, strpos($module_file, '.class.php'));
         $module_name = $module_full_name;
         $namespace_class_name = $root . "\\" . $module_full_name;
         //echo $namespace_class_name . "<br>";
         if (class_exists($namespace_class_name))
         {
            $module_full_name = $namespace_class_name;
         }

         if (class_exists($module_full_name) && get_parent_class($module_full_name) == 'ew\Module')
         {
            $module = new $module_full_name($this);
            $permission_id = \EWCore::does_need_permission($root, $module_name, $module->get_index());

            if ($permission_id && $permission_id !== FALSE)
            {
               // Check for user permission
               if (!UsersManagement::user_has_permission($root, $module_name, $permission_id, $_SESSION['EW.USER_GROUP_ID']))
               {
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
