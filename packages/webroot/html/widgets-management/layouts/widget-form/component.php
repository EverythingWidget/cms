<?php
session_start();

$uiStructureId = $_REQUEST['uisId'];
$widgetId = $_REQUEST['widgetId'];
$widget_type = $_REQUEST['widgetType'];
$feeder_type = $_REQUEST['feederType'];
$panelId = $_REQUEST['panelId'];
?>
<div id="widgets-list-form">
  <div class="header-pane">
    <h1 id="uis-widget-title" class="col-xs-12">
      <span>tr{Add}</span>tr{Widget}
    </h1>
  </div>
  <div id="widgets-list" class="form-content" >
    <div class="col-xs-12 mar-bot mt">
      <div class="text-icon" onclick="uisWidget.showWidgetControlPanel('<?php echo$widget_type ?>')">                        
        <h4>
          tr{Custom Widget}
        </h4>
        <p>
          tr{Create a widget with custom configuration}
        </p>
      </div>
      <?php
      $widget_feeders = EWCore::call_api('webroot/api/widgets-management/get-widget-feeders', [
                  'type' => $feeder_type
      ]);

      $widgets_types_list = $widget_feeders['data'];
      $rowNum = 0;
      $oldApp = "";
      foreach ($widgets_types_list as $row) {
        if ($oldApp != $row->module->get_app()->get_name()) {
          $oldApp = $row->module->get_app()->get_name();
          echo "<h2>$oldApp</h2>";
        }
        $prewidget_data = json_encode(["feeder" => '{ "type": "widget-feeder", "feederId": "' . $row->id . '" }']);
        ?> 
        <div class="text-icon" onclick="uisWidget.showWidgetControlPanel('<?= $widget_type ?>',<?= htmlentities($prewidget_data) ?>)">
          <h4>
            <?= $row->get_title() ?>
          </h4>
          <p>
            <?= $row->feeder_type ?>
          </p>
        </div>
        <?php
      }
      ?>
    </div>
  </div>
