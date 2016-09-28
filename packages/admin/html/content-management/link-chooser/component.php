<?php
$data = $_REQUEST["data"];

$tabs = EWCore::read_registry("ew/ui/components/link-chooser");
?>

<div class="header-pane tabs-bar thin">
  <h1 id="form-title">
    tr{Link Chooser}
  </h1>  

  <ul class="nav nav-pills xs-nav-tabs">    
    <?php
    foreach ($tabs as $id => $tab) {
      if ($id == "content-chooser") {
        echo "<li class='active '><a href='#{$id}' data-toggle='tab'>{$tab["title"]}</a></li>";
      }
      else {
        echo "<li class=''><a href='#{$id}' data-toggle='tab'>{$tab["title"]}</a></li>";
      }
    }
    ?>
  </ul>
</div>
<form id="link-chooser" class="form-content tabs-bar no-footer"  action="#" method="POST">
  <div class="tab-content">
    <?php
    foreach ($tabs as $id => $tab) {
      if ($id == "content-chooser") {
        echo "<div class='tab-pane active' id='{$id}'>{$tab["content"]}</div>";
      }
      else {
        echo "<div class='tab-pane' id='{$id}'>{$tab["content"]}</div>";
      }
    }
    ?>
  </div>
</form>

<script>
<?php
if ($data) {
  echo "EW.setFormData('#link-chooser',$data)";
}
?>
</script>
<!--<div class="footer-pane row actions-bar action-bar-items">
</div>-->