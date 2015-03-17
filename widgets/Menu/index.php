<?php
session_start();
//global $EW;
//echo $widget_parameters;
$titles = $widget_parameters["title"];
$links = $widget_parameters["feeder"];
$icnons = $widget_parameters["title"];
//$result = mysql_query("SELECT * FROM menus , sub_menus WHERE menus.id = sub_menus.menu_id AND menus.id = '$menuId' ORDER BY sub_menus.order") or die(mysql_error());
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
            $linkURL = EW_DIR_URL . $link["url"];
         }
         else if ($link["type"] == "widget-feeder")
         {
            $linkURL = '#';
            $sub_menus = EWCore::get_widget_feeder("menu", $link["feederName"]);
            $sub_menus = json_decode($sub_menus, TRUE);
         }
         else if ($link["type"])
         {
            $linkURL = EW_DIR_URL . $link["type"] . '/' . $link["id"];
         }
         else
         {
            $linkURL = EW_DIR_URL;
         }

         $active = ($linkURL == $_SERVER["REQUEST_URI"]) ? "active" : "";
         // Menu

         echo "<li class='$active'><a href='{$linkURL}'>" . $titles[$i] . "</a>";
         // Sub menu if exist
         if ($sub_menus)
         {
            echo "<ul>";
            foreach ($sub_menus as $sub_menu)
            {
               echo "<li class='$active'><a href='" . EW_DIR_URL . "{$sub_menu["link"]}'>{$sub_menu["title"]}</a></li>";
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
         $sub_menus = EWCore::get_widget_feeder("menu", $link["feederName"]);
         $sub_menus = json_decode($sub_menus, TRUE);
      }
      else if ($link["type"])
      {
         $linkURL = EW_DIR_URL . $link["type"] . '/' . $link["id"];
      }
      else
      {
         $linkURL = EW_DIR_URL;
      }
      ?>
      <li>
         <?php
         echo "<a href='" . EW_DIR_URL . "{$linkURL}'>";
         echo $titles . "</a>";


         if ($sub_menus)
         {
            echo "<ul>";
            foreach ($sub_menus as $sub_menu)
            {
               echo "<li><a href='" . EW_DIR_URL . "{$sub_menu["link"]}'>";
               echo $sub_menu["title"] . "</a></li>";
            }
            echo "</ul>";
         }
         ?>
      </li>
      <?php
   }
   ?>
</ul>
