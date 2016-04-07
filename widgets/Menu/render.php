<?php
$language = $_REQUEST["_language"];
if ($_REQUEST["_url_language"] || $_REQUEST["_language"] != 'en') {
  $language = $_REQUEST["_language"];
  $url_language = $language . "/";
}


$titles = $widget_parameters["title"];
$links = $widget_parameters["link"];
$icons = $widget_parameters["icon"];

$feeder = json_decode($widget_parameters["feeder"], true);
if ($feeder) {
  $feeder_obj = webroot\WidgetsManagement::get_widget_feeder($feeder["feederId"]);


  $page = EWCore::call_api($feeder_obj->api_url, [
              "id"       => $feeder["id"],
              "language" => $language
  ]);

  $content_fields = $page["data"]["content_fields"];

  if ($content_fields) {
    $menu_item = $content_fields["@menu-item"];

    if ($content_fields["@widget/menu/item"]) {
      $menu_item = $content_fields["@widget/menu/item"];
    }

    $titles = $menu_item["content"];
    $links = $menu_item["link"];
    $icons = $menu_item["src"];
  }
}
$result_html = "";
?>
<ul>
  <?php
  if (gettype($titles) == "array") {
    for ($i = 0, $len = count($titles); $i < $len; $i++) {
      $sub_menus = null;
      $link = json_decode($links[$i], true);
      $link_url = ".";
      if (json_last_error() !== JSON_ERROR_NONE) {
        $link = [];
        $link_url = EW_DIR_URL . $links[$i];
      }

      if ($link["type"] == "admin/content-management/link") {
        //echo EWCore::$languages['en'];
        if (!EWCore::$languages[str_replace('/', '', $link["url"])]) {
          $link_url = EW_DIR_URL . $url_language . $link["url"];
        }
        else {
          $link_url = EW_DIR_URL . $link["url"];
        }
      }
      else if ($link["type"] == "widget-feeder") {
        $link_url = '#';
        $sub_menus_json = webroot\WidgetsManagement::get_widget_feeder("menu", $link["feederName"]);
        $sub_menus = json_decode($sub_menus_json, TRUE);
      }
      else if ($link["type"]) {
        $link_url = EW_DIR_URL . $url_language . $link["type"] . '/' . $link["id"];
      }
      /* else
        {
        $linkURL = EW_DIR_URL . $url_language;
        } */
      $link_requlare_expression_ready = preg_quote($link_url, '/');
      $pattern = "/$link_requlare_expression_ready(.*)/";

      preg_match($pattern, $_SERVER['REQUEST_URI'] . '.', $match);
      $active = ($match) ? "active" : "";

      // Menu
      $result_html .= "<li class='$active'><a href='{$link_url}'>$titles[$i]</a>";

      // Sub menu if exist
      if ($sub_menus) {
        $result_html .= "<ul>";
        foreach ($sub_menus as $sub_menu) {
          $result_html .= "<li class='$active'><a href='" . EW_DIR_URL . $url_language . "{$sub_menu["link"]}'>{$sub_menu["title"]}</a></li>";
        }
        $result_html .= "</ul>";
      }
      $result_html .= "</li>";
    }
  }
  else {
    $link = json_decode($links, true);
    if ($link["type"] == "admin/content-management/link") {
      $link_url = EW_DIR_URL . $link["url"];
    }
    else if ($link["type"] == "widget-feeder") {
      $link_url = '#';
      $sub_menus_json = webroot\WidgetsManagement::get_widget_feeder("menu", $link["feederName"]);
      $sub_menus = json_decode($sub_menus_json, TRUE);
    }
    else if ($link["type"]) {
      $link_url = EW_DIR_URL . $url_language . $link["type"] . '/' . $link["id"];
    }
    else {
      $link_url = EW_DIR_URL . $url_language;
    }

    $link_requlare_expression_ready = preg_quote($link_url, '/');
    $pattern = "/$link_requlare_expression_ready(.*)/";
    preg_match($pattern, $_SERVER['REQUEST_URI'] . '.', $match);
    $active = ($match) ? "active" : "";

    echo "<li class='$active' ><a href='$link_url'>$titles</a>";
    if ($sub_menus) {
      echo "<ul>";
      foreach ($sub_menus as $sub_menu) {
        echo "<li class='$active'><a href='" . EW_DIR_URL . $url_language . "{$sub_menu["link"]}'>";
        echo $sub_menu["title"] . "</a></li>";
      }
      echo "</ul>";
    }
    echo '</li>';
  }

  echo $result_html;
  ?>
</ul>
