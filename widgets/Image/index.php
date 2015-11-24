<?php

if ($widget_parameters["image"])
{
      $width = is_numeric($widget_parameters["width"]) ? "width='{$widget_parameters["width"]}'" : "";
      $height = is_numeric($widget_parameters["height"]) ? "height='{$widget_parameters["height"]}'" : "";
      echo "<img src='{$widget_parameters["image"]}' $width $height />";
   
}