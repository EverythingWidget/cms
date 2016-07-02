<?php

session_start();

$id = $_REQUEST["article"];
$articleInfo = [];
$articleInfo["parent_id"] = $_REQUEST["parent"];
if ($id) {
  $articleInfo = EWCore::call_api("admin/api/content-management/contents", [
              "id" => $id
          ])['data'];
}

function inputs() {
  ob_start();
  ?>
  <input type="hidden" id="parent_id" name="parent_id" value="">
  <?php

  return ob_get_clean();
}

EWCore::register_form("ew/ui/forms/content/properties", "article-properties", ["content" => inputs()]);

echo admin\ContentManagement::create_content_form([
    'formId'         => 'article-form',
    "contentType"    => "article",
    "include_script" => \ew\ResourceUtility::get_view(__DIR__ . '/component.js', [], true),
    "data"           => json_encode($articleInfo)
]);

