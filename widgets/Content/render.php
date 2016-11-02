<?phpif (!$feeder_id) {  return '<!-- widget id:$widget_id, no feeder is specified -->';}// Load the page specified by URL$feeder_obj = webroot\WidgetsManagement::get_widget_feeder($feeder_id);if (!$feeder_obj->is_type('page')) {  return '<!-- widget id:$widget_id, feeder type is NOT `page` -->';}$page = EWCore::call_cached_api($feeder_obj->api_url, [            "id"       => $feeder["id"],            "language" => $_REQUEST['_language']        ]);$page_data = $page['data'];if ($page['type'] === 'object') {  $page_title = $page_data['title'];  if (boolval($widget_parameters['include-meta']) === true) {    \webroot\WidgetsManagement::set_html_title($page_title);    \webroot\WidgetsManagement::set_html_keywords($page_data['keywords']);    \webroot\WidgetsManagement::set_html_description($page_data['description']);  }  $page_content = $page_data['content'];  $page_content_parts = webroot\WidgetsManagement::parse_html_to_parts($page_content);  $fields = $widget_parameters['content_fields'];  if (is_string($fields)) {    $fields = [$fields];  }  if ($widget_parameters['item_mason']) {    $path = EW_TEMPLATES_DIR . '/' . $widget_parameters['item_mason'];    if (file_exists($path)) {      $mason = include $path;    }    else {      echo "<p style='color: red; font-wieght: bold;'> Mason not found: {$widget_parameters['item_mason']} </p>";      return;    }    $page_content = $mason->get_html($fields);  }  else if ($widget_parameters['content_fields']) {    $page_content = '';    foreach ($fields as $field) {      $tag = $page_data['content_fields'][$field]['tag'];      if (isset($tag)) {        $field_data = $page_data['content_fields'][$field];        if ($tag === 'img') {          $page_content .= "<img class='$field {$field_data['class']}' src='{$field_data['src']}' />";        }        else {          $href = isset($field_data['link']) ? "href='{$field_data['link']}'" : "";          $page_content .= "<$tag class='$field {$field_data['class']}' $href>{$field_data['content']}</$tag>";        }      }    }    $exclude = false;    foreach ($fields as $field) {      if (strpos($field, '!') !== false) {        $field = substr($field, 1);        $exclude = true;        $page_content_parts = array_filter($page_content_parts, function($item) use ($field) {          return $item['content-field'] !== $field;        });      }    }    if ($exclude) {      $page_content = '';      foreach ($page_content_parts as $part) {        $page_content .= $part['html'];      }    }  }}else {  echo $page_data['html'];  return;}echo $page_content;if ($widget_parameters['linkAddress']) {  ?>  <a class="SeeMore EnterIcon" href="<?= $widget_parameters['linkAddress'] ?>">    <?= $widget_parameters['linkName'] ?>  </a>  <?php}