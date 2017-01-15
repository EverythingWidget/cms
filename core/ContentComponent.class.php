<?php

/**
 *
 * @author Eeliya
 */
interface ContentComponent {

  function on_insert($content_data, $label_data);

  function on_update($content_id, $content_data, $label_data);

  function on_soft_delete($content_id, $content_data, $label_data);

  function on_hard_delete($content_id, $content_data, $label_data);

  function get_explorer_nav($key, $value);

  function get_form($key, $data);

  function get();
}
