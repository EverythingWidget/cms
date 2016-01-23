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


  $page = EWCore::call_api($feeder_obj->api_url, ["id"       => $feeder["id"],
              "language" => $language]);
  $content_fields = $page["data"]["content_fields"];
  if ($content_fields) {
    //print_r($content_fields["menu-title"]["content"]);
    $titles = $content_fields["menu-title"]["content"];
    $links = $content_fields["menu-link"]["content"];
    $icons = $content_fields["menu-icon"]["content"];
    //print_r($links);
  }
}
?>
<ul>
  <?php
  if (gettype($titles) == "array") {
    for ($i = 0, $len = count($titles); $i < $len; $i++) {
      $sub_menus = null;
      $link = json_decode($links[$i], true);

      if ($link["type"] == "admin/content-management/link") {
        //echo EWCore::$languages['en'];
        if (!EWCore::$languages[str_replace('/', '', $link["url"])])
          $linkURL = EW_DIR_URL . $url_language . $link["url"];
        else
          $linkURL = EW_DIR_URL . $link["url"];
      }
      else if ($link["type"] == "widget-feeder") {
        $linkURL = '#';
        $sub_menus = webroot\WidgetsManagement::get_widget_feeder("menu", $link["feederName"]);
        $sub_menus = json_decode($sub_menus, TRUE);
      }
      else if ($link["type"]) {
        $linkURL = EW_DIR_URL . $url_language . $link["type"] . '/' . $link["id"];
      }
      else {
        $linkURL = EW_DIR_URL . $url_language;
      }
      $link_requlare_expression_ready = preg_quote($linkURL, '/');
      $pattern = "/$link_requlare_expression_ready(.*)/";
      //echo $link_requlare_expression_ready;
      preg_match($pattern, $_SERVER['REQUEST_URI'] . '.', $match);
      $active = ($match) ? "active" : "";

      // Menu
      echo "<li class='$active'><a href='{$linkURL}'>" . $titles[$i] . "</a>";

      // Sub menu if exist
      if ($sub_menus) {
        echo "<ul>";
        foreach ($sub_menus as $sub_menu) {
          echo "<li class='$active'><a href='" . EW_DIR_URL . $url_language . "{$sub_menu["link"]}'>{$sub_menu["title"]}</a></li>";
        }
        echo "</ul>";
      }
      echo "</li>";
    }
  }
  else {
    $link = json_decode($links, true);
    if ($link["type"] == "admin/content-management/link") {
      $linkURL = EW_DIR_URL . $link["url"];
    }
    else if ($link["type"] == "widget-feeder") {
      $linkURL = '#';
      $sub_menus = webroot\WidgetsManagement::get_widget_feeder("menu", $link["feederName"]);
      $sub_menus = json_decode($sub_menus, TRUE);
    }
    else if ($link["type"]) {
      $linkURL = EW_DIR_URL . $url_language . $link["type"] . '/' . $link["id"];
    }
    else {
      $linkURL = EW_DIR_URL . $url_language;
    }

    $link_requlare_expression_ready = preg_quote($linkURL, '/');
    $pattern = "/$link_requlare_expression_ready(.*)/";
    preg_match($pattern, $_SERVER['REQUEST_URI'] . '.', $match);
    $active = ($match) ? "active" : "";

    echo "<li class='$active' ><a href='$linkURL'>$titles</a>";
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
  ?>
</ul>
