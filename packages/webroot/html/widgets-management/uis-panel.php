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
<div class="header-pane tabs-bar row">
  <h1 id="uis-panel-title" class="col-xs-12">
    <span>tr{Add}</span>tr{Panel}
  </h1>
  <ul class="nav nav-pills">
    <li class="active"><a href="#properties" data-toggle="tab">Properties</a></li>
    <!--<li><a href="#appearance" data-toggle="tab">Appearance</a></li>-->

    <li><a href='#size-layout' data-toggle='tab'>Size & Layout</a></li>
  </ul>
</div>
<div class="form-content  tabs-bar row">
  <div class="tab-content">
    <div class="tab-pane active" id="properties">
      <form id="uis-panel" >
        <div class="block-row">
          <input type="hidden" name="cmd" id="cmd" >

          <div class="col-xs-12">
            <input data-label="ID" id="style_id" name="style_id" class="text-field" value="<?php echo $row["style_id"] ?>">
          </div>
        </div>
        <div class="block-row">
          <div class="col-xs-12">
            <input id="style_class" data-label="Class" name="style_class" class="text-field" >
            <label class="small" id="used-classes"><?php echo $row["style_class"] ?></label>
          </div>
        </div>
        <div class="block-row">
          <div class="col-xs-12" >
            <h3>Used</h3>
            <div class="col-xs-12 options-panel" id="panel-classes" data-toggle="buttons">
            </div>
          </div>
        </div>
        <div class="block-row">
          <div class="col-xs-12"  >
            <h3>Classes</h3>
            <div class="col-xs-12 options-panel" id="available-classes" data-toggle="buttons">
              <?php
              $templates = json_decode(EWCore::parse_css(EW_PACKAGES_DIR . '/rm/public/' . $_REQUEST["template"] . '/template.css', "panel"), true);

              foreach ($templates as $t) {
                ?>
                <label><?php echo $t ?></label>
                <?php
              }
              ?>
            </div>
          </div>
        </div>
      </form>
    </div>
    <!--<div class="tab-pane" id="appearance">
      <form id="appearance-conf" >
    <?php include 'uis-panel-appearance.php'; ?>
      </form>
    </div>-->

    <div class="tab-pane" id="size-layout">
      <?php include 'uis-widget-size-layout.php'; ?>
    </div>

  </div>

</div>
<div id="uis-panel-actions" class="footer-pane row actions-bar action-bar-items" >

</div>
<script  type="text/javascript">


  function UISPanel() {
    this.bAdd = EW.addAction("tr{Add}", $.proxy(this.addPanel, this), {
      display: "none"
    },
      "uis-panel-actions");
    this.bEdit = EW.addAction("tr{Save}", $.proxy(this.updatePanel, this), {
      display: "none"
    },
      "uis-panel-actions");
    $("#appearance-conf input[name='title']").change(function () {

      if ($(this).val() == "")
      {
        $("#title-text").prop("disabled", true);
      } else
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
    } else
    {
      this.bAdd.comeIn(300);
      this.bEdit.comeOut(200);
    }
    $("#style_class").keyup(this.setClasses);
  }


  UISPanel.prototype.readClasses = function () {
    var self = this;
    var $sizeAndLayout = $("#size-layout");
    var classes = $("#used-classes").text();
    classes = classes.replace('panel', '');
    classes = classes.split(' ');

    $.each($("#available-classes").find("label"), function (k, classBtn) {
      var flag = $("<input type='checkbox'>");
      classBtn = $(classBtn);
      flag.val(classBtn.text().substring(7));
      classBtn.text(classBtn.text().substring(7));

      flag.change(function (event) {
        if ($(this).is(":checked"))
        {
          classBtn.removeClass("btn-default").addClass("btn-success");
          $("#panel-classes").append(classBtn);
        } else
        {
          classBtn.removeClass("btn-success").addClass("btn-default");
          $("#available-classes").append(classBtn);
        }
        self.setClasses();
      });

      classBtn.addClass("btn btn-default btn-xs");
      classBtn.prepend(flag);

      var value = flag.val();
      $.each(classes, function (i, c) {
        if (value === c) {
          classBtn.removeClass("btn-default").addClass("btn-success active");
          flag.prop('checked', true);
          $("#panel-classes").append(classBtn);
          classes.splice(i, 1);
        }
      });
    });

    $.each($sizeAndLayout.find("input:radio,input:checkbox"), function (k, v) {
      var $v = $(v), value = $v.val();
      $.each(classes, function (i, c) {
        if (value === c && !$v.is(":checked")) {
          $v.click();
          $v.prop("checked", true);
          classes.splice(i, 1);
        }
      });
    });

    $.each($sizeAndLayout.find("input[data-slider]"), function (k, v) {
      $.each(classes, function (i, c) {
        var sub = c.match(/(\D+)(\d*)/);
        if (sub && $(v).attr("name") === sub[1])
        {
          $(v).val(sub[2]).change();
        }
      });
    });
    
    $("#size-layout input:radio,#size-layout input:checkbox,input[data-slider]").change(function (event) {
      self.setClasses();
    });

    $("#style_class").val(classes.join(' ').trim()).change();
    self.setClasses();
  };

  UISPanel.prototype.setClasses = function () {
    var styleClass = $("#style_class").val() + " ";
    $("#used-classes").text("");
    $.each($("#panel-classes").find("input"), function (k, v) {
      styleClass += ($(v).val() + " ");
    });

    $.each($("#size-layout input[data-slider]:not(:disabled)"), function (k, v) {
      if (parseInt(v.value)) {
        styleClass += v.name + v.value + " ";
      }
    });

    $.each($("#size-layout input:radio:checked:not(:disabled),#size-layout input:checkbox:checked:not(:disabled)"), function (k, v) {
      styleClass += $(v).val() + " ";
    });
    $("#used-classes").text(styleClass);
    var classes = styleClass.split(" ");
    var html = "";
    $.each(classes, function (i, v) {
      if (v)
        html += "<span class='tag label label-default'>" + v + " </span>";
    });
    $("#used-classes").html(html);
  };
  // Create and add new div to the page
  UISPanel.prototype.addPanel = function (pId) {
    var self = this;
    //EW.lock(neuis.currentDialog, "...");
    var params = $("#appearance-conf").serializeJSON();
    var div = $("<div data-panel='true'><div class='row'></div></div>");
    div.prop("id", $("#style_id").val());
    div.attr("data-panel-parameters", params);
    div.prop("class", "panel " + $("#used-classes").text());
    //if (uisPanel.containerId != 0)
    var containerElement = $("#fr").contents().find("body #base-content-pane div[data-panel-id='" + this.containerId + "']");
    if (containerElement.hasClass("block"))
    {
      containerElement.append(div);
    } else if (!self.containerId)
    {
      var block = $("<div></div>");
      block.prop("id", $("#style_id").val());
      block.attr("data-panel-parameters", params);
      block.prop("class", "panel row " + $("#used-classes").text());
      $("#fr").contents().find("body #base-content-pane").append(block);
    } else
    {
      containerElement.children(".row").append(div);
    }
    //else*/
    //$("#fr").contents().find("body #base-content-pane").append(div);
    $("#inspector-editor").trigger("refresh");
    $.EW("getParentDialog", $("#uis-panel")).trigger("close");
  };
  UISPanel.prototype.updatePanel = function (pId) {
    var params = $("#appearance-conf").serializeJSON();
    var div = $("#fr").contents().find("body #base-content-pane div[data-panel-id='" + this.panelId + "']");
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

  var uisPanel;
  $(document).ready(function () {
    uisPanel = new UISPanel();
    uisPanel.readClasses();
  });
</script>