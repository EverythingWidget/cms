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

   //put your code here

   public function init_app()
   {
      $this->init_plugins();
   }

   protected function init_plugins()
   {
      $app_root = $this->get_root();
      $path = EW_APPS_DIR . '/' . $app_root . '/';
      //echo $path;
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

            //if (class_exists($real_class_name))
            //{
               $sc = new $real_class_name(EWCore::get_app_instance($app_root));
               if (method_exists($sc, "init_plugin"))
               {
                  try
                  {
                     call_user_func(array($sc, "init_plugin"));
                  }
                  catch (Exception $e)
                  {
                     echo $e;
                  }
               }
            //}
            /* else if (class_exists($section_class_name) && get_parent_class($section_class_name) == 'Section')
              {
              $sc = new $section_class_name(EWCore::get_app_instance($app_root));
              if (method_exists($sc, "init_plugin"))
              call_user_func(array($sc, "init_plugin"));
              } */
         }
      }
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

      return array("name" => $this->name, "description" => $this->description, "version" => $this->version, "type" => $this->type, "root" => $this->get_root());
   }

}
