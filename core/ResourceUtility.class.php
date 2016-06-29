<?php

namespace ew;

class ResourceUtility {

  public static function get_view($path, $view_data = []) {
    $full_path = EW_PACKAGES_DIR . '/' . $path;

    if (!file_exists($full_path)) {
      return \EWCore::log_error(404, "<h4>View: File not found</h4><p>File `$full_path`, not found</p>");
    }

    ob_start();
    include $full_path;
    $html = ob_get_clean();

    return static::populate_view($html, $view_data);
  }

  public static function populate_view($view_html, $view_data) {
    return preg_replace_callback("/\{\{([\w]*)\}\}/", function($match) use ($view_data) {
      $data = $view_data[$match[1]];
      return isset($data) ? $data : $match[0];
    }, $view_html);
  }

  public static function load_js_as_tag($path, $data = []) {
    return '<script>' . static::get_view($path, $data) . '</script>';
  }

}
