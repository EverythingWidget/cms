<form id="country-form"   action="#" method="POST">
  <div class="header-pane row">
    <h1 id="form-title" class="col-xs-12">
      <span>New</span>Country
    </h1>  
  </div>
  <div class="form-content row">
    <div class="col-xs-12">
      <input type="hidden" name="id" id="id"/>
      <div class="row mar-bot">
        <div class="col-xs-12">
          <input class="text-field" data-label="Name" value="" id="name" name="name">
        </div>
      </div>
      <div class="row mar-bot">
        <div class="col-xs-12">
          <input class="text-field" data-label="ISO" value="" id="iso" name="iso">
        </div>
      </div>
    </div>
  </div>
  <div class="footer-pane row actions-bar action-bar-items" >
  </div>
</form>
<script>
  var CountryForm = (function()
  {
    function CountryForm()
    {
      this.bAdd = EW.addAction("Add", $.proxy(this.addCountry, this)).hide();
      this.bSave = EW.addAction("Save Changes", $.proxy(this.updateCountry, this)).addClass("green").hide();
    }
    
    CountryForm.prototype.addCountry = function()
    {
      if ($("#name").val())
      {
        //alert(media.itemId);
        var formParams = $.parseJSON($("#country-form").serializeJSON());
        EW.lock($("#country-form"), "Saving...");
        $.post('<?php echo EW_ROOT_URL; ?>app-culturenight/Countries/add_country', formParams, function(data) {
          if (data.status === "success")
          {
            $.EW("getParentDialog", $("#country-form")).trigger("close");
            $(document).trigger("countries-list.refresh");
            $("body").EW().notify(data);
          }
          else
          {
            $("body").EW().notify(data);
          }
          EW.unlock($("#country-form"));
        }, "json");
      }
    };

    CountryForm.prototype.updateCountry = function()
    {
      if ($("#name").val())
      {
        //alert(media.itemId);
        var formParams = $.parseJSON($("#country-form").serializeJSON());
        EW.lock($("#country-form"), "Saving...");
        $.post('<?php echo EW_ROOT_URL; ?>app-culturenight/Countries/update_country', formParams, function(data) {
          if (data.status === "success")
          {
            $(document).trigger("countries-list.refresh");
            $("body").EW().notify(data);
          }
          else
          {
            $("body").EW().notify(data);
          }
          EW.unlock($("#country-form"));
        }, "json");
      }
    };

    return new CountryForm();
  })();

<?php
if ($_REQUEST["country_id"])
{
  $country_info = Countries::get_country($_REQUEST["country_id"]);
  ?>
    var data = <?php echo $country_info ?>;
    EW.setFormData("#country-form", data);
    $("#form-title").html("<span>Edit Country</span>" + data.name);
    CountryForm.bSave.comeIn(300);
  <?php
}
else
{
  ?>
    CountryForm.bAdd.comeIn(300);
  <?php
}
?>
</script>