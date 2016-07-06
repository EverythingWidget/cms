<?php
$app_main_form = EWCore::read_registry('ew-section-main-form');
if (isset($app_main_form) && $form_config) {
  $app_main_form = array_merge_recursive($app_main_form, $form_config);
}
?>

<div id="main-content" class="col-xs-12" role="main">
  <?php
  if ($app_main_form['content']) {
    echo $app_main_form['content']['content'];
  }
  ?>
</div>
<?php
echo $app_main_form["script"];
