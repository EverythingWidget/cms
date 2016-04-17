<div class="col-xs-12 mt">
  <input class="text-field" data-label="Publish date" id="ew_blog/date_published" name="ew_blog/date_published" value=""/>
</div>

<script>
  (function () {
    $("#article-form").on('refresh', function () {
      var value = $("#ew_blog\\/date_published");
      value.datepicker({
        format: 'yyyy-mm-dd'
      });
    });
  }());
</script>