<?php

session_start();

function get_article_data($id) {
  $articleInfo = [];
  $articleInfo["parent_id"] = $_REQUEST["parent"];
  if ($_REQUEST["articleId"]) {
    $articleInfo = EWCore::call_api("admin/api/content-management/get-article", [
                "articleId" => $id
            ])["data"];
  }

  return json_encode($articleInfo);
}

function inputs() {
  ob_start()
  ?>
  <input type="hidden" id="parent_id" name="parent_id" value="">
  <?php

  return ob_get_clean();
}

function script() {
  ob_start();
  include "article-form.js";
  return ob_get_clean();
}

EWCore::register_form("ew/ui/forms/content/properties", "article-properties", ["content" => inputs()]);
echo admin\ContentManagement::create_content_form(["formId"         => "article-form",
    "contentType"    => "article",
    "include_script" => script(),
    "data"           => get_article_data($_REQUEST["articleId"])]);

