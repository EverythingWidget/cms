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

   public function init()
   {
      
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
   
   public function get_plugin_details()
   {
      
      return array("name"=>  $this->name,"description"=>  $this->description,"version"=>  $this->version,"type"=>  $this->type,"root"=>  $this->get_root());
   }

}

