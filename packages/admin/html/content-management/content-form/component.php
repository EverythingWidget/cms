<?php
$content_data = $form_config["data"];

// Set form id to 'content-form' if it is not specified
$form_id = ($form_config["formId"]) ? $form_config["formId"] : "content-form";

// Set content type to the default content type if it is not specified. Default content type is article
if (!$form_config['content_type']) {
  $form_config['content_type'] = "article";
}

// Set default form title to 'Article'
if (!$form_config['form_title']) {
  $form_config['form_title'] = 'Article';
}

$tabs = EWCore::read_registry('ew/ui/forms/content/tabs');
?>

<form id="<?= $form_id ?>"  action="#" method="POST">
  <div class="header-pane thin tabs-bar">
    <h1 id="form-title">
      <span>tr{New}</span>tr{<?= $form_config["form_title"] ?>}
    </h1>
    <ul class="nav nav-pills xs-nav-tabs">
      <?php
      $active = 'active';
      foreach ($tabs as $id => $tab) {
        echo "<li class='$active'><a href='#{$id}' data-toggle='tab'>tr{{$tab["title"]}}</a></li>";
        $active = '';
      }
      ?>
    </ul>
  </div>

  <div class="form-content">
    <div class="tab-content">     
      <?php
      $active = 'active';
      foreach ($tabs as $id => $tab) {
        echo "<div class='tab-pane $active' id='{$id}'>" .
        EWCore::get_view($tab['template_url'], [
            'form_id'      => $form_id,
            'content_type' => $form_config['content_type'],
            'form_config'  => $form_config
        ]) . "</div>";

        $active = '';
      }
      ?>
    </div>
  </div>

  <div class="footer-pane actions-bar action-bar-items">
  </div>
</form>

<script>
  // ContentForm predefined functions
  var ContentForm = {
    formId: "#<?= $form_id ?>",
    allLabels: <?= json_encode(array_keys(EWCore::read_registry(EWCore::$EW_CONTENT_COMPONENT))) ?>,
    initLabels: function (labels) {
      var allLabels = this.allLabels.slice(0);
      labels.forEach(function (el) {
        var labelSwitch = $("#" + el.key + "_control_button");
        labelSwitch.attr('active', 'true');

        allLabels.splice(allLabels.indexOf(el.key), 1);
      });

      $.each(allLabels, function (i, el) {
        var switchBtn = $("#" + el + "_control_button");
        switchBtn.removeAttr('active');
      });
    },
    /**
     * Active specified label
     * @param {string} label Name of the label
     * @param {boolean} flag If true then active the label only for the new content. Default is false
     */
    activeLabel: function (label, flag) {

      if (!flag) {
        $("#" + label + '_control_button:not(:checked)').click();
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

      EW.setFormData(ContentForm.formId, contentInfo);
      $("#content").change();
    }
  };

  ContentForm.uiForm = $(ContentForm.formId);
  ContentForm.uiTitle = ContentForm.uiForm.find("#form-title");

<?= $form_config["include_script"] ?>

// Set form data when the form is completely loaded
  (function () {
    $.each($(ContentForm.formId + " .content-label"), function (i, e) {
      var $e = $(e);

      var lcb = $e.find(".label-control-button");
      lcb.bind("switched", function (e) {
        if (e.originalEvent.detail.active) {
          $e.attr("data-activated", true);
          lcb.text("Turned On");
          lcb.addClass("btn-success").removeClass("btn-default");
          $e.removeClass('disabled');
          TweenLite.fromTo($e[0], .3, {
            opacity: 0
          }, {
            delay: .05,
            opacity: 1
          });

        } else {
          $e.attr("data-activated", false);
          lcb.text("Turned Off");
          lcb.removeClass("btn-success").addClass("btn-default");
          $e.stop().animate({
            className: "+=disabled"
          }, 300, "Power3.easeInOut");
        }
      });
    });

    ContentForm.setData(<?= $content_data; ?>);
    $(ContentForm.formId).find("#title").focus();
  })();

</script>
<?= $form_config["script"] ?>
