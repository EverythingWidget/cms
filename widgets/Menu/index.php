<?php
$language = '';
if ($_REQUEST["_url_language"] || $_REQUEST["_language"] != 'en')
   $language = $_REQUEST["_language"] . '/';
//echo $language;
$titles = $widget_parameters["title"];
$links = $widget_parameters["link"];
$icnons = $widget_parameters["title"];
?>
<ul>
   <?php
   if (gettype($titles) == "array")
   {
      for ($i = 0; $i < count($titles); $i++)
      {
         $sub_menus = null;
         $link = json_decode($links[$i], true);

         if ($link["type"] == "link")
         {
            //echo EWCore::$languages['en'];
            if (!EWCore::$languages[str_replace('/', '', $link["url"])])
               $linkURL = EW_DIR_URL . $language . $link["url"];
            else
               $linkURL = EW_DIR_URL . $link["url"];
         }
         else if ($link["type"] == "widget-feeder")
         {
            $linkURL = '#';
            $sub_menus = webroot\WidgetsManagement::get_widget_feeder("menu", $link["feederName"]);
            $sub_menus = json_decode($sub_menus, TRUE);
         }
         else if ($link["type"])
         {
            $linkURL = EW_DIR_URL . $language . $link["type"] . '/' . $link["id"];
         }
         else
         {
            $linkURL = EW_DIR_URL . $language;
         }
         $link_requlare_expression_ready = preg_quote($linkURL, '/');
         $pattern = "/$link_requlare_expression_ready(.*)/";
         //echo $link_requlare_expression_ready;
         preg_match($pattern, $_SERVER['REQUEST_URI'] . '.', $match);
         $active = ($match) ? "active" : "";

         // Menu
         echo "<li class='$active'><a href='{$linkURL}'>" . $titles[$i] . "</a>";

         // Sub menu if exist
         if ($sub_menus)
         {
            echo "<ul>";
            foreach ($sub_menus as $sub_menu)
            {
               echo "<li class='$active'><a href='" . EW_DIR_URL . $language . "{$sub_menu["link"]}'>{$sub_menu["title"]}</a></li>";
            }
            echo "</ul>";
         }
         echo "</li>";
      }
   }
   else
   {
      $link = json_decode($links, true);
      if ($link["type"] == "link")
      {
         $linkURL = EW_DIR_URL . $link["url"];
      }
      else if ($link["type"] == "widget-feeder")
      {
         $linkURL = '#';
         $sub_menus = webroot\WidgetsManagement::get_widget_feeder("menu", $link["feederName"]);
         $sub_menus = json_decode($sub_menus, TRUE);
      }
      else if ($link["type"])
      {
         $linkURL = EW_DIR_URL . $language . $link["type"] . '/' . $link["id"];
      }
      else
      {
         $linkURL = EW_DIR_URL . $language;
      }

      $link_requlare_expression_ready = preg_quote($linkURL, '/');
      $pattern = "/$link_requlare_expression_ready(.*)/";
      preg_match($pattern, $_SERVER['REQUEST_URI'] . '.', $match);
      $active = ($match) ? "active" : "";

      echo "<li class='$active' ><a href='$linkURL'>$titles</a>";
      if ($sub_menus)
      {
         echo "<ul>";
         foreach ($sub_menus as $sub_menu)
         {
            echo "<li class='$active'><a href='" . EW_DIR_URL . $language . "{$sub_menu["link"]}'>";
            echo $sub_menu["title"] . "</a></li>";
         }
         echo "</ul>";
      }
      echo '</li>';
   }
   ?>
</ul>
