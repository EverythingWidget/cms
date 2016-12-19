<?php

$content_data = $form_config["data"];

// Set form id to 'content-form' if it is not specified
$form_id = ($form_config["formId"]) ? $form_config["formId"] : "content-form";

// Set content type to the default content type if it is not specified. Default content type is article
if (!$form_config["content_type"]) {
  $form_config["content_type"] = "article";
}

// Set default form title to 'Article'
if (!$form_config["form_title"]) {
  $form_config["form_title"] = "Article";
}

$tabs = EWCore::read_registry('ew/ui/forms/content/tabs');

include 'content-base.php';
