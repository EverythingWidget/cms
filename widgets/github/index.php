<?php

$language = $_REQUEST["_language"];
if ($_REQUEST["_url_language"] || $_REQUEST["_language"] != 'en') {
  $language = $_REQUEST["_language"];
}

\webroot\WidgetsManagement::add_html_script([
    "id" => "github-bjs",
    "src" => "https://buttons.github.io/buttons.js"
]);

if($widget_parameters[""])