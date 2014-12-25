<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Template
 *
 * @author Eeliya
 */
class TemplateControl
{
   //put your code here
   public function get_template_cp()
   {
      return "<h3>tr{Nothing to configure}</h3>";
   }
   
   
   public function get_html_body($html_body)
   {
      return $html_body;
   }
   
   
   protected function create_widget()
   {
      
   }
}
