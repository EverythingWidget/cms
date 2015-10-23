<?php
namespace admin;

use Module;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FileManager
 *
 * @author Eeliya
 */
class FileManager extends Module
{

  private $file_types;

  public function __construct($secName, $request)
  {
    parent::__construct($secName, $request);
    $this->sectionDir = "core/";
    $this->file_types = array("jpeg" => "image",
        "png" => "image",
        "gif" => "image",
        "txt"=>"text",
        "mp3"=>"sound",
        "mp4"=>"video");
  }

  //put your code here

  public function list_dir($path)
  {
    if (isset($_REQUEST["path"]))
      $path = $_REQUEST["path"];

    $root = EW_ROOT_DIR ;
    try
    {

      $dir_contents = opendir($root . $path);
      $files = array();

      while ($file = readdir($dir_contents))
      {
        if (strpos($file, '.') === 0)
          continue;
        $file_path = $root . $path . '/' . $file;
        $file_info = pathinfo($file_path);
        if (is_dir($file_path))
          $files[] = array(name => $file, type => "folder", size => "",ext => $file_info["extension"]);
        else
          $files[] = array(name => $file, type => $this->file_types[$file_info["extension"]]==null?"unknown":$this->file_types[$file_info["extension"]], size => filesize($file_path), ext => $file_info["extension"]);
      }
    } catch (Exception $e)
    {
      echo $e->getMessage();
    }
    return json_encode($files);
  }
  
  public function get_title()
  {
    return "File Manager";
  }
  public function get_description()
  {
    return "Upload media and manage them";
  }
  
  public function is_hidden()
  {
    return TRUE;
  }
}

?>
