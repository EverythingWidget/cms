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

   public function get_mime_type($path)
   {
      $extension = strtolower(end(explode('.', $path)));
      return $this->mime_types[$extension];
   }

   protected function handle($app, $app_resource_path, $module_name, $method_name, $parameters = null)
   {
      //$method_name = ($method_name == 'index.php') ? 'index' : $method_name;
      $matches = array();
      preg_match('/(.*\.[^-]{2,4})/', $parameters["_file"], $matches);
      $file = isset($matches[1]) ? $matches[1] : $parameters["_file"];
      $path = null;

      if (\EWCore::is_widget_feeder("*", "*", $module_name))
      {
         // Show index if the URL contains a page feeder
         ob_start();
         $app->index();
         return ob_get_clean();
      }
      else if ($module_name && $file)
      {
         $path = implode('/', $app_resource_path) . '/' . $module_name . '/' . $file;
      }
      else if (!isset($file))
      {
         // if file is not specified, then open the index
         // app index file or package/index.php file should be public and accessible via all the users
         // any sort of permission should implemented by the developer
         $app_index = $app->index();
         $module_name = $app_index['module'];
         $method_name = $app_index['file'];
         $path = implode('/', $app_resource_path) . '/' . $module_name . '/' . $method_name;
      }

      if ($path !== null && is_file(EW_PACKAGES_DIR . '/' . $path))
      {
         $permission_id = \EWCore::does_need_permission($app->get_root(), $module_name, 'html/' . $method_name);
         if ($permission_id && $permission_id !== FALSE)
         {
            if (\admin\UsersManagement::user_has_permission($app->get_root(), $module_name, $permission_id, $_SESSION['EW.USER_GROUP_ID']))
            {
               if ($this->get_mime_type($path))
               {
                  header("Content-Type: " . $this->get_mime_type($path));
               }
               ob_start();
               include EW_PACKAGES_DIR . '/' . $path;
               return ob_get_clean();
            }
            else
            {
               return \EWCore::log_error(403, "tr{You do not have permission for this command}", array(
                           "Access Denied" => "$app_name/$module_name/$method_name"));
            }
         }
         else
         {
            ob_start();
            include EW_PACKAGES_DIR . '/' . $path;
            return ob_get_clean();
         }
      }
      else
      {
         //echo $app_resource_path[1];
         return \EWCore::log_error(404, "<h4>Constract: File not found</h4><p>File `$path`, not found</p>");
      }
   }

}
