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
class HTMLResourceHandler extends ResourceHandler
{

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

   protected function handle($app, $app_resource_path, $module_name, $method_name, $parameters = null)
   {
      $method_name = ($method_name == 'index.php') ? 'index' : $method_name;
      $module_name = \EWCore::hyphenToCamel($module_name);
      if (\EWCore::is_widget_feeder("*", "*", $module_name))
      {
         // Show index if the URL contains a page feeder
         ob_start();
         $app->index();
         return ob_get_clean();
      }
      else if ($module_name)
      {
         
         if ($parameters["_file"])
         {
            //$content_type = substr($parameters["_file"], strripos($parameters["_file"], '.') + 1);
            $path = implode('/', $app_resource_path) . '/' . $module_name . '/' . $parameters["_file"];
            //echo EW_PACKAGES_DIR . '/' .$path;
            
         }
         else
         {
            $path = implode('/', $app_resource_path) . '/' . $module_name . '/index.php';
         }
      }
      else
      {
         if (!isset($parameters["_file"]))
         {
            // Refer to app index
            ob_start();
            $app->index();
            return ob_get_clean();
         }

         // Refer to app section index
         $path = implode('/', $app_resource_path) . '/' . $parameters["_file"];
      }

      if ($path && file_exists(EW_PACKAGES_DIR . '/' . $path))
      {

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
   }

}
