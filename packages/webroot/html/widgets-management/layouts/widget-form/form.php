<?php
session_start();

global $widget_info;
$widget_type = $_REQUEST["widgetType"];

function get_properties_form() {
  global $widget_info;
  ob_start();
  ?>

  <div class="block-row">
    <system-field class="field col-xs-12">
      <label>tr{ID}</label>
      <input class="text-field" value="<?php echo $widget_info["style_id"] ?>" name="style_id" id="style_id" >
    </system-field>

    <system-field class="field col-xs-12">
      <label>tr{Class}</label>
      <input id="style_class" name="style_class" class="text-field" >
      <label class="block-row small" id="used-classes"></label>
    </system-field>    
  </div>

  <div class="col-xs-12">
    <h3 class="line-header">Applied classes</h3>
    <div class=" block-row options-panel" id="widget-classes" data-toggle="buttons">
    </div>
  </div>


  <div class="block-row">
    <div class="col-xs-12"  >
      <h3 class="line-header">Available classes</h3>
      <div class="block-row options-panel" id="available-classes" data-toggle="buttons">
        <?php
        if ($_REQUEST["template"]) {
          $templates = json_decode(EWCore::parse_css(EW_PACKAGES_DIR . '/rm/public/' . $_REQUEST["template"] . '/template.css', "widget"), true);

          foreach ($templates as $t) {
            echo "<label>$t</label>";
          }
        }
        ?>
      </div>
    </div>
  </div>

  <?php
  return ob_get_clean();
}

function get_size_layout_form() {
  ob_start();
  include 'size-layout.php';
  return ob_get_clean();
}

EWCore::register_form('ew/ui/widget-form', 'widget-cp', [
    "title"   => "Widget CP",
    "content" => webroot\WidgetsManagement::get_widget_cp($widget_type)
]);

EWCore::register_form('ew/ui/widget-form', 'size-layout', [
    "title"   => "Size & Layout",
    "content" => get_size_layout_form()
]);

EWCore::register_form('ew/ui/widget-form', 'properties', [
    "title"   => "Properties",
    "content" => get_properties_form()
]);

$tabs = EWCore::read_registry("ew/ui/widget-form");
?>
<div class="header-pane tabs-bar thin">
  <h1 id="uis-widget-title" class="col-xs-12">
    <span>tr{Widget}</span> <?php echo $widget_type; ?>
  </h1>
  <ul class="nav nav-pills xs-nav-tabs">
    <?php
    foreach ($tabs as $id => $tab) {
      if ($id == "widget-cp")
        echo "<li class='active'><a href='#{$id}' data-toggle='tab'>tr{" . $tab["title"] . "}</a></li>";
      else
        echo "<li ><a href='#{$id}' data-toggle='tab'>tr{" . $tab["title"] . "}</a></li>";
    }
    ?>
  </ul>
</div>

<div id="widget-control-panel" class="form-content" >
  <input type="hidden" name="cmd" id="cmd" >
  <div class="tab-content">
    <?php
    foreach ($tabs as $id => $tab) {
      if ($id == "widget-cp")
        echo "<div class='tab-pane active' id='{$id}'>{$tab["content"]}</div>";
      else
        echo "<div class='tab-pane' id='{$id}'>{$tab["content"]}</div>";
    }
    ?>
  </div>
</div>