<?php
session_start();
$uiStructureId = $_REQUEST['uisId'];
$panel_id = $_REQUEST["id"];
$container_id = $_REQUEST["containerId"];
$name = $_REQUEST["name"];
?>
<div id="uis-block-form">
  <div class="header-pane thin">
    <h1 id="uis-block-title">
      Add Block
    </h1>
  </div>
  <div class="form-content">

    <form id="block-form" >
      <div class="block-row mt">
        <input type="hidden" name="cmd" id="cmd" >
        <div class="col-xs-12">
          <input data-label="ID" id="style_id" name="style_id" class="text-field" value="<?php echo $row["style_id"] ?>">
        </div>
      </div>
      <div class="block-row">
        <div class="col-xs-12">
          <input data-label="Class" id="style_class" name="style_class" class="text-field" >
          <label class="small" id="used-classes"><?php echo $row["style_class"] ?></label>
        </div>
      </div>
      <div class="block-row">
        <div class="col-xs-12" >                     
          <h3>Applied classes</h3>                     
          <div class="block-row options-panel" id="panel-classes" data-toggle="buttons">
          </div>
        </div>
      </div>
      <div class="block-row">
        <div class="col-xs-12"  >
          <h3>Classes</h3>
          <div class="block-row options-panel" id="available-classes" data-toggle="buttons">
            <?php
            if ($_REQUEST["template"]) {
              $templates = json_decode(EWCore::parse_css(EW_PACKAGES_DIR . '/rm/public/' . $_REQUEST["template"] . '/template.css', "block"), true);
              foreach ($templates as $t) {
                ?>
                <label ><?php echo $t ?></label>
                <?php
              }
            }
            ?>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>


<div id="uis-panel-actions" class="footer-pane actions-bar action-bar-items" >

</div>
<script  type="text/javascript">

  var BlockForm = (function () {
    function BlockForm() {
      var self = this;
      this.bAdd = EW.addAction("tr{Add}", $.proxy(this.addBlock, this), {
        display: "none"
      },
        "uis-panel-actions");
      this.bEdit = EW.addAction("tr{Save}", $.proxy(this.updateBlock, this), {
        display: "none"
      },
        "uis-panel-actions");
      $("#appearance-conf input:radio").change(function () {
        if ($(this).val() == "no")
        {
          $("#title-text").prop("disabled", true);
        } else
        {
          $("#title-text").prop("disabled", false);
        }
      });

      this.panelId = "<?php echo $panel_id ?>";
      this.blockHTML = $("<div data-block='true'></div>");
      $("#used-classes").text(this.blockHTML.prop("class"));
      if (this.panelId)
      {
        var panel = $("#fr").contents().find("body #base-content-pane div[data-panel-id='" + this.panelId + "']");
        var blockParams = (panel.attr("data-panel-parameters")) ? $.parseJSON(panel.attr("data-panel-parameters")) : {};
        EW.setFormData("#appearance-conf", blockParams);
        $("#style_id").val(panel.prop("id")).change();
        $("#uis-block-title").html("Edit Block");
        $("#used-classes").text(panel.prop("class"));
        this.bAdd.comeOut(200);
        this.bEdit.comeIn(300);
      } else
      {
        this.bAdd.comeIn(300);
        this.bEdit.comeOut(200);
      }
      /*$("#block-form").on("refresh", function(e, data) {
       
       if ($("#block-form #id").val())
       {
       self.bAdd.comeOut(300);
       self.bSave.comeIn(300);
       }
       else
       {
       self.bAdd.comeIn(300);
       self.bSave.comeOut(300);
       }
       });*/
      $("#style_class").keyup(this.setClasses);
    }


    BlockForm.prototype.readClasses = function () {
      var $this = this;
      var $availableClasses = $("#available-classes");
      var $blockClasses = $("#panel-classes");
      var classes = $("#used-classes").text();
      classes = classes.replace('block', '');
      classes = classes.replace('row', '');
      classes = classes.split(' ');
      
      $.each($availableClasses.find("label"), function (k, classBtn) {
        classBtn = $(classBtn);
        
        var a = $("<input type='checkbox'>");
        a.val(classBtn.text().substring(7));
        classBtn.text($(classBtn).text().substring(7));
        a.change(function (event) {
          if ($(this).is(":checked")) {
            classBtn.removeClass("btn-default").addClass("btn-success");
            $blockClasses.append(classBtn);
          } else
          {
            classBtn.removeClass("btn-success").addClass("btn-default");
            $availableClasses.append(classBtn);
          }

          $this.setClasses();
          //event.preventDefault()
        });
        classBtn.addClass("btn btn-default btn-xs");
        classBtn.prepend(a);
        $.each(classes, function (i, c) {
          if (a.val() === (c))
          {
            classBtn.removeClass("btn-default");
            classBtn.addClass("btn-success active");
            a.prop('checked', true);
            $blockClasses.append(classBtn);
            classes[i] = null;
          }
        });
      });
      
      $("#style_class").val(classes.join(' ').trim()).change();
      this.setClasses();
    };

    BlockForm.prototype.setClasses = function () {
      var styleClass = $("#style_class").val() + " ";
      $("#used-classes").text("");

      $.each($("#panel-classes").find("input"), function (k, v) {
        styleClass += ($(v).val() + " ");
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

    BlockForm.prototype.addBlock = function (pId) {
      var params = $("#block-configuration").serializeJSON(true);
      this.blockHTML.find("div:not(.panel)").addClass("panel");
      this.blockHTML.prop("id", $("#style_id").val());
      this.blockHTML.attr("data-panel-parameters", params);
      this.blockHTML.attr("data-block-name", "<?php echo $block_class_name; ?>");
      this.blockHTML.prop("class", "block row " + $("#used-classes").text());

      if ($("#fr").contents().find("body [base-content-pane]").length) {
        $("#fr").contents().find("body [base-content-pane]").append(this.blockHTML);
      } else {
        $("#fr").contents().find("body #base-content-pane").append(this.blockHTML);
      }
      $("#inspector-editor").trigger("refresh");

      $.EW("getParentDialog", $("#block-form")).trigger("close");
    };

    BlockForm.prototype.updateBlock = function (pId) {
      var params = $("#block-configuration").serializeJSON(true);
      var div = $("#fr").contents().find("body #base-content-pane div[data-panel-id='" + this.panelId + "']");
      var oldParameters = div.attr("data-panel-parameters");
      div.attr("id", $("#style_id").val());
      div.attr("data-panel-parameters", params);

      div.prop("class", "block row " + $("#used-classes").text());
      $("#inspector-editor").trigger("refresh");

      $.EW("getParentDialog", $("#block-form")).trigger("close");
    };
    return new BlockForm();
  })();
  BlockForm.readClasses();
</script>