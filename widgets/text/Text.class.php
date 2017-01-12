<?php

namespace webroot;

/**
 * Description of Content
 *
 * @author Eeliya
 */
class Text implements Widget {

  public function get_configuration_form($widget_parameters = []) {
    ob_start();
    include 'form-config.php';
    return ob_get_clean();
  }

  public function get_description() {
    return 'Add a text block';
  }

  public function get_title() {
    return 'Text';
  }

  public function render($widget_parameters, $widget_id, $style_id, $style_class) {
    $format = $widget_parameters['format'];
    if ($widget_parameters['feeder']) {
      $feeder = json_decode($widget_parameters['feeder'], TRUE);
      $content_id = $feeder['id'];
      $field_id = $feeder['fieldId'];
    }
    else {
      return "<$format>&nbsp;</$format>";
    }

    $content = \EWCore::call_cached_api('admin/api/content-management/ew-list-feeder-related-contents', [
                'content_id' => $content_id,
                'key'        => "admin_ContentManagement_language",
                'value'      => URL_LANGUAGE
            ])['data'];

    if (isset($content)) {
      $contentFields = $content[0]['content_fields'];

      if ($format === 'default') {
        return $contentFields[$field_id]['content'];
      }
      else {
        return "<$format> {$contentFields[$field_id]['content']} </$format>";
      }
    }
    else {
      return "<span style='color:red'>Text not found</span>";
    }
  }

  public function get_feeder_type() {
    return null;
  }

//put your code here
}
