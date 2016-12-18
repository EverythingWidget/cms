<?php

function get_folder_data($id) {
  $folder_info = [];
  $folder_info["parent_id"] = $_REQUEST["parent"];
  if ($id) {
    $folder_info = EWCore::call_api("admin/api/content-management/contents", ["id" => $id])['data'];
  }

  return json_encode($folder_info);
}

function inputs() {
  ob_start();
  ?>
  <input type="hidden" id="parent_id" name="parent_id" value="">
  <?php

  return ob_get_clean();
}

EWCore::register_form("ew/ui/forms/content/properties", "category-properties", ["content" => inputs()]);
echo admin\ContentManagement::create_content_form(
        [
            "formTitle"      => "Folder",
            "formId"         => "category-form",
            "contentType"    => "folder",
            "include_script" => \ew\ResourceUtility::get_view(__DIR__ . '/component.js', [], true),
            "data"           => get_folder_data($_REQUEST["folderId"])
]);
