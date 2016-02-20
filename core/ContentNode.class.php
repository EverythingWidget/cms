<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ew;

/**
 * Description of UINode
 *
 * @author Eeliya
 */
interface UINode {

  public function on_insert($content_data, $label_data);

  public function on_update($content_id, $content_data, $label_data);

  public function on_soft_delete($content_id, $content_data, $label_data);

  public function on_hard_delete($content_id, $content_data, $label_data);

  public function get_html($template);
}
