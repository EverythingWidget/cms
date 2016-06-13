<div id="properties-form" class="col-lg-8 col-md-8 col-sm-12 col-xs-12" >
  <div class="block-row mt">
    <input type="hidden" id="id" name="id" value="">
    <input type="hidden" id="type" name="type" value="<?php echo $form_config["contentType"] ?>">
    <system-field class="field">
      <label>tr{Title}</label>
      <input class="text-field" value="" id="title" name="title" data-validate="r" required/>  
    </system-field>
  </div>

  <div class="block-row">
    <a id="slug" class="btn btn-xs btn-info mb" href="" target="_blank"></a>
  </div>

  <div class="row">
    <div class="col-xs-12 col-md-6 col-lg-6">
      <textarea class="text-field" id="keywords" data-label="tr{Keywords}" name="keywords"></textarea>
    </div>

    <div class="col-xs-12 col-md-6 col-lg-6">
      <textarea class="text-field" id="description" data-label="tr{Description}" name="description"></textarea>
    </div>
  </div>

  <?php
  // App custom inputs
  $input_groups = EWCore::read_registry("ew/ui/forms/content/properties");
  foreach ($input_groups as $id => $inputs) {
    echo "<div class=row><div class='col-xs-12'><h3>{$inputs["title"]}</h3></div></div>";
    echo $inputs["content"];
  }
  ?>
</div>

<div id="content-labels" class="col-lg-4 col-md-4 col-sm-12 col-xs-12" >
  <?php
  // Load content labels
  $content_components = EWCore::read_registry(EWCore::$EW_CONTENT_COMPONENT);

  foreach ($content_components as $comp_id => $label_object) {
    $data_array = json_decode($form_config["data"], true);
    $labels = $data_array["labels"];

    if (isset($labels)) {
      foreach ($labels as $label) {
        if ($label["key"] == $comp_id) {
          $value = $label["value"];
          break;
        }
      }
    }

    $form = EWCore::call($label_object['form'], compact("comp_id", "value", "form_id"));
    ?>
    <div class="block-row">
      <div class='box box-grey content-label disabled' data-activated="false">
        <div class='block-row'>
          <h3 class="pull-left"><?= $label_object["title"] ?></h3>

          <system-button-switch id="<?= $comp_id ?>_control_button"
                                class="label-control-button btn btn-default btn-sm pull-right" >
            Turned Off
          </system-button-switch>
        </div>

        <div class='row'>
          <?= EWCore::populate_view($form, compact("comp_id", "value", "form_id")) ?>
        </div>

      </div>
    </div>
    <?php
  }
  ?>
</div>
