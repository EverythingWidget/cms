<?php

namespace admin;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LanguageComponent
 *
 * @author Eeliya
 */
class LanguageComponent implements \ContentComponent
{

   public function get_explorer_nav($key, $data)
   {
      $form = [
          "title" => "Language",
          "description" => "Language of the content",
          "url" => "html/admin/content-management/explorer-language.php"
      ];
      return $form;
   }

   public function get_form($key, $data)
   {
      $admin = new \admin\App();
      $form = [
          "title" => "Language",
          "description" => "Language of the content",
          "html" => $admin->get_view("html/content-management/label_language.php", $data)
      ];

      return $form;
   }

   public function on_hard_delete($content_id, $content_data, $label_data)
   {
      
   }

   public function on_insert($content_data, $label_data)
   {
      
   }

   public function on_soft_delete($content_id, $content_data, $label_data)
   {
      
   }

   public function on_update($content_id, $content_data, $label_data)
   {
      
   }

   public function get()
   {
      $config = [
          "title" => "Language",
          "description" => "Language of the content",
          "explorer" => "html/admin/content-management/explorer-language.php",
          "form" => "html/admin/content-management/label-language.php"
      ];
      return $config;
   }

//put your code here
}
