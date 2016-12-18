<?php

$content_data = $form_config["data"];

// Set form id to 'content-form' if it is not specified
$form_id = ($form_config["formId"]) ? $form_config["formId"] : "content-form";

// Set content type to the default content type if it is not specified. Default content type is article
if (!$form_config["contentType"]) {
  $form_config["contentType"] = "article";
}

// Set default form title to 'Article'
if (!$form_config["formTitle"]) {
  $form_config["formTitle"] = "Article";
}

$tabs = EWCore::read_registry('ew/ui/forms/content/tabs');

include 'content-base.php';
