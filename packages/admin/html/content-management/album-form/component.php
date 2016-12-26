<?php

//session_start();
$albumInfo = EWCore::call_api('admin/api/content-management/albums', [
            'albumId' => $_REQUEST["albumId"]
        ])['data'];

function scripts() {
  ob_start();
  include 'album-form.js';
  return ob_get_clean();
}

echo admin\ContentManagement::create_content_form([
    "formId"         => "album-form",
    'form_title'      => 'Album',
    "content_type"    => "album",
    "include_script" => \ew\ResourceUtility::get_view(__DIR__ . '/component.js', [], true),
    "data"           => json_encode($albumInfo)
]);
