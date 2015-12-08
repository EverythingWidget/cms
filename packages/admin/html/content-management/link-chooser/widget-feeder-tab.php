<script>
   var feedersList = EW.createTable({name: "feeders-list", headers: {Name: {}, Type: {}},
      rowCount: true,
      url: "<?php echo EW_ROOT_URL; ?>admin/api/EWCore/get_widget_feeders",
      urlData: {type: "all"},
      pageSize: 30
      , buttons: {"Select": function (rowId) {
<?php
//Call the function which has been attached to the function reference element
if ($_REQUEST["callback"] == "function-reference")
{
   ?>
               var doc = {type: "widget-feeder", feederType: rowId.data("field-type"), feederApp: rowId.data("field-app"), feederName: rowId.data("field-name")};
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