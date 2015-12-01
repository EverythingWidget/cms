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
  protected $directly_accessible = [
      "js",
      "css",
      "html"
  ];

  public function get_mime_type($path)
  {
    $extension = strtolower(end(explode('.', $path)));
    return $this->mime_types[$extension];
  }

  public function get_extension($path)
  {
    return strtolower(end(explode('.', $path)));
  }

  protected function handle($app, $package, $resource_type, $module_name, $method_name, $parameters = null)
  {
    $matches = array();
    preg_match('/(.*\.[^-]{2,4})/', $parameters["_file"], $matches);
    $file = isset($matches[1]) ? $matches[1] : $parameters["_file"];
    $path = null;

    if (\webroot\WidgetsManagement::is_widget_feeder("*", "*", $module_name))
    {
      // Show index if the URL contains a page feeder
      ob_start();
      $app->index();
      return ob_get_clean();
    }
    else if ($module_name && $file)
    {
      $path = $package . '/' . $resource_type . '/' . $module_name . '/' . $file;
    }
    else if (!isset($file))
    {
      // if file is not specified, then open the index
      // app index file or package/index.php file should be public and accessible via all the users
      // any sort of permission should implemented by the developer
      $app_index = $app->index();
      $module_name = $app_index['module'];
      $method_name = $app_index['file'];
      $path = $package . '/' . $resource_type . '/' . $module_name . '/' . $method_name;
    }

    if ($path !== null && is_file(EW_PACKAGES_DIR . '/' . $path))
    {
      //$ext = $this->get_extension($path);
      $type = $this->get_mime_type($path);

      /* if (in_array($ext, $this->directly_accessible))
        {
        header("content-type: " . $type);
        return file_get_contents(EW_PACKAGES_DIR . '/' . $path, NULL);
        } */

      if (\admin\UsersManagement::user_has_permission($app->get_root(), 'html', $module_name, $method_name))
      {
        return $this->load_file(EW_PACKAGES_DIR . '/' . $path, $type, $parameters);
      }
      else
      {
        return \EWCore::log_error(403, "You do not have corresponding permission to access this file", array(
                    "Access Denied" => "$app_name/$module_name/$method_name"));
      }
    }
    else
    {
      //echo $app_resource_path[1];
      return \EWCore::log_error(404, "File not found: `$path`", [
                  "app/resource: " . $package . '/' . $resource_type,
                  "module: $module_name",
                  "file: $file"
      ]);
    }
  }

  private function load_file($file_path, $type, $parameters)
  {
    header("Content-Type: " . $type);
    ob_start();
    //var_dump($parameters);
    include $file_path;
    return ob_get_clean();
  }

}