</div>
<div id="uis-widget-form" class="">
</div>
<div id="uis-panel-actions" class="footer-pane actions-bar action-bar-items" >
</div>
<script  type="text/javascript">
  function UISWidget() {
    this.widgetId = "<?= $widgetId ?>";
    this.widgetType = "<?= $widget_type ?>";
    this.feederType = "<?= $feeder_type ?>";
    this.template = "<?= $_REQUEST["template"] ?>";
    this.bAdd = EW.addAction("Add", $.proxy(this.addWidgetToPanel, this), {
      display: "none"
    }).addClass("btn-success");
    this.bApply = EW.addAction("Apply", $.proxy(this.applyToWidget, this), {
      display: "none"
    }).addClass("btn-success");
    //this.bCW = EW.addAction("Change Widget", this.showWidgetList, {display: "none"});
    this.bCC = EW.addAction("Cancel Changing", this.cancel, {
      display: "none"
    });
    this.setData = true;
    this.getWidgetData;
    this.widgetParameters = {};
    this.uisWidgetForm = $('#uis-widget-form');
    if (this.widgetId) {
      $("#cmd").val("edit");
      $("#uis-widget-title").html("Edit Widget");
      $("#widget-control-panel").fadeIn(300);
      $("#widgets-list-form").hide();
      this.bAdd.hide();
      this.bApply.show();
      var widget = uisForm.getEditorItem(this.widgetId);
      this.showWidgetControlPanel(widget.attr("data-widget-type"));
    } else if (!this.feederType) {
      this.showWidgetControlPanel(this.widgetType);
    }
  }

  UISWidget.prototype.autoSetData = function (flag) {
    this.setData = flag;
  };
  UISWidget.prototype.addWidgetToPanel = function () {
    var self = this;
    $.EW('lock', $.EW("getParentDialog", this.uisWidgetForm));

    var wp = $("#uis-widget").serializeJSON(true);
    if (self.getWidgetData) {
      wp = $.extend($.parseJSON(wp), self.getWidgetData.apply(null, null));
    }

    var styleId = $("#style_id").val();
    var styleClass = this.vue.containerClasses.join(' ');
    var widgetStyleClass = $("#style_class").val();

    $.post('api/webroot/widgets-management/create-widget', {
      widget_type: uisWidget.widgetType,
      style_class: styleClass,
      widget_style_class: widgetStyleClass,
      style_id: styleId,
      widget_parameters: wp
    }, function (response) {
      EW.lock($.EW("getParentDialog", self.uisWidgetForm));
      // Add widget data to the widget-data script tag
      if (response.data["widget_data"])
        uisForm.setWidgetData(response.data["widget_id"], response.data["widget_data"]);
      var containerElement = $("#fr").contents().find("body #base-content-pane div[data-panel-id='<?php echo $panelId ?>']");
      if (containerElement.hasClass("block")) {
        uisForm.addWidget(response.data["widget_html"], containerElement[0]);
      } else {
        uisForm.addWidget(response.data["widget_html"], containerElement[0]);
      }

      $("#inspector-editor").trigger("refresh");
      $.EW("getParentDialog", self.uisWidgetForm).trigger("close");
    }, "json");
  };
  UISWidget.prototype.applyToWidget = function () {
    var self = this;
    $.EW("lock", $.EW("getParentDialog", this.uisWidgetForm));
    var widget = uisForm.getEditorItem(this.widgetId);
    //console.log($("#uis-widget").serializeJSON());
    var wp = JSON.parse($("#uis-widget").serializeJSON());
    if (self.getWidgetData) {
      wp = $.extend($.parseJSON(wp), self.getWidgetData.apply(null, null));
    }
    //console.log(wp);
    //alert(wp);
    var styleId = $("#style_id").val();
    var styleClass = this.vue.containerClasses.join(' ');
    var widgetStyleClass = $("#style_class").val();
    console.log('widget parameters:', wp);
    $.ajax({
      type: 'POST',
      url: 'api/webroot/widgets-management/create-widget',
      data: {
        widget_id: this.widgetId,
        widget_type: uisWidget.widgetType,
        style_class: styleClass,
        widget_style_class: widgetStyleClass,
        style_id: styleId,
        widget_parameters: JSON.stringify(wp)
      },
      success: function (response) {
        EW.lock($.EW("getParentDialog", self.uisWidgetForm));
        // Remove the old widget script
        uisForm.getEditor().find("head #" + self.widgetId).remove();
        // Add widget data to the widget-data script tag
        if (response.data["widget_data"]) {
          uisForm.setWidgetData(response.data["widget_id"], response.data["widget_data"]);
        }

        uisForm.replaceWidget(response.data["widget_html"], widget.parent()[0]);
        $.EW("getParentDialog", self.uisWidgetForm).trigger("close");
        $("#inspector-editor").trigger("refresh");
      }
    });
  };
  UISWidget.prototype.showWidgetControlPanel = function (widgetType, widgetParams) {
    var self = this;
    var widget = $();
    self.widgetType = widgetType;
    widgetParams = widgetParams || {};
    // if widgetId exist, get the corresponding widget
    if (self.widgetId != "") {
      widget = uisForm.getEditorItem(self.widgetId);
      //widgetParams = (widget.attr("data-widget-parameters")) ? $.parseJSON(widget.attr("data-widget-parameters")) : {};
      widgetParams = uisForm.editor.ew_widget_data[self.widgetId];
      self.widgetParameters = widgetParams;
    }

    self.bCC.comeOut(200);
    this.uisWidgetForm.html("").show();
    $("#widgets-list-form").hide();
    EW.lock(this.uisWidgetForm);
    $.post('html/webroot/widgets-management/layouts/widget-form/form.php', {
      widgetType: widgetType,
      template: self.template,
      widgetParameters: JSON.stringify(self.widgetParameters)
    }, function (data) {
      self.uisWidgetForm.stop().hide();
      self.uisWidgetForm.html(data);
      self.usedClassElement = $("#used-classes");
      // If widgetId exist, set data for widget control panel

      var containerClasses = [];
      var styleClasses = [];
      $("#style_id").val(widget.prop("id")).change();
      if (self.widgetId != "") {
        containerClasses = widget.data("container").prop("class").replace('widget-container', '').split(' ').filter(Boolean);
        styleClasses = widget.prop("class").replace('widget', '').split(' ').filter(Boolean);
        // If true, set values for the fields of widget control panel form
        if (self.setData === true) {

        }
      }
      // If widgetId is empty show add button
      else {
        self.bAdd.comeIn(300);
      }

      if (widgetParams) {
        setTimeout(function () {
          console.log('widget data: ', widgetParams);
          EW.setFormData("#uis-widget", widgetParams);
        });
      }

      self.vue = new Vue({
        el: '#widget-control-panel',
        data: {
          styleClasses: styleClasses,
          availableClasses: <?= json_encode(EWCore::parse_css_clean(EW_PACKAGES_DIR . '/rm/public/' . $_REQUEST["template"] . '/template.css', 'widget')) ?>,
          containerClasses: populateLayout(containerClasses),
          widgetClasses: [],
          tempClasses: []
        },
        computed: {
          appliedClasses: function () {
            return this.styleClasses.concat(this.widgetClasses);
          },
          styleClassesText: {
            set: function (value) {
              this.tempClasses = value;
            },
            get: function () {
              var all = this.appliedClasses.filter(function (item, pos, source) {
                return item && source.indexOf(item) === pos;
              });

              return all.length ? all.join(' ') + ' ' : '';
            }
          }
        },
        methods: {
          updateStyleClasses: function () {
            this.styleClasses = this.tempClasses.split(' ');
          },
          isSelected: function (item) {
            return this.appliedClasses.indexOf(item) !== -1;
          },
          toggleClass: function (item) {
            var index = this.appliedClasses.indexOf(item);

            if (index !== -1) {
              this.styleClasses.splice(index, 1);
            } else {
              this.styleClasses.push(item);
            }

            this.$nextTick(function () {
              $("#style_class").change();
            });
          }
        }
      });

      $("#size-layout").find("input").change(function (event) {
        self.vue.containerClasses = readLayoutClasses();
      });

      self.vue.containerClasses = readLayoutClasses();

      self.uisWidgetForm.fadeIn(300);
      $("#style_class").change();
    });
  };

  function populateLayout(classes) {
    var layoutClasses = [];

    var $sizeAndLayout = $("#size-layout");
    $.each($sizeAndLayout.find('input:radio, input:checkbox'), function (k, field) {
      var $v = $(field), value = $v.val();
      $.each(classes, function (i, className) {
        if (value === className) {
          $v.click();
          $v.prop("checked", true);
          layoutClasses.push(classes.splice(i, 1)[0]);
        }
      });
    });

    $.each($sizeAndLayout.find('input[data-slider]'), function (k, field) {
      $.each(classes, function (i, className) {
        if (!className)
          return;

        var sub = className.match(/(\D+)(\d*)/);
        if (sub && $(field).attr("name") === sub[1]) {
          $(field).val(sub[2]).change();
          layoutClasses.push(classes.splice(i, 1)[0]);
        }
      });
    });

    return layoutClasses.filter(Boolean);
  }

  function readLayoutClasses() {
    var layoutClasses = [];

    var $sizeAndLayout = $("#size-layout");
    $.each($sizeAndLayout.find("input[data-slider]:not(:disabled)"), function (k, v) {
      if (v.value) {
        layoutClasses.push(v.name + v.value);
      }
    });

    $.each($sizeAndLayout.find("input:radio:checked:not(:disabled), input:checkbox:checked:not(:disabled)"), function (k, v) {
      layoutClasses.push($(v).val());
    });

    return layoutClasses.filter(Boolean);
  }

  UISWidget.prototype.cancel = function () {
    this.uisWidgetForm.stop().fadeIn(300);
    $("#widgets-list-form").hide();
    uisWidget.bCC.comeOut(200);
    uisWidget.bAdd.comeIn(300);
    //uisWidget.bCW.comeIn(300);
  };

  UISWidget.prototype.showWidgetList = function () {
    uisWidget.bAdd.comeOut(200);
    //uisWidget.bCW.comeOut(200);
    uisWidget.bCC.comeIn(300);
    this.uisWidgetForm.hide();
    $("#widgets-list-form").fadeIn(300);
  };

  var uisWidget = new UISWidget();

</script>
<?php
// Load widget control panel scripts
if (function_exists("get_script"))
  echo get_script();
