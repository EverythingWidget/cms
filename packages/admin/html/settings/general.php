<div>
  <?php
  $ui_settings_general = EWCore::read_registry("ew/ui/settings/general");

  foreach ($ui_settings_general as $settings_card) {
    if ($settings_card["url"]){
      $panel = EWCore::call_api($settings_card["url"], []);
      if(is_array($panel)){
        //echo $panel["message"];
      } else {
        echo $panel;
      }
    }
  }
  ?>
</div>
<script  type="text/javascript">

  (function () {
    System.module("settings/general", function () {
      //alert();
    });
  })();
</script>
