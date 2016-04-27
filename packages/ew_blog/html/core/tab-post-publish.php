<div class="col-xs-12 mt">
  <input class="text-field" data-label="Publish date" id="ew_blog/date_published" name="ew_blog/date_published" value=""/>
  <label class="checkbox">
    Draft
    <input type="checkbox" name="ew_blog/draft" value="1"/><i></i>
  </label>
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