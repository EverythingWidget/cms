<?php
session_start();

include($_SESSION['ROOT_DIR'] . '/config.php');

include_once 'WidgetsManagementCore.php';

$uiStructureId = $_REQUEST['uisId'];
$widgetId = $_REQUEST['widgetId'];
$widget_type = $_REQUEST['widgetType'];
$panelId = $_REQUEST['panelId'];
$position = mysql_real_escape_string($_POST['position']);
$order = mysql_real_escape_string($_POST['order']);
$class = mysql_real_escape_string($_POST['class']);
$parameters = mysql_real_escape_string($_POST['parameters']);
$WM = new admin\WidgetsManagement();
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
      <?php
      $wm = new admin\WidgetsManagement();
      $widgets_types_list = json_decode($wm->get_widgets_types(), true);
      $widgets_types_list = $widgets_types_list["result"];
      $rowNum = 0;
      foreach ($widgets_types_list as $row)
      {
         ?>		
         <div class="text-icon" onclick="uisWidget.showWidgetControlPanel('<?php echo $row["path"] ?>')">                        
            <h4>
               <?php echo $row["title"] ?>
            </h4>
            <p>
               <?php echo $row["description"] ?>
            </p>
         </div>
         <?php
      }
      ?>
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
      this.template = "<?php echo $_REQUEST["template"] ?>";
      this.bAdd = EW.addAction("Add", this.addWidgetToPanel, {display: "none"}).addClass("btn-success");
      this.bApply = EW.addAction("Apply", $.proxy(this.applyToWidget, this), {display: "none"}).addClass("btn-success");
      //this.bCW = EW.addAction("Change Widget", this.showWidgetList, {display: "none"});
      this.bCC = EW.addAction("Cancel Changing", this.cancel, {display: "none"});
      this.setData = true;
      this.getWidgetParameters = null;
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
      }
      else
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
      $.EW("lock", $.EW("getParentDialog", $("#uis-widget-form")));
      var wp = $("#uis-widget").serializeJSON();
      if (this.getWidgetParameters)
         wp = this.getWidgetParameters.apply(null, null);
      //var param = $("#parameters").val();
      var styleId = $("#style_id").val();
      var styleClass = $("#used-classes").text();
      var widgetStyleClass = $("#style_class").val();
      //$("#add").hide();
      //$("#cancel").hide();

      /*var widgetContainer = $("<div class='widget-container col-xs-12'><div class='widget'></div></div>");
       var widget = widgetContainer.children(".widget");
       
       widget.prop("id", styleId);
       widget.attr("class", "widget " + widgetStyleClass);
       widget.attr("data-widget-parameters", wp);
       widget.attr("data-widget-type", uisWidget.widgetType);
       widgetContainer.prop("class", "widget-container " + styleClass);*/
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/create_widget', {widget_type: uisWidget.widgetType, style_class: styleClass,
         widget_style_class: widgetStyleClass, style_id: styleId, widget_parameters: wp},
      function (data) {
         var containerElement = $("#fr").contents().find("body #base-content-pane div[data-panel-id='<?php echo $panelId ?>']");
         if (containerElement.hasClass("block"))
         {
            containerElement.append(data);
         }
         else
         {
            containerElement.children(".row").append(data);
         }
         //$("#fr").contents().find("body #base-content-pane div[data-panel-id='<?php echo $panelId ?>']").append(data);
         $("#inspector-editor").trigger("refresh");
         $.EW("getParentDialog", $("#uis-widget-form")).trigger("close");
      });


      //neuis.updateUIS(true);
      //neuis.currentDialog.dispose();
   };

   UISWidget.prototype.applyToWidget = function ()
   {
      var base = this;
      $.EW("lock", $.EW("getParentDialog", $("#uis-widget-form")));
      var widget = uisForm.getEditorItem(this.widgetId);
      var wp = $("#uis-widget").serializeJSON();
      if (this.getWidgetParameters)
         wp = this.getWidgetParameters.apply(null, null);
      //var oldParameters = widget.attr("data-widget-parameters");
      //var param = $("#parameters").val();
      var styleId = $("#style_id").val();
      var styleClass = $("#used-classes").text();
      var widgetStyleClass = $("#style_class").val();
      //$("#add").hide();
      //$("#cancel").hide();*/

      /*widget.prop("id", styleId);
       widget.attr("data-widget-parameters", wp);
       widget.attr("class", "widget " + widgetStyleClass);
       widget.attr("data-widget-type", uisWidget.widgetType);
       widget.data("container").prop("class", "widget-container " + styleClass);*/

      $.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/create_widget', {widget_id: this.widgetId, widget_type: uisWidget.widgetType, style_class: styleClass,
         widget_style_class: widgetStyleClass, style_id: styleId, widget_parameters: wp},
      function (data) {
         //alert(styleClass);
         var w = $.parseHTML(data, $("#fr")[0].contentWindow.document, true);
         //w.context = $("#fr")[0].contentWindow.document;
         //console.log(w);
         //var 
         widget.parent().replaceWith(w);
         //widget.parent()[0].appendChild(w[0]);
         //w.append();
         //$("#fr").contents().find("body").find("div[data-widget-id='" + base.widgetId + "']").after(data);
         //$("#fr").contents().find("body").find("div[data-widget-id='" + base.widgetId + "']").parent().remove();
         //$("#fr").contents().find("body #base-content-pane div[data-panel-id='<?php echo $panelId ?>']").append(data);
         $.EW("getParentDialog", $("#uis-widget-form")).trigger("close");
         $("#inspector-editor").trigger("refresh");         
      });


      /*if (oldParameters != wp || oldType != uisWidget.widgetType)
       {
       neuis.updateUIS(true);
       }*/
   };


   UISWidget.prototype.showWidgetControlPanel = function (widgetType)
   {
      var self = this;
      var widget;
      self.widgetType = widgetType;
      // if widgetId exist, get the corresponding widget
      if (self.widgetId != "")
      {
         widget = uisForm.getEditorItem(self.widgetId);
         var widgetParams = (widget.attr("data-widget-parameters")) ? $.parseJSON(widget.attr("data-widget-parameters")) : {};
         self.widgetParameters = widgetParams;
      }

      self.bCC.comeOut(200);
      //$this.bCW.comeIn(300);
      $('#uis-widget-form').html("");
      $('#uis-widget-form').show();
      $("#widgets-list-form").hide();
      EW.lock($('#uis-widget-form'));
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/uis-widget-form.php', {widgetType: widgetType, template: self.template, widgetParameters: JSON.stringify(self.widgetParameters)},
      function (data) {
         $('#uis-widget-form').stop().hide();
         $('#uis-widget-form').html(data);
         // If widgetId exist, set data for widget control panel
         if (self.widgetId != "")
         {
            $("#used-classes").text(widget.data("container").prop("class"));
            $("#style_class").val(widget.prop("class"));
            // If true, set values for the fields of widget control panel form
            if (self.setData === true)
            {
               var widgetParams = (widget.attr("data-widget-parameters")) ? $.parseJSON(widget.attr("data-widget-parameters")) : {};
               EW.setFormData("#uis-widget", widgetParams);
               $("#style_class").keyup(this.setClasses);
            }
         }
         // If widgetId is empty show add button
         else
            self.bAdd.comeIn(300);
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
            }
            else
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
               widgetClasses[i] = null;
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
