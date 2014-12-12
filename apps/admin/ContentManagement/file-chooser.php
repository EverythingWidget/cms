<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// {@tr:(.*)}
function get_contents_list()
{
   ob_start();
   ?>

   <script>
      var parentId = new Array();
      var oldParentId = 0;
      var table = EW.createTable({name: "articles-list-table", columns: ["title", "round_date_created"], headers: {Name: {}, "Date Created": {}}, rowCount: true, url: "<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_articles_list", pageSize: 30
         , buttons: {"Select": function (rowId) {
   <?php
//Call the function which has been attached to the function reference element
   if ($_REQUEST["callback"] == "function-reference")
   {
      ?>
                  var doc = {type: "article", id: rowId.data("field-id")};
                  var func = $("#link-chooser #function-reference").data("callback")(JSON.stringify(doc));
      <?php
   }
   else
      echo $_REQUEST["callback"] . '(rowId);'
      ?>

            }}});
      var categoriesTable = EW.createTable({name: "categories-list-table", columns: ["title", "round_date_created"], headers: {Name: {}, "Date Created": {}}, rowCount: true, url: "<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/get_categories_list", pageSize: 30
         , buttons: {"Select": function (rowId) {
   <?php
//Call the function which has been attached to the function reference element
   if ($_REQUEST["callback"] == "function-reference")
   {
      ?>
                  var doc = {type: "category", id: rowId.data("field-id")};
                  var func = $("#link-chooser #function-reference").data("callback")(JSON.stringify(doc));
      <?php
   }
   else
      echo $_REQUEST["callback"] . '(rowId);'
      ?>

            }, "Browse": function (row)
            {
               //oldParentId = parentId;
               table.refresh({parentId: row.data("field-id")});
               categoriesTable.refresh({parentId: row.data("field-id")});
               $("#documents-up-btn").comeIn();
               if (parentId.length == 0)
               {
                  parentId.push(0);
                  parentId.push(row.data("field-id"));
               }
               else
               {
                  parentId.push(row.data("field-id"));
               }
            }}});
      categoriesTable.container.css({position: "relative", "height": "500px"});
      table.container.css({position: "relative", height: "500px"});
      $("#contents-list").append($("<div class=col-xs-12><h2><button class='button' id='documents-up-btn' type='button' style='display:none;float:right;'>UP</button>Folders</h2></div>").append(categoriesTable.container));
      $("#contents-list").append($("<div class='col-xs-12 mar-bot'><h2>Files</h2></div>").append(table.container));
      $("#documents-up-btn").click(function () {
         var index = parentId.length - 2;
         table.refresh({parentId: parentId[index]});
         categoriesTable.refresh({parentId: parentId[index]});
         parentId.splice(index, 2);
         //parentId = oldParentId;
         if (parentId.length == 0)
            $("#documents-up-btn").comeOut();
      });</script>
   <?php
   return ob_get_clean();
}

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
EWCore::register_form("ew-file-chooser-form-default", "contents-list", ["title" => "Contents", "content" => get_contents_list()]);
//EWCore::register_form("ew-file-chooser-form-default", "media-list", ["title" => "Media", "content" => "Coming Soon..."]);
//EWCore::register_form("ew-file-chooser-form-default", "apps-pages-list", ["title" => "Apps", "content" => "Coming Soon ... "]);
EWCore::register_form("ew-file-chooser-form-default", "widgets-feeders-list", ["title" => "Widgets Feeders", "content" => custom_widget_feeder_tab()]);
EWCore::register_form("ew-file-chooser-form-default", "custom-url", ["title" => "URL", "content" => custom_url_tab()]);
$tabsDefault = EWCore::read_registry("ew-file-chooser-form-default");
$tabs = EWCore::read_registry("ew-file-chooser-form");
?>
<div class="header-pane tabs-bar row">
   <h1 id="form-title" class="col-xs-12">
      File Chooser
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