<?php
session_start();

if (!$_SESSION['login'])
{
  header('Location: Login.php');
  return;
}
?>
<div id="e-l">

</div>
<script  type="text/javascript">
  function Countries()
  {
    var self = this;
    this.bAdd = EW.addAction("Add Country", this.countryForm).hide().comeIn(300);
    this.table = EW.createTable({
      name: "countries-list",
      headers: {
        Name: {
        },
        ISO: {
        },
        Slug: {
        }
      },
      rowCount: true,
      url: "<?php echo EW_ROOT_URL; ?>app-culturenight/Countries/getcountries_list",
      pageSize: 30,
      onEdit: this.countryForm,
      onDelete: this.deleteCountry
    });
    $("#Countries").html(this.table.container);
    $(document).off("countries-list.refresh");
    $(document).on("countries-list.refresh", function() {
      self.table.refresh();
    });
  }
  Countries.prototype.countryForm = function(eId)
  {
    var data = {
    };
    if (typeof eId === "string")
      data = {
        country_id: eId
      };
    var countryFormDP = EW.createModal();
    EW.lock(countryFormDP);
    $.post("<?php echo EW_ROOT_URL; ?>app-culturenight/Countries/country-form.php", data, function(data) {
      countryFormDP.html(data);
    });
  };
  
  Countries.prototype.deleteCountry = function(eId)
  {
    if (confirm("Are you sure of deleting this country?"))
    {
      var data = {
      };
      if (typeof eId === "string")
        data = {
          country_id: eId
        };
      EW.lock(self.table);
      $.post("<?php echo EW_ROOT_URL; ?>app-culturenight/Countries/delete_country", data, function(data) {
        $("body").EW().notify(data);
        $(document).trigger("countries-list.refresh");
      },"json");
    }
  };
  var countries = new Countries();
</script>