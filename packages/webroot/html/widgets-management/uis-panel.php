<?php
session_start();
$uiStructureId = $_REQUEST['uisId'];
$panel_id = $_REQUEST["panelId"];
$container_id = $_REQUEST["containerId"];
?>
<div class="header-pane tabs-bar">
  <h1 id="uis-panel-title">
    <span>tr{Add}</span>tr{Panel}
  </h1>

  <ul class="nav nav-pills">
    <li class="active"><a href="#properties" data-toggle="tab">Properties</a></li>

    <li><a href='#size-layout' data-toggle='tab'>Size & Layout</a></li>
  </ul>
</div>

<div class="form-content tabs-bar">
  <div class="tab-content">
    <div class="tab-pane active" id="properties">
      <form id="uis-panel" >
        <div class="block-row">
          <input type="hidden" name="cmd" id="cmd" >
          <system-field class="field col-xs-12">
            <label>tr{ID}</label>
            <input id="style_id" name="style_id" class="text-field" value="<?= $row['style_id'] ?>">
          </system-field> 
        </div>
        <div class="block-row">
          <system-field class="field col-xs-12">
            <label>tr{Class}</label>
            <input id="style_class" name="style_class" class="text-field" v-model="styleClassesText">            
          </system-field>  

          <div class="col-xs-12">
            <label class="small" id="used-classes">
              <span class='tag label label-info'
                    v-for="class in usedClasses">
                {{ class }}
              </span>
            </label>
          </div>          
        </div>

        <div class="block-row">
          <div class="col-xs-12">
            <h3>Classes</h3>
            <div class=" options-panel" id="available-classes">
              <label class="btn btn-default" 
                     v-bind:class=" { 'active' : isSelected(class) } "
                     v-for="class in templateClasses" 
                     v-on:click="toggleClass(class)">                
                {{ class }}
              </label>
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
      <?php include 'layouts/widget-form/uis-widget-size-layout.php'; ?>
    </div>
  </div>
</div>

<div id="uis-panel-actions" class="footer-pane actions-bar action-bar-items"> </div>

