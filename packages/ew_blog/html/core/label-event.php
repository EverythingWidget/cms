<div class="col-xs-12">
  <input class="text-field" type="hidden" id="{{comp_id}}_key" name="key" value="{{comp_id}}"/>
  <input class="text-field" data-label="Select a date" id="{{comp_id}}_value" name="value" value=""/>
</div>
<div class="col-xs-12">
  <ul id="{{comp_id}}_attached" class="list">

  </ul>
</div>
<script>
  (function () {
    var value = $("#{{comp_id}}_value");
    value.datepicker({
      format: 'yyyy-mm-dd'
    });

    $("#{{form_id}}").on("refresh", function (e, formData) {
      value.change();
    });
  }());
</script>