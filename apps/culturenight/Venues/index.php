<?php
session_start();

if (!$_SESSION['login'])
{
  header('Location: Login.php');
  return;
}
?>

<script  type="text/javascript">
  function Venues()
  {
    var self = this;
    this.bAdd = EW.addAction("Add Venue", this.venueForm).hide().comeIn(300);
    this.table = EW.createTable({name: "venues-list", columns: ["name", "address", "description"], headers: {Name: {}, Address: {}, Description: {}}, rowCount: true, url: "<?php echo EW_ROOT_URL; ?>app-culturenight/Venues/get_venues_list", pageSize: 30
              , onEdit: this.venueForm});
    $("#Venues").html(this.table.container);
    $(document).off("venues-list.refresh");
    $(document).on("venues-list.refresh", function() {
      self.table.refresh();
    });
  }

  Venues.prototype.venueForm = function(vId)
  {
    var data = {};
    if (typeof vId == "string")
      data = {venueId: vId};
    var dp = EW.createModal();
    $.post("<?php echo EW_ROOT_URL; ?>app-culturenight/Venues/venue-form.php", data, function(data) {
      dp.html(data);
    });
  };
  var venues = new Venues();
</script>