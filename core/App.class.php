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
   // our list of mime types
   private $mime_types = array(
       "pdf" => "application/pdf",
       "exe" => "application/octet-stream",
       "zip" => "application/zip",
       "docx" => "application/msword",
       "doc" => "application/msword",
       "xls" => "application/vnd.ms-excel",
       "ppt" => "application/vnd.ms-powerpoint",
       "gif" => "image/gif",
       "png" => "image/png",
       "jpeg" => "image/jpg",
       "jpg" => "image/jpg",
       "mp3" => "audio/mpeg",
       "wav" => "audio/x-wav",
       "mpeg" => "video/mpeg",
       "mpg" => "video/mpeg",
       "mpe" => "video/mpeg",
       "mov" => "video/quicktime",
       "avi" => "video/x-msvideo",
       "3gp" => "video/3gpp",
       "css" => "text/css",
       "jsc" => "application/javascript",
       "js" => "application/javascript",
       "php" => "text/html",
       "htm" => "text/html",
       "html" => "text/html");

   public function get_mime_type($path)
   {
      $extension = strtolower(end(explode('.', $path)));
      return $this->mime_types[$extension];
   }

   public function __construct()
   {
      $this->addResource("api", function($app_resource_path, $section_name, $method_name, $parameters = null)
      {
         $app_name = $this->get_root();
         $real_class_name = $app_name . '\\' . $section_name;
         if (!$section_name)
         {
            return \EWCore::log_error(400, "<h4>$app_name-api </h4><p>Please specify the api command</p>");
         }
         if (class_exists($real_class_name))
         {
            $app_section_object = new $real_class_name($this);
            return $app_section_object->process_request($method_name, $parameters);
         }
         else
         {
            return \EWCore::log_error(404, "<h4>$app_name-api </h4><p>Section `$section_name` not found</p>");
         }
      });

      $this->addResource($this->default_resource, function($app_resource_path, $section_name, $method_name, $parameters = null)
      {
         if (\EWCore::is_widget_feeder("*", "*", $section_name))
         {
            // Show index if the URL contains a page feeder
            ob_start();
            $this->index();
            return ob_get_clean();
         }
         else if ($section_name)
         {
            if ($parameters["_file"])
            {
               //$content_type = substr($parameters["_file"], strripos($parameters["_file"], '.') + 1);
               $path = implode('/', $app_resource_path) . '/' . $section_name . '/' . $parameters["_file"];
               //echo EW_PACKAGES_DIR . '/' .$path;
            }
            else
            {
               $path = implode('/', $app_resource_path) . '/' . $section_name . '/index.php';
            }
         }
         else
         {
            if (!$parameters["_file"])
            {
               // Refer to app index
               ob_start();
               $this->index();
               return ob_get_clean();
            }
            // Refer to app section index
            $path = implode('/', $app_resource_path) . '/' . $parameters["_file"];
         }

         if ($path && file_exists(EW_PACKAGES_DIR . '/' . $path))
         {
            //$finfo = \finfo::file( EW_PACKAGES_DIR . '/' . $path,FILEINFO_MIME_TYPE);
            //$content_type = \finfo_file($finfo, EW_PACKAGES_DIR . '/' . $path);
            //\finfo_close($finfo);
            //echo "asdasd";
            
            if ($this->get_mime_type($path))
               header("Content-Type: " . $this->get_mime_type($path));
            //http_response_code(200);
            ob_start();
            include EW_PACKAGES_DIR . '/' . $path;
            return ob_get_clean();
         }
         else if ($path)
         {
            return \EWCore::log_error(404, "<h4>Constract: File not found</h4><p>File `$path`, not found</p>");
         }
      });
   }

   //put your code here

   public function init_app()
   {

      $this->init_api();
      //$this->init_plugins();
   }

   protected function init_api()
   {
      $app_root = $this->get_root();
      $path = EW_PACKAGES_DIR . '/' . $app_root . '/api/';
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

         require_once $path . $section_name;

         $section_class_name = substr($section_name, 0, $i);
         $real_class_name = "$app_root\\$section_class_name";
         //echo $real_class_name .' loaded <br>';
         $sc = new $real_class_name($this);
         //if (method_exists($sc, "init_plugin"))
         //{
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
         //}
      }
   }

   protected function init_plugins()
   {
      $app_root = $this->get_root();
      $path = EW_PACKAGES_DIR . '/' . $app_root . '/';

      $section_dirs = opendir($path);
      //$sections = array();
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

   public function process_command($app_resource_path, $section_name, $method_name, $parameters = null)
   {
      $app_name = $this->get_root();
      $real_class_name = $app_name . '\\' . $section_name;

      $class_exist = false;
      //print_r($app_resource_path);
      //echo $real_class_name;
      // If class has namespace

      if ($this->resources[$app_resource_path[1]])
      {
         return $this->resources[$app_resource_path[1]]($app_resource_path, $section_name, $method_name, $parameters);
         
      }
      else
      {
         return \EWCore::log_error(404, "<h4>Resource not found</h4><p>Resource `$app_resource_path[1]/$section_name/$method_name`, not found</p>");
      }
      /* if ($app_resource_path[1] === "api")
        {
        // Create an instance of section with its parent App
        if (!$section_name)
        {
        return \EWCore::log_error(400, "<h4>$app_name-api </h4><p>Please specify the api command</p>");
        }
        if (class_exists($real_class_name))
        {
        $app_section_object = new $real_class_name($this);
        return $app_section_object->process_request($method_name, $parameters);
        }
        else
        {
        return \EWCore::log_error(404, "<h4>$app_name-api </h4><p>Section `$section_name` not found</p>");
        }
        } */

      //$pages_feeders = \EWCore::read_registry("ew-widget-feeder");
      /* if ($class_exist)
        {

        $RESULT_CONTENT = $app_section_object->process_request($method_name, $parameters);
        }
        else */
      //return $RESULT_CONTENT;
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
