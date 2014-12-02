<?php
session_start();
$uiStructureId = $_REQUEST['uisId'];
$panel_id = $_REQUEST["panelId"];
$container_id = $_REQUEST["containerId"];

/* if (isset($panel_id))
  {
  $dbc = get_db_connection();

  $result = $dbc->query("SELECT * FROM ui_structures_parts WHERE id = $panel_id");
  $row = $result->fetch_assoc();
  } */
?>
<div class="header-pane  tabs-bar row">
   <h1 id="uis-panel-title" class="col-xs-12">
      <span>tr{Add}</span>tr{Panel}
   </h1>
   <ul class="nav nav-tabs">
      <li class="active"><a href="#properties" data-toggle="tab">Properties</a></li>
      <li><a href="#appearance" data-toggle="tab">Appearance</a></li>

      <li><a href='#size-layout' data-toggle='tab'>Size & Layout</a></li>

   </ul>
</div>
<div class="form-content  tabs-bar row">
   <div class="tab-content col-xs-12">
      <div class="tab-pane active" id="properties">
         <form id="uis-panel" >
            <div class="row">
               <input type="hidden" name="cmd" id="cmd" >

               <div class="col-xs-12">
                  <input data-label="ID" id="style_id" name="style_id" class="text-field" value="<?php echo $row["style_id"] ?>">
               </div>
            </div>
            <div class="row">
               <div class="col-xs-12">
                  <input id="style_class" data-label="Class" name="style_class" class="text-field" >
                  <label class="small" id="used-classes"><?php echo $row["style_class"] ?></label>
               </div>
            </div>
            <div class="row">
               <div class="col-xs-12 col-lg-6" >
                  <div class="dashed-box">
                     <div class="col-xs-12">
                        <h2>Used</h2>
                     </div>
                     <div class="col-xs-12" id="panel-classes" >
                     </div>
                  </div>
               </div>
               <div class="col-xs-12 col-lg-6"  >
                  <div class="dashed-box">
                     <div class="col-xs-12">
                        <h2>Classes</h2>
                     </div>
                     <div class="col-xs-12" id="available-classes" >
                        <?php
                        //global $EW;
                        $css_class = "panel";
                        if (!$container_id)
                           $css_class = "block";
                        $templates = json_decode(EWCore::parse_css($_REQUEST["template"] . '/template.css', $css_class), true);
                        foreach ($templates as $t)
                        {
                           ?>
                           <label><?php echo $t ?></label>
                           <?php
                        }
                        ?>
                     </div>
                  </div>
               </div>
            </div>
         </form>
      </div>
      <div class="tab-pane" id="appearance">
         <form id="appearance-conf" >
            <?php include 'uis-panel-appearance.php'; ?>
         </form>
      </div>

      <div class="tab-pane" id="size-layout">
         <?php include 'uis-widget-size-layout.php'; ?>
      </div>

   </div>

</div>
<div id="uis-panel-actions" class="footer-pane row actions-bar action-bar-items" >

