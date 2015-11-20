<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Eeliya
 */
interface ContentComponent
{

   //put your code here
   function on_insert($content_data, $label_data);

   function on_update($content_id, $content_data, $label_data);

   function on_soft_delete($content_id, $content_data, $label_data);

   function on_hard_delete($content_id, $content_data, $label_data);

   function get_explorer_nav($key, $value);

   function get_form($key, $data);
   
   function get();
}
