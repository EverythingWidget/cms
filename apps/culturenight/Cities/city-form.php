<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if ($_REQUEST["cityId"])
{
  $city_id = $_REQUEST["cityId"];
  $cities = new culturenight\Cities();
  $city_info = json_decode($cities->get_city($city_id), true);
}
?>
<form id="city-form"   action="#" method="POST">
  <div class="header-pane row">
    <h1 id="form-title" class="col-xs-12">
      <?php
      echo ($city_info) ? "<span>Edit</span> {$city_info["name"]}" : "<span>New</span>City";
      ?>
    </h1>  
  </div>
  <div class="form-content  row">
    <input type="hidden" id="id" name="id" value="<?php echo $city_info["id"] ?>"/>
    <div class="col-xs-12">
      <div class="row">
        <div class="col-xs-12 mar-bot">
          <select data-label="Country" id="country_id" name="country_id" value="<?php echo $city_info["country_id"] ?>" data-width="100%">
            <option value="0">---</option>
            <?php
            $countries = new culturenight\Countries();
            $cl = json_decode($countries->getcountries_list(), true);
            $cl = $cl["result"];
            foreach ($cl as $country)
            {
              if ($city_info["country_id"] == $country["id"])
                echo "<option value='{$country["id"]}' selected>{$country["name"]}</option>";
              else
                echo "<option value='{$country["id"]}' >{$country["name"]}</option>";
            }
            ?>
          </select>

        </div>
      </div>    
      <div class="row">
        <div class="col-xs-12 mar-bot">
          <input class="text-field" data-label="Name" value="<?php echo $city_info["name"] ?>" id="name" name="name">
        </div>
      </div>    
    </div>
  </div>
  <div class="footer-pane row actions-bar action-bar-items" >
  </div>
</form>
<script>
  var CityForm = (function()
  {
    function CityForm()
    {
      this.bAdd = EW.addAction("tr{Save}", $.proxy(this.addCity, this)).hide();
      this.bSave = EW.addAction("tr{Save Changes}", $.proxy(this.updateCity, this)).addClass("btn-success").hide();
    }

    CityForm.prototype.addCity = function()
    {
      if ($("#name").val())
      {
        //alert(media.itemId);
        var formParams = $.parseJSON($("#city-form").serializeJSON());
        EW.lock($("#city-form"), "Saving...");
        $.post('<?php echo EW_ROOT_URL; ?>app-culturenight/Cities/add_city', formParams, function(data) {
          if (data.status === "success")
          {
            $.EW("getParentDialog", $("#city-form")).trigger("close");
            $(document).trigger("cities-list.refresh");
            $("body").EW().notify(data).show();
          }
          else
          {
            $("body").EW().notify(data).show();
          }
          EW.unlock($("#city-form"));
        }, "json");
      }
    };

    CityForm.prototype.updateCity = function()
    {
      if ($("#name").val())
      {
        //alert(media.itemId);
        var formParams = $.parseJSON($("#city-form").serializeJSON());
        EW.lock($("#city-form"), "Saving...");
        $.post('<?php echo EW_ROOT_URL; ?>app-culturenight/Cities/update_city', formParams, function(data) {
          if (data.status === "success")
          {
            $(document).trigger("cities-list.refresh");
            $("body").EW().notify(data).show();
          }
          else
          {
            $("body").EW().notify(data).show();
          }
          EW.unlock($("#city-form"));
        }, "json");
      }
    };

    return new CityForm();
  })();

<?php
if ($_REQUEST["cityId"])
{
  ?>
    CityForm.bSave.comeIn(300);
  <?php
}
else
{
  ?>
    CityForm.bAdd.comeIn(300);
  <?php
}
?>
</script>
