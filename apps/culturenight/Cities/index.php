<?php
session_start();

if (!$_SESSION['login'])
{
  header('Location: Login.php');
  return;
}
?>

<script  type="text/javascript">
  function Events()
  {
    var self = this;
    this.bAdd = EW.addAction("Add City", this.cityForm);
    this.table = EW.createTable({
      name: "cities-list",
      columns: ["name", "country_name"],
      headers: {
        Name: {
        },
        Country: {
        }
      },
      rowCount: true,
      url: "<?php echo EW_ROOT_URL; ?>app-culturenight/Cities/getcities_list",
      pageSize: 30,
      onEdit: this.cityForm
    });
    $("#Cities").html(this.table.container);
    $(document).off("cities-list.refresh");
    $(document).on("cities-list.refresh", function() {
      self.table.refresh();
    });
  }

  Events.prototype.cityForm = function(cId)
  {
    var data = {
    };
    if (typeof cId == "string")
      data = {
        cityId: cId
      };
    var dp = EW.createModal();
    $.post("<?php echo EW_ROOT_URL; ?>app-culturenight/Cities/city-form.php", data, function(data) {
      dp.html(data);
    });
  };
  var events = new Events();
</script>