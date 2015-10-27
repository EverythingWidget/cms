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

   public function __construct()
   {
      $this->addResource("api", new APIResourceHandler($this));
      $this->addResource($this->default_resource, new HTMLResourceHandler($this));
   }

   //put your code here

   public function init_app()
   {
      $this->load_modules("api");
   }

   public function load_modules($dir)
   {
      $app_root = $this->get_root();
      $path = EW_PACKAGES_DIR . '/' . $app_root . '/' . $dir;
      if (!file_exists($path))
         return;
      $sections = scandir($path);
      //$section_dir = readdir($section_dirs);
      //echo count($sections);

      for ($in = 0, $len = count($sections); $in < $len; $in++)
      //while ($section_dir = readdir($section_dirs))
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
         $sc = new $real_class_name($this);
         try
         {
            if (get_parent_class($sc) === "ew\Module")
            {
               call_user_func([
                   $sc,
                   "install_permissions"]);
            }
         }
         catch (Exception $e)
         {
            echo $e;
         }
      }
   }

   public function process_command($app_resource_path, $module_name, $method_name, $parameters = null)
   {
      //session_destroy();
      $app_name = $this->get_root();

      $permission_id = \EWCore::does_need_permission($app_name);
      // Get permission id for the requested method or FALSE in the case of no permission id available
      if ($permission_id === true)
      {
         if (\admin\UsersManagement::user_has_permission_for_resource($app_name, $app_resource_path[1], $_SESSION['EW.USER_GROUP_ID']))
         {
            if ($this->resources[$app_resource_path[1]])
            {
               return $this->resources[$app_resource_path[1]]->process($this, $app_resource_path, $module_name, $method_name, $parameters);
            }
            else
            {
               return \EWCore::log_error(404, "<h4>Resource not found</h4><p>Resource `$app_resource_path[1]/$module_name/$method_name`, not found</p>");
            }
         }
         else
         {
            return \EWCore::log_error(403, "tr{You do not have permission for this resource}", array(
                        "Access Denied" => "$app_name/$module_name/$method_name"));
         }
      }

      if ($this->resources[$app_resource_path[1]])
      {
         return $this->resources[$app_resource_path[1]]->process($this, $app_resource_path, $module_name, $method_name, $parameters);
      }
      else
      {
         return \EWCore::log_error(404, "<h4>Resource not found</h4><p>Resource `$app_resource_path[1]/$module_name/$method_name`, not found</p>");
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
          "name" => $this->name,
          "description" => $this->description,
          "version" => $this->version,
          "type" => $this->type,
          "root" => $this->get_root());
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

      return preg_replace_callback("/\{\{([\w]*)\}\}/", function($match) use ($view_data)
      {
         return $view_data[$match[1]];
      }, $res);
   }

   public function index()
   {
      $path = $this->get_path('index.php');
      include $path;
   }

   public function addResource($name, $func)
   {
      $this->resources[$name] = $func;
   }

}
