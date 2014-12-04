<?php
//echo "googooli";

$venue_info = EWCore::process_command("culturenight", "Venues", "get_venue", array("venueId" => $_REQUEST["venueId"]));
//echo $venue_info;
//echo $venue_info;
?>
<div class="header-pane row">
   <h1 id='form-title' class="col-xs-12">
      <span>tr{New}</span>Venue
   </h1>
</div>
<div class="form-content  row">
   <form id="venue-form" class="col-xs-12" action="#" method="POST">
      <input type="hidden" id="id" name="id" >
      <div class="row mar-bot">
         <div class="col-xs-12">
            <input class="text-field" data-label="Name" value="" id="name" name="name">
         </div>
      </div>    
      <div class="row">
         <div class="col-xs-12">
            <img id="logo_image" alt="Event Logo" data-ew-plugin="image-chooser" style="height:220px;">
            <input type="hidden" name="logo" id="logo"/>
         </div>
      </div>
      <div class="row">
         <div class="col-xs-12">
            <textarea data-label="Description" class="text-field" value="" id="description" name="description"></textarea>
         </div>
         <div class="col-xs-12">

            <select data-label="Country" class="text-field" id="country_id" name="country_id" onchange="neVenue.setCenter()">
               <option value=""></option>
               <?php
               $countries = new culturenight\Countries();
               $cl = json_decode($countries->getcountries_list(), true);
               $cl = $cl["result"];
               foreach ($cl as $country)
                  echo "<option value='{$country["id"]}'>{$country["name"]}</option>";
               ?>
            </select>

         </div>
      </div>    
      <div class="row">
         <div class="col-xs-12">
            <input type="hidden" id="city_id" name="city_id" >
            <input class="text-field" data-label="City" value="" id="city_name" name="city_name" onkeyup="neVenue.setCenter()">
         </div>
      </div>    
      <div class="row mar-bot">
         <div class="col-xs-12">
            <input class="text-field" data-label="Address" value="" id="address" name="address" onkeyup="neVenue.setCenter()">
         </div>   
      </div>
      <div class="row mar-bot">
         <div class="col-xs-12 ">
            <label >
               Map
            </label>
            <label class="small">
               You can click and drag marker to point exact location
            </label>
            <input type="hidden" id="lat" name="lat" >
            <input type="hidden" id="lng" name="lng" >
            <div id="map-canvas" class="text-field" style="display:block;width:auto;height:400px;"></div>
         </div>    
      </div>
   </form>
</div>
<div class="footer-pane row actions-bar action-bar-items">
</div>

