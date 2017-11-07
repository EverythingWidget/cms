<?phpif (!$feeder_id) {  return '<!-- widget id:$widget_id, no feeder is specified -->';}// Load the page specified by URL$feeder_obj = webroot\WidgetsManagement::get_widget_feeder($feeder_id);if (!$feeder_obj->is_type('page')) {  return '<!-- widget id:$widget_id, feeder type is NOT `page` -->';}$page = EWCore::call_cached_api($feeder_obj->api_url, [    'id' => $feeder['id'],    '_language' => URL_LANGUAGE]);$page_data = $page['data'];$parent_data = [];$parent_title = '';if (isset($page['parent'])) {  $parent_data = $page['parent'];  $parent_title = ' - ' . $parent_data['title'];}if ($page['type'] === 'object') {  $page_title = $page_data['title'];  if (boolval($widget_parameters['include-meta']) === true) {    \webroot\WidgetsManagement::set_html_title($page_title . $parent_title);    \webroot\WidgetsManagement::set_html_keywords($page_data['keywords']);    \webroot\WidgetsManagement::set_html_description($page_data['description']);  }  $page_content = $page_data['content'];  $page_content_parts = webroot\WidgetsManagement::parse_html_to_parts($page_content);  $fields = $widget_parameters['content_fields'];  if (is_string($fields)) {    $fields = [$fields];  }  if ($widget_parameters['content_mason']) {    $mason = \webroot\WidgetsManagement::get_mason($widget_parameters['content_mason']);    if (!is_null($mason)) {      $page_content = $mason->get_html($page_data['content_fields']);    } else {      echo "<p style='color: red; font-wieght: bold;'> Mason not found: {$widget_parameters['content_mason']} </p>";      return;    }  } else if ($widget_parameters['content_fields']) {    $page_content = '';    foreach ($fields as $field) {      $tag = $page_data['content_fields'][$field]['tag'];      if (isset($tag)) {        $field_data = $page_data['content_fields'][$field];        if ($tag === 'img') {          $page_content .= "<img class='$field {$field_data['class']}' src='{$field_data['src']}' />";        } else {          $href = isset($field_data['link']) ? "href='{$field_data['link']}'" : "";          $page_content .= "<$tag class='$field {$field_data['class']}' $href>{$field_data['content']}</$tag>";        }      }    }    $exclude = false;    foreach ($fields as $field) {      if (strpos($field, '!') !== false) {        $field = substr($field, 1);        $exclude = true;        $page_content_parts = array_filter($page_content_parts, function ($item) use ($field) {          return $item['content-field'] !== $field;        });      }    }    if ($exclude) {      $page_content = '';      foreach ($page_content_parts as $part) {        $page_content .= $part['html'];      }    }  }} else {  echo $page_data['html'];  return;}echo $page_content;if ($widget_parameters['linkAddress']) {  ?>  <a class="see-more" href="<?= $widget_parameters['linkAddress'] ?>">    <?= $widget_parameters['linkName'] ?>  </a>  <?php}