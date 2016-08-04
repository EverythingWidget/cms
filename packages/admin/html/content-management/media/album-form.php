<?php

//session_start();
$albumInfo = EWCore::call_api("admin/api/content-management/albums", [
            'albumId' => $_REQUEST["albumId"]
        ])['data'];

function inputs() {
  ob_start();
  ?>

  <?php

  return ob_get_clean();
}

function scripts() {
  ob_start();
  include 'album-form.js';
  return ob_get_clean();
}

//EWCore::register_form("ew-content-form-proerties", "article-properties", ["content" => inputs()]);
echo admin\ContentManagement::create_content_form([
    "formId"         => "album-form",
    'formTitle'      => 'Album',
    "contentType"    => "album",
    "include_script" => scripts(),
    "data"           => json_encode($albumInfo)
]);
