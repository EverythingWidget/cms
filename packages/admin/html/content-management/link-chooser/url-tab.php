<div class="col-xs-12">
   <input class="text-field" data-label="URL link" name="url_link" id="url_link"/>
</div>

<div class="col-xs-12 ">
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
         var doc = {type: "admin/content-management/link", url: $("#url_link").val()};
         var func = $("#link-chooser #function-reference").data("callback")(JSON.stringify(doc));
   <?php
}
else
   echo $_REQUEST["callback"] . '(rowId);'
   ?>

   }
   $("#link-chooser").on("refresh.url", function (e, data) {
      if (data.type === "link") {
         $("#link-chooser #url_link").val(data.url).change();
      }
   });
</script>