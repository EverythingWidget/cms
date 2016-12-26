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
      <input class="text-field" value="" name="style_id" id="style_id" >
    </system-field>

    <system-field class="field col-xs-12">
      <label>tr{Class}</label>
      <input id="style_class" name="style_class" class="text-field" v-on:keyup.space="updateStyleClasses()" v-on:blur="updateStyleClasses()" v-model="styleClassesText">
      <label class="block-row small" id="used-classes">
        <span class='tag label'
              v-for="class in containerClasses">
          {{ class }}
        </span>
      </label>
    </system-field>    
  </div>

  <div class="block-row">
    <div class="col-xs-12"  >
      <h3 class="line-header">Classes</h3>
      <div class="block-row options-panel" id="available-classes">
        <label class="btn btn-default" 
               v-bind:class=" { 'active' : isSelected(class) } "
               v-for="class in availableClasses" 
               v-on:click="toggleClass(class)">                
          {{ class }}
        </label>  
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