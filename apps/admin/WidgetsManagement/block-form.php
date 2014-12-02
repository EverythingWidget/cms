<?php
session_start();
$uiStructureId = $_REQUEST['uisId'];
$panel_id = $_REQUEST["id"];
$container_id = $_REQUEST["containerId"];
$name = $_REQUEST["name"];
?>
<div id="uis-block-form">
   <div class="header-pane   row">
      <h1 id="uis-block-title" class="col-xs-12">
         Add Block
      </h1>
   </div>
   <div class="form-content row">
      <div class="col-xs-12">
         <form id="block-form" >
            <div class="row">
               <input type="hidden" name="cmd" id="cmd" >
               <div class="col-xs-12">
                  <input data-label="ID" id="style_id" name="style_id" class="text-field" value="<?php echo $row["style_id"] ?>">
               </div>
            </div>
            <div class="row">
               <div class="col-xs-12">
                  <input data-label="Class" id="style_class" name="style_class" class="text-field" >
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
                        global $EW;
                        $templates = json_decode(EWCore::parse_css($_REQUEST["template"] . '/template.css', "block"), true);
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
   </div>
</div>


<div id="uis-panel-actions" class="footer-pane row actions-bar action-bar-items" >

</div>
<script  type="text/javascript">

   var BlockForm = (function () {
      function BlockForm()
      {
         var self = this;
         this.bAdd = EW.addAction("tr{Add}", $.proxy(this.addBlock, this), {display: "none"}, "uis-panel-actions");
         this.bEdit = EW.addAction("tr{Save}", $.proxy(this.updateBlock, this), {display: "none"}, "uis-panel-actions");
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
         //this.containerId = "<?php echo $container_id ?>";
         //this.blockHTML = $("<?php echo str_replace("\"", "'", trim(preg_replace('/\s+/', ' ', $block_html))) ?>");
         this.blockHTML = $("<div></div>");
         $("#used-classes").text(this.blockHTML.prop("class"));
         if (this.panelId)
         {
            var panel = $("#fr").contents().find("body #base-content-pane div[data-panel-id='" + this.panelId + "']");
            EW.setFormData("#appearance-conf", $.parseJSON(panel.attr("data-panel-parameters")));
            $("#uis-block-title").html("Edit Block");
            $("#used-classes").text(panel.prop("class"));
            this.bAdd.comeOut(200);
            this.bEdit.comeIn(300);
         }
         else
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


      BlockForm.prototype.readClasses = function ()
      {
         var $this = this;
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
               $this.setClasses();
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

         this.setClasses();
      };

      BlockForm.prototype.setClasses = function ()
      {
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

      BlockForm.prototype.addBlock = function (pId)
      {
         var params = $("#block-configuration").serializeJSON();
         this.blockHTML.find("div:not(.panel)").addClass("panel");
         this.blockHTML.prop("id", $("#style_id").val());
         this.blockHTML.attr("data-panel-parameters", params);
         this.blockHTML.attr("data-block-name", "<?php echo $block_class_name; ?>");
         this.blockHTML.prop("class", "panel block row " + $("#used-classes").text());

         $("#fr").contents().find("body #base-content-pane").append(this.blockHTML);
         $("#inspector-editor").trigger("refresh");

         $.EW("getParentDialog", $("#block-form")).trigger("close");
      };

      BlockForm.prototype.updateBlock = function (pId)
      {
         var params = $("#block-configuration").serializeJSON();
         var div = $("#fr").contents().find("body #base-content-pane div[data-panel-id='" + this.panelId + "']");
         var oldParameters = div.attr("data-panel-parameters");
         div.prop("id", $("#style_id").val());
         div.attr("data-panel-parameters", params);

         div.prop("class", "panel block row " + $("#used-classes").text());
         $("#inspector-editor").trigger("refresh");

         $.EW("getParentDialog", $("#block-form")).trigger("close");
      };
      return new BlockForm();
   })();
   BlockForm.readClasses();
</script>