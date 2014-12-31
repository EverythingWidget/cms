<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function custom_url_tab()
{
   ob_start();
   ?>  
   <div class="col-xs-12">
      <input class="text-field" data-label="URL link" name="url_link" id="url_link"/>
   </div>
   <div class="col-xs-12 mar-top">
      <button type="button" class="btn btn-primary" onclick="url_done();">Done</button>
   </div>

   <script>
      function url_done()
      {
   <?php
//Call the function which has been attached to the function reference element
   if ($_REQUEST["callback"] == "function-reference")
   {
      ?>
            var doc = {type: "link", "url": $("#url_link").val()};
            var func = $("#link-chooser #function-reference").data("callback")(JSON.stringify(doc));
      <?php
   }
   else
      echo $_REQUEST["callback"] . '(rowId);'
      ?>

      }

   </script>
   <?php
   return ob_get_clean();
}

function custom_widget_feeder_tab()
{
   ob_start();
   ?>  
   <script>
      var feedersList = EW.createTable({name: "feeders-list", headers: {Name: {}, Type: {}}, rowCount: true, url: "<?php echo EW_ROOT_URL; ?>app-admin/EWCore/get_widget_feeders", urlData: {type: "all"}, pageSize: 30
         , buttons: {"Select": function (rowId) {
   <?php
//Call the function which has been attached to the function reference element
   if ($_REQUEST["callback"] == "function-reference")
   {
      ?>
                  var doc = {type: "widget-feeder", feederType: rowId.data("field-type"), feederName: rowId.data("field-name")};
                  var func = $("#link-chooser #function-reference").data("callback")(JSON.stringify(doc));
      <?php
   }
   else
      echo $_REQUEST["callback"] . '(rowId);'
      ?>

            }}});
      //categoriesTable.container.css({position: "relative", "height": "500px"});
      $("#widgets-feeders-list").append(feedersList.container);
   </script>
   <?php
   return ob_get_clean();
}

//global $EW;
//EWCore::register_form("ew-file-chooser-form-default", "contents-list", ["title" => "Contents", "content" => get_contents_list()]);
//EWCore::register_form("ew-file-chooser-form-default", "media-list", ["title" => "Media", "content" => "Coming Soon..."]);
//EWCore::register_form("ew-file-chooser-form-default", "apps-pages-list", ["title" => "Apps", "content" => "Coming Soon ... "]);
EWCore::register_form("ew-link-chooser-form-default", "widgets-feeders-list", ["title" => "Widgets Feeders", "content" => custom_widget_feeder_tab()]);
EWCore::register_form("ew-link-chooser-form-default", "custom-url", ["title" => "URL", "content" => custom_url_tab()]);
$tabsDefault = EWCore::read_registry("ew-link-chooser-form-default");
$tabs = EWCore::read_registry("ew-link-chooser-form");
?>
<div class="header-pane tabs-bar row">
   <h1 id="form-title" class="col-xs-12">
      tr{Link Chooser}
   </h1>  
   <ul class="nav nav-tabs xs-nav-tabs">    
      <?php
      foreach ($tabsDefault as $id => $tab)
      {
         if ($id == "contents-list")
            echo "<li class='active '><a href='#{$id}' data-toggle='tab'>{$tab["title"]}</a></li>";
         else
            echo "<li class=''><a href='#{$id}' data-toggle='tab'>{$tab["title"]}</a></li>";
      }
      foreach ($tabs as $id => $tab)
      {
         echo "<li class='' ><a href='#{$id}' data-toggle='tab'>{$tab["title"]}</a></li>";
      }
      ?>
   </ul>
</div>
<form id="link-chooser"  action="#" method="POST">
   <div class="form-content tab-content tabs-bar no-footer row">

      <?php
      foreach ($tabsDefault as $id => $tab)
      {
         if ($id == "contents-list")
            echo "<div class='tab-pane active' id='{$id}'>{$tab["content"]}</div>";
         else
            echo "<div class='tab-pane' id='{$id}'>{$tab["content"]}</div>";
      }
      foreach ($tabs as $id => $tab)
      {
         echo "<div class='tab-pane' id='{$id}'>{$tab["content"]}</div>";
      }
      ?>
   </div>
</form>
<!--<div class="footer-pane row actions-bar action-bar-items">
</div>-->