<script  type="text/javascript">

  (function () {
    var panel = $("#fr").contents().find("body #base-content-pane div[data-panel-id='<?= $panel_id ?>']");
    var usedClasses = panel.prop('class').split(' ');

    var panelVue = new Vue({
      el: '#uis-panel',
      data: {
        panelId: <?= $panel_id ?>,
        styleClassesText: '',
        allClasses: [],
        layoutClasses: [],
        templateClasses: <?= json_encode(EWCore::parse_css_clean(EW_PACKAGES_DIR . "/rm/public/{$_REQUEST['template']}/template.css", 'panel')); ?>
      },
      computed: {
        styleClasses: function () {
          return this.styleClassesText.split(' ').filter(Boolean);
        },
        panelClasses: function () {
          var _this = this;

          return _this.templateClasses.filter(function (item) {
            return _this.allClasses.indexOf(item) > -1;
          });
        },
        usedClasses: {
          set: function (value) {
            this.allClasses = value;
          },
          get: function () {
            var a = this.panelClasses;
            var b = this.styleClasses;
            var all = this.allClasses.concat(a).concat(b);
            return  all.filter(function (item, pos) {
              return all.indexOf(item) === pos;
            });
          }
        }
      },
      methods: {
        isSelected: function (className) {
          return this.allClasses.indexOf(className) > -1;
        },
        toggleClass: function (className) {
          var index = this.allClasses.indexOf(className);
          if (index !== -1) {
            this.allClasses.splice(index, 1);
          } else {
            this.allClasses.push(className);
          }
        },
        refreshStyleText: function() {
          var left = this.usedClasses.filter(function(item) {
            return 
          })
        }
      }
    });

    panelVue.usedClasses = panel.prop('class').split(' ');

    function UISPanel() {
      var _this = this;
      this.bAdd = EW.addAction("tr{Add}", $.proxy(this.addPanel, this), {
        display: "none"
      }, 'uis-panel-actions');

      this.bEdit = EW.addAction("tr{Save}", $.proxy(this.updatePanel, this), {
        display: "none"
      }, 'uis-panel-actions');

      $("#appearance-conf input[name='title']").change(function () {
        if ($(this).val() == "") {
          $('#title-text').prop('disabled', true);
        } else {
          $('#title-text').prop('disabled', false);
        }
      });

      this.panelId = "<?= $panel_id ?>";
      this.containerId = "<?= $container_id ?>";

      if (this.panelId) {
        var panel = $("#fr").contents().find("body #base-content-pane div[data-panel-id='" + this.panelId + "']");
        var panelParams = (panel.attr("data-panel-parameters")) ? $.parseJSON(panel.attr("data-panel-parameters")) : {};
        EW.setFormData("#appearance-conf", panelParams);

        $('#uis-panel-title').html('<span>tr{Edit}</span>tr{Panel}');

        this.bAdd.comeOut(200);
        this.bEdit.comeIn(300);
      } else {
        this.bAdd.comeIn(300);
        this.bEdit.comeOut(200);
      }



      $("#size-layout input:radio,#size-layout input:checkbox,input[data-slider]").change(function (event) {
        _this.setClasses();
      });

      _this.readClasses();
    }

    UISPanel.prototype.readClasses = function () {
      var $sizeAndLayout = $("#size-layout");
      var classes = panelVue.usedClasses;
      var layoutClasses = [];

      $.each($sizeAndLayout.find('input:radio, input:checkbox'), function (k, field) {
        var $v = $(field), value = $v.val();

        $.each(classes, function (i, className) {
          if (value === className) {
            $v.click();
            $v.prop("checked", true);
            layoutClasses.push(classes.splice(i, 1));
          }
        });
      });

      $.each($sizeAndLayout.find('input[data-slider]'), function (k, field) {
        $.each(classes, function (i, c) {
          if (!c)
            return;

          var sub = c.match(/(\D+)(\d*)/);
          if (sub && $(field).attr("name") === sub[1]) {
            $(field).val(sub[2]).change();
            layoutClasses.push(classes.splice(i, 1));
          }
        });
      });

      panelVue.layoutClasses = layoutClasses;
      //panelVue.styleClassesText = classes.join(' ');
    };

    UISPanel.prototype.setClasses = function () {
      var otherClasses = panelVue.panelClasses;

      $.each($("#panel-classes").find("input"), function (k, field) {
        otherClasses.push($(field).val());
      });

      $.each($("#size-layout input[data-slider]:not(:disabled)"), function (k, field) {
        if (parseInt(field.value)) {

          otherClasses.push(field.name + field.value);
        }
      });

      $.each($("#size-layout input:radio:checked:not(:disabled),#size-layout input:checkbox:checked:not(:disabled)"), function (k, field) {
        otherClasses.push($(field).val());
      });

      var usedClasses = otherClasses.concat(panelVue.styleClasses);

      panelVue.usedClasses = usedClasses.filter(function (item, pos) {
        return usedClasses.indexOf(item) === pos;
      });

      console.log(usedClasses)
    };

    // Create and add new div to the page
    UISPanel.prototype.addPanel = function (pId) {
      var self = this;
      //EW.lock(neuis.currentDialog, "...");
      var params = $("#appearance-conf").serializeJSON();
      var div = $("<div data-panel='true'></div>");
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
      var panel = $("#fr").contents().find("body #base-content-pane div[data-panel-id='" + this.panelId + "']");
      var oldParameters = panel.attr("data-panel-parameters");
      panel.prop("id", $("#style_id").val());
      panel.attr("data-panel-parameters", params);
      panel.prop("class", "panel " + panelVue.usedClasses.join(' '));
      /*if (oldParameters != params)
       {
       neuis.updateUIS(true);
       }*/

      //$("#inspector-editor").trigger("refresh");
      $.EW("getParentDialog", $("#uis-panel")).trigger("close");
    };

    uisPanel = new UISPanel();
  })();
  var uisPanel;


</script>