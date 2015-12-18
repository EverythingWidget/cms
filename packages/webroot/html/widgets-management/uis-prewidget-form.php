<?php
session_start();

//include($_SESSION['ROOT_DIR'] . '/config.php');
//include_once 'WidgetsManagementCore.php';

$uiStructureId = $_REQUEST['uisId'];
$widgetId = $_REQUEST['widgetId'];
$widget_type = $_REQUEST['widgetType'];
$feeder_type = $_REQUEST['feederType'];
$panelId = $_REQUEST['panelId'];
/* $position = mysql_real_escape_string($_POST['position']);
  $order = mysql_real_escape_string($_POST['order']);
  $class = mysql_real_escape_string($_POST['class']);
  $parameters = mysql_real_escape_string($_POST['parameters']); */
//$WM = new admin\WidgetsManagement();
/* if ($_REQUEST["widgetId"])
  {
  //echo $_REQUEST["widgetId"];
  $widget_info = json_decode($WM->get_widget($_REQUEST["widgetId"]), TRUE);
  $widget_type = $widget_info["widget_type"];
  } */
?>
<div id="widgets-list-form">
   <div class="header-pane   row">
      <h1 id="uis-widget-title" class="col-xs-12">
         <span>tr{Add}</span>tr{Widget}
      </h1>
   </div>
   <div id="widgets-list" class="form-content row" >
      <div class="col-xs-12 mar-bot">
         <div class="text-icon" onclick="uisWidget.showWidgetControlPanel('<?php echo$widget_type ?>')">                        
            <h4>
               tr{Custom Widget}
            </h4>
            <p>
               tr{Create a widget with custom configuration}
            </p>
         </div>
         <h4>tr{App's Widgets}</h4>
         <?php
         //$wm = new admin\WidgetsManagement();
         //$widgets_types_list = json_decode($wm->get_widgets_types(), true);
         $widgets_types_list = json_decode(webroot\WidgetsManagement::get_widget_feeders($feeder_type), true);
         $widgets_types_list = $widgets_types_list["result"];
         $rowNum = 0;
         $oldApp = "";
         foreach ($widgets_types_list as $row)
         {
            if ($oldApp != $row["app"])
            {
               $oldApp = $row["app"];
               echo "<h2>$oldApp</h2>";
            }
            $prewidget_data = json_encode(["feeder" => "{\"type\":\"widget-feeder\",\"feederType\":\"$widget_type\", \"feederApp\": \"$oldApp\", \"feederName\": \"{$row["name"]}\"}"]);
            ?> 
            <div class="text-icon" onclick="uisWidget.showWidgetControlPanel('<?php echo $widget_type ?>',<?php echo htmlentities($prewidget_data) ?>)">
               <?php
               echo '<h4>' . $row["name"] . '</h4>'
               . '<p>' . $row["type"] . '</p>';
               ?>
            </div>
            <?php
         }
         ?>
      </div>
   </div>
</div>
<div id="uis-widget-form" class="">
</div>
<div id="uis-panel-actions" class="footer-pane row actions-bar action-bar-items" >
</div>
<script  type="text/javascript">

   function UISWidget()
   {
      this.widgetId = "<?php echo $widgetId ?>";
      this.widgetType = "<?php echo $widget_type ?>";
      this.feederType = "<?php echo $feeder_type ?>";
      this.template = "<?php echo $_REQUEST["template"] ?>";
      this.bAdd = EW.addAction("Add", $.proxy(this.addWidgetToPanel, this), {display: "none"}).addClass("btn-success");
      this.bApply = EW.addAction("Apply", $.proxy(this.applyToWidget, this), {display: "none"}).addClass("btn-success");
      //this.bCW = EW.addAction("Change Widget", this.showWidgetList, {display: "none"});
      this.bCC = EW.addAction("Cancel Changing", this.cancel, {display: "none"});
      this.setData = true;
      this.getWidgetData;
      this.widgetParameters = {};

      if (this.widgetId)
      {
         $("#cmd").val("edit");
         $("#uis-widget-title").html("Edit Widget");
         $("#widget-control-panel").fadeIn(300);
         $("#widgets-list-form").hide();
         this.bAdd.comeOut(200);
         this.bApply.comeIn(300);

         var widget = uisForm.getEditorItem(this.widgetId);
         this.showWidgetControlPanel(widget.attr("data-widget-type"));
      } else if (!this.feederType)
      {
         this.showWidgetControlPanel(this.widgetType);
      }
   }

   UISWidget.prototype.autoSetData = function (flag)
   {
      this.setData = flag;
   };

   UISWidget.prototype.addWidgetToPanel = function ()
   {
      var self = this;
      $.EW("lock", $.EW("getParentDialog", $("#uis-widget-form")));
      var wp = JSON$("#uis-widget").serializeJSON();
      if (self.getWidgetData)
         wp = JSON.stringify($.extend($.parseJSON(wp), self.getWidgetData.apply(null, null)));
      //var param = $("#parameters").val();
      var styleId = $("#style_id").val();
      var styleClass = $("#used-classes").text();
      var widgetStyleClass = $("#style_class").val();

      $.post('<?php echo EW_ROOT_URL; ?>~webroot/api/widgets-management/create-widget', {widget_type: uisWidget.widgetType, style_class: styleClass,
         widget_style_class: widgetStyleClass, style_id: styleId, widget_parameters: wp},
              function (data) {
                 EW.lock($.EW("getParentDialog", $("#uis-widget-form")));

                 // Add widget data to the widget-data script tag
                 if (data["widget_data"])
                    uisForm.setWidgetData(data["widget_id"], data["widget_data"]);

                 var containerElement = $("#fr").contents().find("body #base-content-pane div[data-panel-id='<?php echo $panelId ?>']");
                 if (containerElement.hasClass("block"))
                 {
                    uisForm.addWidget(data["widget_html"], containerElement[0]);
                 } else
                 {
                    uisForm.addWidget(data["widget_html"], containerElement.children(".row")[0]);
                 }

                 $("#inspector-editor").trigger("refresh");
                 $.EW("getParentDialog", $("#uis-widget-form")).trigger("close");
              }, "json");
   };

   UISWidget.prototype.applyToWidget = function ()
   {
      var self = this;
      $.EW("lock", $.EW("getParentDialog", $("#uis-widget-form")));
      var widget = uisForm.getEditorItem(this.widgetId);
      var wp = JSON.parse($("#uis-widget").serializeJSON());
      if (self.getWidgetData) {
         wp = $.extend($.parseJSON(wp), self.getWidgetData.apply(null, null));
      }
      console.log(wp);
      //alert(wp);
      var styleId = $("#style_id").val();
      var styleClass = $("#used-classes").text();
      var widgetStyleClass = $("#style_class").val();

      $.post('<?php echo EW_ROOT_URL; ?>~webroot/api/widgets-management/create-widget', {
         widget_id: this.widgetId,
         widget_type: uisWidget.widgetType,
         style_class: styleClass,
         widget_style_class: widgetStyleClass,
         style_id: styleId,
         widget_parameters: wp
      }, function (data) {
         EW.lock($.EW("getParentDialog", $("#uis-widget-form")));
         // Remove the old widget script
         uisForm.getEditor().find("head #" + self.widgetId).remove();

         // Add widget data to the widget-data script tag
         if (data["widget_data"]) {
            uisForm.setWidgetData(data["widget_id"], data["widget_data"]);
         }

         uisForm.replaceWidget(data["widget_html"], widget.parent()[0]);

         $.EW("getParentDialog", $("#uis-widget-form")).trigger("close");
         $("#inspector-editor").trigger("refresh");
      }, "json");
   };


   UISWidget.prototype.showWidgetControlPanel = function (widgetType, widgetParams)
   {
      var self = this;
      var widget;
      self.widgetType = widgetType;
      // if widgetId exist, get the corresponding widget
      if (self.widgetId != "")
      {
         widget = uisForm.getEditorItem(self.widgetId);
         //widgetParams = (widget.attr("data-widget-parameters")) ? $.parseJSON(widget.attr("data-widget-parameters")) : {};
         widgetParams = uisForm.editor.EW.widget_data[self.widgetId];
         self.widgetParameters = widgetParams;
      }

      self.bCC.comeOut(200);
      //$this.bCW.comeIn(300);
      $('#uis-widget-form').html("");
      $('#uis-widget-form').show();
      $("#widgets-list-form").hide();
      EW.lock($('#uis-widget-form'));
      $.post('<?php echo EW_ROOT_URL; ?>~webroot/html/widgets-management/uis-widget-form.php', {widgetType: widgetType, template: self.template, widgetParameters: JSON.stringify(self.widgetParameters)},
              function (data) {
                 $('#uis-widget-form').stop().hide();
                 $('#uis-widget-form').html(data);
                 // If widgetId exist, set data for widget control panel
                 if (self.widgetId != "")
                 {
                    $("#used-classes").text(widget.data("container").prop("class"));
                    $("#style_class").val(widget.prop("class"));
                    $("#style_id").val(widget.prop("id")).change();
                    // If true, set values for the fields of widget control panel form
                    if (self.setData === true)
                    {
                       //widgetParams = (widget.attr("data-widget-parameters")) ? $.parseJSON(widget.attr("data-widget-parameters")) : {};
                       EW.setFormData("#uis-widget", widgetParams);
                       $("#style_class").keyup(this.setClasses);
                    }
                 }
                 // If widgetId is empty show add button
                 else
                    self.bAdd.comeIn(300);

                 if (widgetParams)
                    EW.setFormData("#uis-widget", widgetParams);
                 $('#uis-widget-form').fadeIn(300);

                 self.readClasses();
              });
   };

   UISWidget.prototype.cancel = function ()
   {
      $("#uis-widget-form").stop();
      $("#uis-widget-form").fadeIn(300);
      $("#widgets-list-form").hide();
      uisWidget.bCC.comeOut(200);
      uisWidget.bAdd.comeIn(300);
      //uisWidget.bCW.comeIn(300);
   };

   UISWidget.prototype.showWidgetList = function ()
   {
      uisWidget.bAdd.comeOut(200);
      //uisWidget.bCW.comeOut(200);
      uisWidget.bCC.comeIn(300);
      $("#uis-widget-form").hide();
      $("#widgets-list-form").fadeIn(300);

   };

   UISWidget.prototype.readClasses = function ()
   {
      var widgetClasses = ($("#style_class").val()) ? $("#style_class").val() : "";
      widgetClasses = widgetClasses.split(" ");
      var classes = $("#used-classes").text();
      classes = classes.split(" ");
      $.each($("#available-classes").find("label"), function (k, classBtn) {
         var a = $("<input type='checkbox'>");
         classBtn = $(classBtn);
         a.val(classBtn.text().substring(8));
         classBtn.text(classBtn.text().substring(8));

         a.change(function (event) {
            if ($(this).is(":checked"))
            {
               classBtn.removeClass("btn-default");
               classBtn.addClass("btn-success");
               $("#widget-classes").append($(classBtn));
            } else
            {
               classBtn.removeClass("btn-success");
               classBtn.addClass("btn-default");
               $("#available-classes").append($(classBtn));
            }
            uisWidget.setClasses();
            //event.preventDefault()
         });

         classBtn.prepend(a);
         classBtn.addClass("btn btn-default btn-xs");
         $.each(widgetClasses, function (i, c) {
            if (a.val() === (c))
            {
               classBtn.removeClass("btn-default");
               classBtn.addClass("btn-success active");
               a.prop('checked', true);
               $("#widget-classes").append($(classBtn));
               widgetClasses.slice(i, 1);
            }
         });
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
            var sub = c.match(/(\D+)(\d*)/);
            //alert(sub[1]+" "+$(v).attr("name")+" "+sub[2]);
            if (sub && $(v).attr("name") === sub[1])
            {
               //alert(sub[2]);
               $(v).val(sub[2]).change();
            }
         });
      });
      $("#size-layout input:radio,#size-layout input:checkbox,input[data-slider]").change(function (event) {
         uisWidget.setClasses();
      });
      uisWidget.setClasses();
   };

   UISWidget.prototype.setClasses = function ()
   {
      $("#used-classes").text("");
      $("#style_class").text("");
      var styleClass = "";

      $.each($("#widget-classes").find("input"), function (k, v) {
         styleClass += ($(v).val() + " ");
      });

      $("#style_class").val(styleClass).change();
      $.each($("#size-layout input[data-slider]:not(:disabled)"), function (k, v) {
         $("#used-classes").append($(v).attr("name") + $(v).val() + " ");
      });
      $.each($("#size-layout input:radio:checked:not(:disabled),#size-layout input:checkbox:checked:not(:disabled)"), function (k, v) {

         $("#used-classes").append($(v).val() + " ");
      });
      var classes = $("#used-classes").text().split(" ");
      var html = "";
      $.each(classes, function (i, v) {
         if (v)
            html += "<span class='tag label label-default'>" + v + " </span>";
      });
      $("#used-classes").html(html);
   };

   var uisWidget = new UISWidget();



</script>
<?php
// Load widget control panel scripts
if (function_exists("get_script"))
   echo get_script();
