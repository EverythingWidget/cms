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
class DocumentComponent implements \ContentComponent
{

   public function get_explorer_nav($key, $value)
   {
      $form = [
          "title" => "Document",
          "description" => "Language of the content",
          "url" => "admin-api/ContentManagement/explorer-document.php"
      ];
      return $form;
   }

   public function get_form($key, $data)
   {
      $admin = new \admin\Admin();
      $form = [
          "title" => "Document",
          "description" => "Language of the content",
          "html" => $admin->get_view("ContentManagement/label_document.php", $data)
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

//put your code here
}
