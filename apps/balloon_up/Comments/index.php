<script  type="text/javascript">
  function Events()
  {
    this.table = EW.createTable({name: "categories-list", headers: {Name: {}, Slug: {}}, rowCount: true, url: "<?php echo EW_ROOT_URL; ?>app-culturenight/Categories/get_categories_list", pageSize: 30});
    $("#Comments").html(this.table.container);
  }
  var events = new Events();
</script>