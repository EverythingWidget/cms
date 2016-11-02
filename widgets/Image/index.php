<?php

if ($widget_parameters["image"]) {
  $width = is_numeric($widget_parameters["width"]) ? "width='{$widget_parameters["width"]}'" : "";
  $height = is_numeric($widget_parameters["height"]) ? "height='{$widget_parameters["height"]}'" : "";

  if (boolval($widget_parameters['link-to-homepage'])) {
    echo "<a href='/'><img src='{$widget_parameters["image"]}' $width $height alt=''/></a>";
    return;
  }
  echo "<img src='{$widget_parameters["image"]}' $width $height alt=''/>";
}