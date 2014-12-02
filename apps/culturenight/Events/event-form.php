<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/* if($_REQUEST["ew_actionBase"])
  print_r ($_REQUEST["ew_actionBase"]); */
?>
<form id="event-form"   action="#" method="POST">    
   <div class="header-pane row">
      <h1 id="form-title" class="col-xs-12">
         <span>tr{New}</span>tr:culturenight{Event}
      </h1>  
   </div>
   <div class="form-content row">
      <div class="col-xs-12">
         <input type="hidden" name="id" id="id"/>
         <div class="row mar-bot">
            <div class="col-xs-12">
               <input class="text-field" data-label="Name" value="" id="name" name="name" data-validate="r">
            </div>
         </div>
         <div class="row mar-bot">
            <div class="col-xs-12">
               <label for="">
                  Logo
               </label>
               <img id="logo" alt="Event Logo" data-ew-plugin="image-chooser" style="max-height:400px;">

            </div>
         </div> 
         <div class="row mar-bot">
            <div class="col-xs-12">          
               <input  class="text-field" data-label="Venue" id="venue_name" name="venue_name" >
               <input  type="hidden" id="venue_id" name="venue_id" data-validate="r">
            </div>
         </div>    
         <div class="row mar-bot">
            <div class="col-xs-12">
               <input style="width:200px" data-label="Start Date" class="text-field" id="start_date" name="start_date">
            </div>
         </div>    
         <div class="row mar-bot">
            <div class="col-xs-12">
               <label for="hed">
                  <input id="hed" type="checkbox">
                  Has end date
               </label>
            </div>
         </div>
         <div id="edc" class="row mar-bot">
            <div class="col-xs-12">
               <input style="width:200px" data-label="End Date" class="text-field" id="end_date" name="end_date">
            </div>
         </div>    
         <div class="row mar-bot">
            <div class="col-xs-12">
               <label for="repeat">
                  Repeat
               </label>

               <select id="repeat" name="repeat" value="" data-width="100%">
                  <option value="0">No Repeat</option>
                  <option value="1">Daily</option>
                  <option value="2">Weekly</option>
                  <option value="3">Monthly</option>
               </select>

            </div>
         </div>
         <div class="row mar-bot">
            <div class="col-xs-12">
               <label for="category_id">
                  Category
               </label>

               <select id="category_id" name="category_id" value="" data-width="100%">
                  <option value="0">---</option>
                  <?php
                  $categories = new culturenight\Categories();
                  $cl = json_decode($categories->get_categories_list(), true);
                  $cl = $cl["result"];
                  //print_r($cl);
                  foreach ($cl as $category)
                  {
                     echo "<option value='{$category["id"]}' >{$category["name"]}</option>";
                  }
                  ?>
               </select>

            </div>
         </div>
         <div class="row mar-bot">
            <div class="col-xs-12">
               <select id="type"  name="type" value="" data-width="100%" data-label="Style">
                  <option value="">---</option>
                  <?php
                  $styles = EWCore::get_widget_feeder("menu", "events-styles");
                  $styles = json_decode($styles, TRUE);
                  //$cl = $cl["result"];
                  foreach ($styles as $style)
                  {
                     echo "<option value='{$style["id"]}' >{$style["title"]}</option>";
                  }
                  ?>
               </select>
            </div>
         </div>
         <div class="row mar-bot">
            <div class="col-xs-12">
               <textarea data-label="Description" class="text-field" value="" id="notes" name="notes"></textarea>
            </div>
            <div class="col-xs-12">
               <input class="text-field" data-label="Web" value="" id="web" name="web" >
            </div>
            <div class="col-xs-12">
               <input class="text-field" data-label="Tags" value="" data-role="tagsinput" id="tags" name="tags">
            </div>
            <div class="col-xs-12 btn-group mar-top" data-toggle="buttons">
               <label class="btn btn-default" >
                  <input type="checkbox"  id="published" name="published" value="1" >Publish
               </label>
               <label class="btn btn-default" >
                  <input type="checkbox"  id="promoted" name="promoted" value="1" >Promote
               </label>
            </div>
         </div>
      </div>
   </div>
   <div id="uis-panel-actions" class="footer-pane row actions-bar action-bar-items" >
   </div>