</div>
<script  type="text/javascript">

   function UISPanel()
   {
      this.bAdd = EW.addAction("tr{Add}", this.addPanel, {display: "none"}, "uis-panel-actions");
      this.bEdit = EW.addAction("tr{Save}", this.updatePanel, {display: "none"}, "uis-panel-actions");
      $("#appearance-conf input:radio").change(function () {

         if ($(this).val() == "no")
         {
            $("#title-text").prop("disabled", true);
         }
         else
         {
            $("#title-text").prop("disabled", false);
         }
      });


      this.panelId = "<?php echo $panel_id ?>";
      this.containerId = "<?php echo $container_id ?>";
      if (this.panelId)
      {
         var panel = $("#fr").contents().find("body #base-content-pane div[data-panel-id='" + this.panelId + "']");
         var panelParams = (panel.attr("data-panel-parameters")) ? $.parseJSON(panel.attr("data-panel-parameters")) : {};
         EW.setFormData("#appearance-conf", panelParams);
         $("#uis-panel-title").html("<span>tr{Edit}</span>tr{Panel}");
         $("#used-classes").text(panel.prop("class"));
         this.bAdd.comeOut(200);
         this.bEdit.comeIn(300);
      }
      else
      {
         this.bAdd.comeIn(300);
         this.bEdit.comeOut(200);
      }
      $("#style_class").keyup(this.setClasses);

   }


   UISPanel.prototype.readClasses = function ()
   {
      //$("#available-classes").html("");

      //$.getJSON("index.php", {className: "EWCore", cmd: "parse_css", path: $("#template").val()}, function(data)
      //{
      var self = this;
      var classes = $("#used-classes").text();

      classes = classes.split(" ");
      $.each($("#available-classes").find("label"), function (k, v) {
         var a = $("<input type='checkbox'>");
         a.val($(v).text().substring(7));
         $(v).text($(v).text().substring(7));
         a.change(function (event) {
            if ($(this).is(":checked")) {
               $("#panel-classes").append($(v));
            }
            else
            {
               $("#available-classes").append($(v));
            }
            self.setClasses();
            //event.preventDefault()
         });
         $.each(classes, function (i, c) {
            if (a.val() === (c))
            {
               a.prop('checked', true);
               $("#panel-classes").append($(v));
               classes[i] = null;
            }
         });
         //var l = $("<label></label>");
         $(v).css({float: "left", margin: "0px 5px 5px 0px"});
         $(v).prepend(a);
         $(v).addClass("button white");
      });

      $.each($("#size-layout").find("input:radio,input:checkbox"), function (k, v) {
         $.each(classes, function (i, c) {
            if ($(v).val() === c && !$(v).is(":checked"))
            {
               $(v).click();
               $(v).prop("checked", true);
            }
         });
      });
      $.each($("#size-layout").find("input[data-slider]"), function (k, v) {
         $.each(classes, function (i, c) {
            if (c)
            {
               var sub = c.match(/(\D+)(\d*)/);
               //alert(sub[1]+" "+$(v).attr("name")+" "+sub[2]);
               if (sub && $(v).attr("name") === sub[1])
               {
                  //alert(sub[2]);
                  $(v).val(sub[2]).change();
               }
            }
         });
      });
      $("#size-layout input:radio,#size-layout input:checkbox,input[data-slider]").change(function (event) {
         self.setClasses();
      });
      self.setClasses();
   };

   UISPanel.prototype.setClasses = function ()
   {

      var styleClass = $("#style_class").val() + " ";
      $("#used-classes").text("");

      $.each($("#panel-classes").find("input"), function (k, v) {
         styleClass += ($(v).val() + " ");
      });

      $.each($("#size-layout").find("input:radio:checked:not(:disabled),input:checkbox:checked:not(:disabled)"), function (k, v) {

         styleClass += $(v).val() + " ";
         //$("#used-classes").append();
      });
      $.each($("#size-layout input[data-slider]:not(:disabled)"), function (k, v) {
         styleClass += $(v).attr("name") + $(v).val() + " ";
         //$("#used-classes").append($(v).attr("name") + $(v).val() + " ");
      });
      //$("#used-classes").text(styleClass);
      var classes = styleClass.split(" ");
      var html = "";
      $.each(classes, function (i, v) {
         if (v)
            html += "<span class='tag label label-default'>" + v + " </span>";
      });
      $("#used-classes").html(html);
   };

   // Create and add new div to the page
   UISPanel.prototype.addPanel = function (pId)
   {
      //EW.lock(neuis.currentDialog, "...");
      var params = $("#appearance-conf").serializeJSON();
      var div = $("<div><div class='row'></div></div>");
      div.prop("id", $("#style_id").val());
      div.attr("data-panel-parameters", params);
      div.prop("class", "panel " + $("#used-classes").text());

      //if (uisPanel.containerId != 0)

      var containerElement = $("#fr").contents().find("body #base-content-pane div[data-panel-id='" + uisPanel.containerId + "']");
      if (containerElement.hasClass("block"))
      {
         containerElement.append(div);
      }
      else if (!uisPanel.containerId)
      {
         var block = $("<div></div>");
         block.prop("id", $("#style_id").val());
         block.attr("data-panel-parameters", params);
         block.prop("class", "panel block row " + $("#used-classes").text());
         $("#fr").contents().find("body #base-content-pane").append(block);
      }
      else
      {
         containerElement.children(".row").append(div);
      }
      //else*/
      //$("#fr").contents().find("body #base-content-pane").append(div);
      $("#inspector-editor").trigger("refresh");
      $.EW("getParentDialog", $("#uis-panel")).trigger("close");

   };

   UISPanel.prototype.updatePanel = function (pId)
   {
      var params = $("#appearance-conf").serializeJSON();
      var div = $("#fr").contents().find("body #base-content-pane div[data-panel-id='" + uisPanel.panelId + "']");
      var oldParameters = div.attr("data-panel-parameters");

      div.prop("id", $("#style_id").val());
      div.attr("data-panel-parameters", params);
      div.prop("class", "panel " + $("#used-classes").text());

      /*if (oldParameters != params)
       {
       neuis.updateUIS(true);
       }*/

      //$("#inspector-editor").trigger("refresh");
      $.EW("getParentDialog", $("#uis-panel")).trigger("close");
   };

   var uisPanel = new UISPanel();

   uisPanel.readClasses();

<?php
/* if ($row['widgets_parameters'])
  {
  $res = stripslashes($row['widgets_parameters']);
  if ($res)
  echo "EW.setFormData('#appearance-conf',$res);";
  } */
?>

</script>