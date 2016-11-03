<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of template
 *
 * @author Eeliya
 */
class template extends TemplateControl {

  public function get_template_body($html_body, $template_settings) {
    return $html_body;
  }

  public function get_template_script($template_settings) {
    \webroot\WidgetsManagement::add_html_script([
        'src' => 'public/rm/templates/mybit/aos/aos.js'
    ]);
    
    \webroot\WidgetsManagement::add_html_link('public/rm/templates/mybit/aos/aos.css');
    
    ob_start();
    include 'template.js';
    return ob_get_clean();
  }

  public function get_template_settings_form() {
    return '';
  }

  //put your code here
}