<script>
   function NEVenues()
   {
      var self = this;
      this.initialized = false;
      this.map;
      this.marker;
      this.bAdd = EW.addAction("Save", $.proxy(this.addVenue, this)).hide();
      this.bSave = EW.addAction("Save Changes", $.proxy(this.saveVenue, this)).addClass("btn-success").hide();
      $("#city_name").autocomplete({
         source: function (request, response) {
            $("#city_id").val("");
            $.post("<?php echo EW_ROOT_URL; ?>app-culturenight/Cities/get_cities_by_country_id", {
               nameFilter: request.term,
               countryId: $("#country_id").val()
            },
            function (data)
            {
               response($.map(data.result, function (item)
               {
                  return {
                     label: item.name,
                     value: item.id
                  };
               }));
            }, "json");
         },
         minLength: 1,
         focus: function (event, ui) {
            $("#city_name").val(ui.item.label);
            $("#city_id").val(ui.item.value);
            return false;
         },
         select: function (event, ui) {
            $("#city_name").val(ui.item.label);
            $("#city_id").val(ui.item.value);
            self.setCenter();
            return false;
         },
         open: function (e, ui) {

            //$('ul.ui-autocomplete:visible').append("<li class='ui-menu-item'><a href='javascript:void(0)' class='btn btn-link'>New Venue</a></li>"); //See all results
            $("ul.ui-autocomplete:visible").css({
               "max-height": "150px",
               overflow: "auto"
            });
         }
      });
      $("#venue-form").on("refresh", function (e, data) {

         if ($("#venue-form #id").val())
         {
            self.bAdd.comeOut(300);
            self.bSave.comeIn(300);
            $("#form-title").html("<span>Edit Venue</span>" + data.name);
            $("#logo_image").prop("src", "<?php echo EW_ROOT_URL ?>res/images/" + data.logo);
         }
         else
         {
            self.bAdd.comeIn(300);
            self.bSave.comeOut(300);
         }
      });
   }

   NEVenues.prototype.setCenter = function (name)
   {
      if (!this.initialized)
         return;
      name = $("#country_id option:selected").text() + ", " + $("#city_name").val() + ", " + $("#address").val();
      var to = this;
      var geocoder = new google.maps.Geocoder();
      geocoder.geocode({
         'address': name
      },
      function (results, status) {
         if (status == google.maps.GeocoderStatus.OK) {
            to.marker.setMap(to.map);
            to.marker.setPosition(results[0].geometry.location);
            to.map.setCenter(results[0].geometry.location);
            $("#lat").val(results[0].geometry.location.lat());
            $("#lng").val(results[0].geometry.location.lng());
         }
         else
         {
            if (to.marker)
               to.marker.setMap(null);
            $("#lat").val(0);
            $("#lng").val(0);
         }
      });
   };
   NEVenues.prototype.initialize = function () {
      this.initialized = true;
      this.marker = new google.maps.Marker({
         draggable: true
      });
      var mapOptions = {
         zoom: 1,
         streetViewControl: false,
         center: new google.maps.LatLng(0, 0),
         mapTypeId: google.maps.MapTypeId.ROADMAP
      };
      neVenue.map = new google.maps.Map(document.getElementById('map-canvas'),
              mapOptions);
      if ($("#lat").val() && $("#lng").val())
      {
         neVenue.map.setCenter(new google.maps.LatLng($("#lat").val(), $("#lng").val()));
         neVenue.map.setZoom(15);
         neVenue.marker.setMap(neVenue.map);
         neVenue.marker.setPosition(new google.maps.LatLng($("#lat").val(), $("#lng").val()));
      }

      google.maps.event.addListener(neVenue.map, 'click', function (e) {
         neVenue.marker.setMap(neVenue.map);
         neVenue.marker.setPosition(e.latLng);
         neVenue.marker.setTitle(e.latLng.toString());
         $("#lat").val(e.latLng.lat());
         $("#lng").val(e.latLng.lng());
         //alert(e.latLng.lat());
      });
   };
   var loadScript = function () {

      if ($("#google-map-api").length != 0)
      {
         neVenue.initialize();
         return;
      }
      var script = document.createElement('script');
      script.id = "google-map-api";
      script.type = 'text/javascript';
      script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&' + 'callback=neVenue.initialize';
      $(document).find("head").append(script);
   };
   NEVenues.prototype.saveVenue = function (eventId)
   {
      var params = $.parseJSON($("#venue-form").serializeJSON());
      params["logo"] = $("#logo_image").attr("data-filename") + ".640,440." + $("#logo_image").attr("data-file-extension");
      //alert($("#logo_image").attr("src"));
      $.post('<?php echo EW_ROOT_URL; ?>app-culturenight/Venues/update_venue', params, function (data) {
         $("body").EW().notify(data);
         $(document).trigger("venues-list.refresh");
      }, "json");
   };
   NEVenues.prototype.addVenue = function (eventId)
   {
      var params = $.parseJSON($("#venue-form").serializeJSON());
      params["logo"] = $("#logo_image").attr("data-filename") + ".640,440." + $("#logo_image").attr("data-file-extension");
      EW.lock($.EW("getParentDialog", $("#venue-form")));
      $.post('<?php echo EW_ROOT_URL; ?>app-culturenight/Venues/add_venue', params, function (data) {

         //listCategories();
         $("body").EW().notify(data);
         // when the venue has been added successfully, added event will be triggered
         if (data.status === "success")
         {
            $("#venue-form").trigger("added", [data]);
         }
         $(document).trigger("venues-list.refresh");
         $.EW("getParentDialog", $("#venue-form")).trigger("close");
      }, "json");
   };
   var neVenue = new NEVenues();
   loadScript();
   EW.setFormData("#venue-form", <?php echo ($venue_info) ? $venue_info : "{}"; ?>);


</script>