</form>
<script>
   function NEEvent()
   {
      var self = this;
      $("#venue_name").EW().inputButton({title: "Add Venue", id: "add-venue-input-btn", onClick: self.venueForm});
      $("#add-venue-input-btn").css("display", "none");
      $('#tags').tagsinput({
         //itemText: "name",
         typeahead: {
            source: function (query, process) {
               var res = [];
               //alert("asf");
               //alert(query + " " + process);

               $.post("<?php echo EW_ROOT_URL ?>app-culturenight/Events/get_tags_list", {
                  nameFilter: query
               },
               function (data)
               {
                  //data.success = true;

                  process(data.result);
               }, "json");

            }
         }
         //source:["test","ali","amali","ajibe"]
      });

      //$('#tags').tagsinput('add', {"value": 1, "text": "Amsterdam"});

      this.bAdd = EW.addActivity({title: "tr{Save}", activity: "app-culturenight/Events/add_event",
         postData: function ()
         {
            if (!$("#event-form").EW().validate())
            {
               return false;
            }
            var data = $.parseJSON($("#event-form").serializeJSON());
            data["logo"] = $("#logo").attr("data-filename") + ".582,400." + $("#logo").attr("data-file-extension");
            return data;
         },
         onDone: function (data)
         {
            $("body").EW().notify(data).show();
            $(document).trigger("events-list.refresh");
            $.EW("getParentDialog", $("#event-form")).trigger("close");
         }}).hide();

      this.bSave = EW.addActivity({title: "tr{Save Changes}", activity: "app-culturenight/Events/update_event", defaultClass: "btn-success",
         postData: function ()
         {

            if (!$("#event-form").EW().validate())
            {
               return false;
            }
            EW.lock($.EW("getParentDialog", $("#event-form")));
            var data = $.parseJSON($("#event-form").serializeJSON());
            data["logo"] = $("#logo").attr("data-filename") + ".582,400." + $("#logo").attr("data-file-extension");
            return data;
         },
         onDone: function (data)
         {
            EW.unlock($.EW("getParentDialog", $("#event-form")));
            $("body").EW().notify(data).show();
            $(document).trigger("events-list.refresh");
         }}).hide();
      $("#start_date").datepicker({
         format: "yy-mm-dd"
      });
      $("#end_date").datepicker({
         format: "yy-mm-dd"
      });
      $("#hed").click(function () {
         if ($(this).is(":checked"))
            $("#edc").show();
         else
            $("#edc").hide();
      });

      if ($("#hed").is(":checked"))
         $("#edc").show();
      else
         $("#edc").hide();

      $("#venue_name").autocomplete({
         //source: "<?php echo EW_ROOT_URL; ?>app-culturenight/Venues/get_venues_list",
         source: function (input) {
            $.post("<?php echo EW_ROOT_URL; ?>app-culturenight/Venues/get_venues_list", {nameFilter: $("#venue_name").val(), size: 30}, function (data) {
               input.trigger("updateList", [data.result]);
               if (data.result.length === 0)
               {
                  $("#venue_id").val("");
                  $("#add-venue-input-btn").fadeIn(300);
               }
               else
               {
                  $("#add-venue-input-btn").fadeOut(300);
               }
            }, "json");
         },
         templateText: "<li><a href='#'><%= name %></a><li>",
         insertText: function (item) {
            return item.name;
         }

      });
      //$("#event-form").off("refresh");
      $("#event-form").on("refresh", function (e,data) {
         if ($("#event-form #id").val())
         {
            //console.log(data+"ff");
            //$("#logo").prop("src", "<?php echo EW_ROOT_URL ?>res/images/" + data["logo"]);
            self.bAdd.comeOut(300);
            self.bSave.comeIn(300);
         }
         else
         {
            self.bAdd.comeIn(300);
            self.bSave.comeOut(300);
         }
      });
   }

   NEEvent.prototype.saveEvent = function (eventId)
   {
      //EW.lock($("#event-form"));
      var params = $.parseJSON($("#event-form").serializeJSON());
      params["logo"] = $("#logo").attr("data-filename") + ".582,400." + $("#logo").attr("data-file-extension");
      //alert($("#logo_image").attr("src"));
      //alert(JSON.stringify(params));
      if (!$("#event-form").EW().validate())
      {
         return;
      }
      $.post('<?php echo EW_ROOT_URL; ?>app-culturenight/Events/update_event', params, function (data) {
         $("body").EW().notify(data).show();
         //EW.unlock($("#event-form"));
         $(document).trigger("events-list.refresh");
      }, "json");
   };

   NEEvent.prototype.addEvent = function (eventId)
   {
      var params = $.parseJSON($("#event-form").serializeJSON());
      params["logo"] = $("#logo").attr("data-filename") + ".582,400." + $("#logo").attr("data-file-extension");
      //alert($("#logo_image").attr("src"));
      if (!$("#event-form").EW().validate())
      {
         return;
      }
      EW.lock($.EW("getParentDialog", $("#event-form")));
      $.post('<?php echo EW_ROOT_URL; ?>app-culturenight/Events/add_event', params, function (data) {
         $("body").EW().notify(data).show();
         $(document).trigger("events-list.refresh");
         $.EW("getParentDialog", $("#event-form")).trigger("close");
      }, "json");
   };
   NEEvent.prototype.venueForm = function (vId)
   {
      var data = {};
      if (typeof vId == "string")
         data = {
            venueId: vId
         };
      var dp = EW.createModal();
      $.post("<?php echo EW_ROOT_URL; ?>app-culturenight/Venues/venue-form.php", data, function (data) {
         dp.html(data);
         EW.setFormData("#venue-form", {"name": $("#venue_name").val()});
         $("#venue-form").on("added", function (e, data)
         {
            $("#venue_name").val(data.name);
            $("#venue_id").val(data.id);
         });
      });
   };
   var neEvent = new NEEvent();
<?php
if ($_REQUEST["eventId"])
{
   $event_id = $_REQUEST["eventId"];
   $v = new culturenight\Events();
   $event_info = $v->get_event($event_id);
   ?>
      var data = <?php echo ($event_info) ? $event_info : "{}" ?>;
      //var tags = $.parseJSON(data);
      //delete(data.tags);
      EW.setFormData("#event-form", data);
      $("#form-title").html("<span>tr:culturenight{Edit Event}</span>" + data.name);
      $("#logo").prop("src", "<?php echo EW_ROOT_URL ?>res/images/" + data.logo);
       /*$("#logo_image").attr("data-file-extension",/[^.]+$/.exec(data.logo));
       $("#logo_image").attr("data-filename",/^[^.]+/.exec(data.logo));*/
      //alert(/[^.]+$/.exec(data.logo)+" "+/^[^.]+/.exec(data.logo)+" "+data.logo);
      if (data.end_date !== data.start_date)
         $("#hed").click();
      if (data.tags)
         $.each(data.tags, function (k, v) {
            $('#tags').tagsinput("add", v["name"]);
            //alert(v["name"]);
         });

   <?php
}
?>
   $("#event-form").trigger("refresh");

</script>