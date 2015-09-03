<?php

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

   //put your code here

   public function init_app()
   {
      $this->init_plugins();
   }

   protected function init_plugins()
   {
      $app_root = $this->get_root();
      $path = EW_PACKAGES_DIR . '/' . $app_root . '/';

      $section_dirs = opendir($path);
      $sections = array();
      while ($section_dir = readdir($section_dirs))
      {
         if (strpos($section_dir, '.') === 0)
            continue;
         $section_files = opendir($path . $section_dir);

         while ($file = readdir($section_files))
         {
            $i = strpos($file, '.class.php');
            if (strpos($file, '.') === 0 || !$i)
               continue;
            //echo $path . $section_dir . $file;
            require_once $path . $section_dir . '/' . $file;
            $section_class_name = substr($file, 0, $i);
            $real_class_name = "$app_root\\$section_class_name";
            $sc = new $real_class_name($this);
            if (method_exists($sc, "init_plugin"))
            {
               try
               {
                  call_user_func(array(
                      $sc,
                      "init_plugin"));
               }
               catch (Exception $e)
               {
                  echo $e;
               }
            }
         }
      }
   }

   public function process_command($section_name, $method_name, $parameters = null)
   {
      $app_name = $this->get_root();
      $real_class_name = $app_name . '\\' . $section_name;

      $class_exist = false;
      // If class has namespace
      if ($section_name && class_exists($real_class_name))
      {
         // Create an instance of section with its parent App
         $app_section_object = new $real_class_name($this);
         $class_exist = true;
      }

      $pages_feeders = EWCore::read_registry("ew-widget-feeder");
      if ($class_exist)
      {

         $RESULT_CONTENT = $app_section_object->process_request($method_name, $parameters);
      }
      else if (EWCore::is_widget_feeder("*", "*", $section_name))
      {

         // Show index if the URL contains a page feeder
         $path = EW_PACKAGES_DIR . '/' . $app_name . '/index.php';
      }
      else if (!$section_name)
      {
         // Refer to app index
         if ($method_name == 'index')
         {
            ob_start();
            $this->index();
            return ob_get_clean();
         }
         $path = EW_PACKAGES_DIR . '/' . $app_name . '/' . $method_name . '.php';

         //echo "here is app-in $path";
      }
      else
      {

         // Refer to app section index
         $path = EW_PACKAGES_DIR . '/' . $app_name . '/' . $section_name . '/' . $method_name;
      }


      if ($path && file_exists($path))
      {
         ob_start();
         include $path;
         $RESULT_CONTENT = ob_get_clean();
      }
      else if ($path)
      {
         $RESULT_CONTENT = EWCore::log_error(404, "<h4>{$path}</h4><p>$app_name: FILE NOT FOUND</p>");
      }
      return $RESULT_CONTENT;
   }

   public function get_root()
   {
      $ro = new ReflectionClass($this);
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
      $path = EW_PACKAGES_DIR . '/' . $this->get_root() . '/' . $path;
      ob_start();
      include $path;
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

}
