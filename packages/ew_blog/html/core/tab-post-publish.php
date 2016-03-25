<div class="col-xs-12 mt">
  <input class="text-field" data-label="Publish date" id="ew_blog/publish_date" name="ew_blog/publish_date" value=""/>
</div>

<script>
  (function () {
    $("#article-form").on('refresh', function () {
      var value = $("#ew_blog\\/publish_date");
      value.datepicker({
        format: 'yyyy-mm-dd'
      });
    });
  }());
</script>