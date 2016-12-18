<div class="form-block">
  <system-field class="field">
    <label>tr{URL link}</label>
    <input class="text-field" name="url_link" id="url_link"/>
  </system-field>

  <div class="block-row">
    <button type="button" class="btn btn-primary" onclick="url_done();">Done</button>
  </div>
</div>

<script>
  function url_done() {
<?php
//Call the function which has been attached to the function reference element
if ($_REQUEST["callback"] == "function-reference") {
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