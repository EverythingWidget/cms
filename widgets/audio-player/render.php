<?php
// if default content set to true, then read the page feeder name from the URL
$priority_with_url = $widget_parameters["priority-with-url"];
if ($priority_with_url == "yes" && $_REQUEST["_file"] && $_REQUEST["_module_name"]) {
  $is_feeder_app = webroot\WidgetsManagement::get_widget_feeder_by_url($_REQUEST["_module_name"]);

  if ($is_feeder_app) {
    $feeder_id = $is_feeder_app->api_url;
    $feeder["id"] = $_REQUEST["_method_name"];
  }
}

if ($feeder_id) {
  $feeder_obj = webroot\WidgetsManagement::get_widget_feeder($feeder_id);

  $page = EWCore::call_cached_api($feeder_obj->api_url, [
              'id'       => $feeder["id"],
              '_language' => $language
  ]);

  $page_data = $page["data"];
}

//print_r($is_feeder_app);
if ($widget_parameters["content_field"]) {
  $field = $widget_parameters["content_field"];

  $page_data['html'] = '';

  $tag = $page_data['content_fields'][$field]['content'];
  if (isset($tag)) {
    $source = './~rm/public/media/' . $tag;
  }
}
?>

<audio>
  <source src="<?= $source ?>" type="audio/mp3">
  Your browser does not support the audio element.
</audio>
<script>
  window.addEventListener('load', function () {
    audiojs.events.ready(function () {
      var as = audiojs.createAll();
    });
  });
</script>