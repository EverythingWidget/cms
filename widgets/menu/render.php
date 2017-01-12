<?php
$language = $_REQUEST['_language'];

if ($_REQUEST['_url_language']) {
  $language = $_REQUEST['_url_language'];
  $url_language = $language . '/';
}

//die($language);

$titles = $widget_parameters["title"];
$links = $widget_parameters["link"];
$icons = $widget_parameters["icon"];

$feeder = json_decode($widget_parameters["feeder"], true);

if ($feeder) {
  $feeder_obj = webroot\WidgetsManagement::get_widget_feeder($feeder["feederId"]);

  if (is_null($feeder_obj)) {
    echo 'feeder not found: ' . $feeder["feederId"];
    return;
  }

  $page = EWCore::call_api($feeder_obj->api_url, [
              'id'        => $feeder["id"],
              '_language' => $language
  ]);

  $content_fields = $page["data"]["content_fields"];
  $menu_item = [];

  if ($content_fields) {
    if ($content_fields["@menu-item"]) {
      $menu_item = $content_fields["@menu-item"];
    }
    if ($content_fields["@widget/menu/item"]) {
      $menu_item = $content_fields["@widget/menu/item"];
    }

    $titles = $menu_item["content"];
    $links = $menu_item["link"];
    $icons = $menu_item["src"];
    $class = $menu_item['class'];
  }
}

$result_html = "";

$base_url = rtrim(EW_DIR_URL, '/') . '/';
$base_url_with_language = $base_url . $url_language;
?>
<ul>
  <?php
  if (gettype($titles) == "array") {

    for ($i = 0, $len = count($titles); $i < $len; $i++) {
      $sub_menus = null;
      $link = json_decode($links[$i], true);
      $link_url = '';

      if (!isset($links[$i])) {
        $links[$i] = '';
      }

      if (json_last_error() !== JSON_ERROR_NONE) {
        $link = [];
        $link_url = $base_url_with_language . $links[$i];
      }
      else {
        $link_url = $base_url_with_language . $links[$i];
      }
      if ($link["type"] == "admin/content-management/link") {
        if (!EWCore::$languages[str_replace('/', '', $links[$i])]) {
          $link_url = $base_url_with_language . $links[$i];
        }
        else {
          $link_url = $base_url_with_language . $links[$i];
        }
      }
      else if ($link["type"] == "widget-feeder") {
        $link_url = '#';
        $sub_menus_json = webroot\WidgetsManagement::get_widget_feeder('menu', $link['feederName']);
        $sub_menus = json_decode($sub_menus_json, TRUE);
      }
      else if ($link["type"]) {
        $link_url = $base_url_with_language . $link['type'] . '/' . $link['id'];
      }

      /* else
        {
        $linkURL = EW_DIR_URL . $url_language;
        } */
//      $link_requlare_expression_ready = preg_quote($link_url, '/');
//      $pattern = "/$link_requlare_expression_ready/";
//      preg_match($pattern, $_SERVER['REQUEST_URI'] . '.', $match);
      $active = (rtrim($link_url, '/') === rtrim($_SERVER['REQUEST_URI'], '/')) ? "active" : "";

      // Menu

      $result_html .= "<li class='$active'><a class='{$class[$i]}' href='{$link_url}'>{$titles[$i]}</a>";

      // Sub menu if exist
      if ($sub_menus) {
        $result_html .= "<ul>";
        foreach ($sub_menus as $sub_menu) {
          $result_html .= "<li class='$active'><a class='{$class[$i]}' href='" . $base_url_with_language . "{$sub_menu["link"]}'>{$sub_menu["title"]}</a></li>";
        }
        $result_html .= "</ul>";
      }
      $result_html .= "</li>";
    }
  }
  else {
    $link = json_decode($links, true);
    if ($link['type'] == 'admin/content-management/link') {
      $link_url = $base_url . $link["url"];
    }
    else if ($link['type'] == 'widget-feeder') {
      $link_url = '#';
      $sub_menus_json = webroot\WidgetsManagement::get_widget_feeder('menu', $link['feederName']);
      $sub_menus = json_decode($sub_menus_json, TRUE);
    }
    else if ($link["type"]) {
      $link_url = $base_url_with_language . $link['type'] . '/' . $link['id'];
    }
    else {
      $link_url = $base_url_with_language;
    }

    // $link_requlare_expression_ready = preg_quote($link_url, '/');
    // $pattern = "/$link_requlare_expression_ready(.*)/";
    // preg_match($pattern, $_SERVER['REQUEST_URI'] . '.', $match);
    // $active = ($match) ? "active" : "";
    $active = (rtrim($link_url, '/') === rtrim($_SERVER['REQUEST_URI'], '/')) ? "active" : "";

    $result_html .= "<li class='$active' ><a href='$link_url'>$titles</a>";
    if ($sub_menus) {
      $result_html .= "<ul>";
      foreach ($sub_menus as $sub_menu) {
        $result_html .= "<li class='$active'><a href='" . $base_url_with_language . "{$sub_menu["link"]}'>";
        $result_html .= $sub_menu["title"] . "</a></li>";
      }
      $result_html .= "</ul>";
    }
    $result_html .= '</li>';
  }

  echo $result_html;
  ?>
</ul>
<script>
  document.querySelector('[data-widget-id=$php.widget_id]').addEventListener('click', function () {
    this.classList.toggle('active');
  });
</script>