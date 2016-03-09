<form id="<?= $form_id ?>"  action="#" method="POST">
  <div class="header-pane tabs-bar row">
    <h1 id="form-title" class="col-xs-12">
      <span>tr{New}</span>tr{<?= $form_config["formTitle"] ?>}
    </h1>  
    <ul class="nav nav-pills xs-nav-tabs">
      <li class="active"><a href="#content-properties" data-toggle='tab'>tr{Properties}</a></li>
      <li class=""><a href="#content-html" data-toggle='tab'>tr{Content}</a></li>
      <?php
      foreach ($tabs as $id => $tab) {
        echo "<li class='' ><a href='#{$id}' data-toggle='tab'>tr{" . $tab["title"] . "}</a></li>";
      }
      ?>
    </ul>
  </div>
  <div class="form-content  tabs-bar">
    <div class="tab-content">
      <div class="tab-pane active" id="content-properties">
        <?= get_properties($form_config, $form_id); ?>
      </div>
      <div class="tab-pane" id="content-html">
        <?= get_editor($form_config, $form_id); ?>
      </div>
      <?php
      foreach ($tabs as $id => $tab) {
        //$tab_object = EWCore::process_request_command($tab["app"], $tab["resource"], $tab["module"], $tab["method"], ["form_config" => $form_config]);
        echo "<div class='tab-pane' id='{$id}'>" . preg_replace('/\{\{formId\}\}/', $form_id, $tab["form"]) . "</div>";
      }
      ?>
    </div>
  </div>
  <div class="footer-pane row actions-bar action-bar-items">
  </div>
</form>

<script>
  // ContentForm predefined functions
  var ContentForm = {
    formId: "#<?= $form_id ?>",
    allLabels: <?= json_encode(array_keys(EWCore::read_registry(EWCore::$EW_CONTENT_COMPONENT))) ?>,
    initLabels: function (labels) {
      //$(".content-label .label-control-button:checked").click();
      //$(".content-label .label-control-button").prop("checked", false);

      var allLabels = this.allLabels.slice(0);
      $.each(labels, function (i, el) {
        var t = $("#" + el.key + "_control_button");
        t[0].setAttribute('active', true);

        allLabels.splice(allLabels.indexOf(el.key), 1);
      });

      $.each(allLabels, function (i, el) {
        var t = $("#" + el + "_control_button");
        t[0].setAttribute('active', false);
      });
    },
    /**
     * Active specified label
     * @param {string} label Name of the label
     * @param {boolean} flag If true then active the label only for the new content. Default is false
     */
    activeLabel: function (label, flag) {

      if (!flag) {
        $("#" + label + "_control_button:not(:checked)").click();
        $("#" + label + "_control_button").prop("checked", true);
        return;
      }

      if (!this.getFormData().id) {
        $("#" + label + "_control_button:not(:checked)").click();
        $("#" + label + "_control_button").prop("checked", true);
      }
    },
    /**
     * Get content label as json object
     * 
     * @returns {json} return a json object contained of content labels in the {key:value} format
     */
    getLabels: function () {
      var labels = {};
      $.each(this.uiForm.find("#content-labels .content-label"), function (i, el) {
        el = $(el);
        if (el.attr("data-activated") === "false") {
          labels[el.find("input[name='key']").val()] = null;
        } else if (!el.find("input[name='key']").is(":disabled") && !el.find("[name='value']").is(":disabled")) {
          labels[el.find("input[name='key']").val()] = el.find("[name='value']").val();
        }
      });

      return JSON.stringify(labels);
    },
    /**
     * Get content label as json object
     * 
     * @returns {json} return a json object contained of content labels in the {key:value} format
     */
    getLabel: function (key) {
      var value = null;
      $.each(this.uiForm.find("#content-labels .content-label[data-activated='true']"), function (i, el) {
        el = $(el);
        if (el.find("input[name='key']:not(:disabled)").val() == key) {
          value = el.find("[name='value']").val();
          return;
        }
      });

      return value;
    },
    setLabels: function (labels) {
      this.uiForm.find("#content-labels .content-label input[name='value']").val("");
      $.each(labels, function (i, el) {
        $("#" + el.key + "_value").val(el.value);
      });
    },
    /**
     * Get the content form data as json
     * 
     * @returns {json} return a json object of form data
     */
    getFormData: function () {
      var formData = $.parseJSON($(this.formId).serializeJSON());
      delete formData.key;
      delete formData.value;
      formData['labels'] = this.getLabels();
      if (contentEditor.regions() && contentEditor.regions()[0]) {
        formData["content"] = contentEditor.regions()[0].html();
        contentEditor._rootLastModified = ContentEdit.Root.get().lastModified();
      }

      return formData;
    },
    setData: function (contentInfo) {
      if (contentInfo && contentInfo.labels) {
        ContentForm.initLabels(contentInfo.labels);
        ContentForm.setLabels(contentInfo.labels);
      }

      EW.setFormData(this.formId, contentInfo || {});
      $("#content").change();
    }
  };

  ContentForm.uiForm = $(ContentForm.formId);
  ContentForm.uiTitle = ContentForm.uiForm.find("#form-title");


</script>
<?= $form_config["script"] ?>
<script>
  // Set form data when the form is completely loaded
  $(document).ready(function () {

    $.each($(ContentForm.formId + " .content-label"), function (i, e) {
      var $e = $(e);
      var lcb = $e.find(".label-control-button");
      lcb.on("switched", function (e) {
        //var label = lcb.next("span");
        //var labelBox = lcb.parent();
        if (e.originalEvent.detail.active) {
          $e.attr("data-activated", true);
          lcb.text("Turned On");
          lcb.addClass("btn-success").removeClass("btn-default");
          $e.stop().animate({
            className: "-=disabled"
          },
            500, "Power3.easeInOut");

        } else {
          $e.attr("data-activated", false);
          //alert("click: "+e.attr("data-activated"));
          lcb.text("Turned Off");
          lcb.removeClass("btn-success").addClass("btn-default");
          $e.stop().animate({
            className: "+=disabled"
          },
            400, "Power3.easeInOut");
        }
      });
    });
    ContentForm.setData(<?php echo $content_data; ?>);
    $(ContentForm.formId).find("#title").focus();
  });
</script> 