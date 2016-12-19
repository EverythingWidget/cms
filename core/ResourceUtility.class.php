<?php

namespace ew;

class ResourceUtility {

  public static function get_view($path, $view_data = [], $absolute_path = false) {
    $full_path = $absolute_path ? $path : EW_PACKAGES_DIR . '/' . $path;

    if (!file_exists($full_path)) {
      return \EWCore::log_error(404, "View: File not found: `$full_path`");
    }

    ob_start();
    include $full_path;
    $html = ob_get_clean();

    return static::populate_view($html, $view_data);
  }

  public static function populate_view($view_html, $view_data) {
    $text = preg_replace_callback("/\{\{([\w]*)\}\}/", function($match) use ($view_data) {
      $data = $view_data[$match[1]];
      return isset($data) ? $data : $match[0];
    }, $view_html);

    return preg_replace_callback('/\$php\.([\w]*)/', function($match) use ($view_data) {
      $data = $view_data[$match[1]];
      return isset($data) ? $data : null;
    }, $text);
  }

  public static function load_js_as_tag($path, $data = [], $absolute_path = false) {
    return '<script>' . static::get_view($path, $data, $absolute_path) . '</script>';
  }

  public static function css_to_array($css) {
    
//    preg_match_all('/(?ims)([a-z0-9\s\.\:#_\-@,\[\]\*]+)\{([^\}]*)\}/i', $css, $arr);
//    $css_no_comments = preg_replace('/(/\*(?:[^*]|\*[^/])*\*/)/', '', $css);
//    preg_match_all('/(?<selector>(?:(?:[^,{]+),?)*?)\{(?:(?<name>[^}:]+):?(?<value>[^};]+);?)*?\}/i', $css, $arr);
//    $result = array();
//    foreach ($arr[0] as $i => $x) {
//      $selector = trim($arr[1][$i]);
//      $rules = explode(';', trim($arr[2][$i]));
//      $rules_arr = array();
//      foreach ($rules as $strRule) {
//        if (!empty($strRule)) {
//          $rule = explode(":", $strRule);
//          $rules_arr[trim($rule[0])] = trim($rule[1]);
//        }
//      }
//
//      $selectors = explode(',', trim($selector));
//      foreach ($selectors as $strSel) {
//        $result[$strSel] = $rules_arr;
//      }
//    }
//    return $result;

    $regex = "/([#\.][a-z0-9]*?\.?.*?)\s?\{([^\{\}]*)\}/m";
    preg_match_all($regex, $css, $classes, PREG_PATTERN_ORDER);

    if(sizeof($classes[1]) > 0){
        //organize a new proper array "$parsedCss"
        foreach($classes[1] as $index => $value){
            $parsedCss[$index][0] = $value; //class or id name
        }

        foreach($classes[2] as $index => $value){  
            //Parsing the attributes string
            $regex = "/([^\;]*);/m";
            preg_match_all($regex, $value, $returned_attributes, PREG_PATTERN_ORDER);

            if(sizeof($returned_attributes[1]) > 0){
                $parsedCss[$index][1] = $returned_attributes[1]; // array of attributes
            }
        }
    }
    return $parsedCss;
  }

  public static $LAYOUT_KEYS = [
      'display',
      'margin',
      'padding',
      'font-size'
  ];

  public static function parse_css_to_layout_and_theme($css_classes_list) {
    $layout = [];
    $theme = [];

    foreach ($css_classes_list as $class_name => $class_attributes) {
      $layout_values = [];
      $theme_values = [];

      foreach ($class_attributes as $key => $value) {
        if (in_array($key, static::$LAYOUT_KEYS)) {
          $layout_values[$key] = $value;
        }
        else {
          $theme_values[$key] = $value;
        }
      }

      if (count($layout_values) > 0) {
        $layout[$class_name] = $layout_values;
      }

      if (count($theme_values) > 0) {
        $theme[$class_name] = $theme_values;
      }
    }

    return [
        'layout' => $layout,
        'theme'  => $theme
    ];
  }

}
