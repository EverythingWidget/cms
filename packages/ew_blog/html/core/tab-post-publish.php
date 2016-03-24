<div class="col-xs-12 mt">
  <input class="text-field" data-label="Publish date" id="ew_blog_publish_date" name="ew_blog_publish_date" value=""/>
</div>

<script>
  (function () {
    var value = $("#publish_date");
    value.datepicker({
      format: 'dd-mm-yyyy'
    });
  }());
</script>