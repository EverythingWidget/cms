/* global System */

function UISForm() {
  clearTimeout(repTimeout);
  System.ui.forms.uis_form = this;

  var _this = this;
  this.currentDialog;
  this.dpPreference = null;
  this.uisId;
  this.uisTemplate = "";
  this.defaultStructure = null;
  this.inlineEditor = {};
  this.layoutForm = $("#layout-form");
  this.editorWindow = $("#editor-container");
  this.editorWindow.width($(window).width() - 400);
  this.uis_editor_tool_pane = $("#uis-editor-tool-pane");
  this.inspectorEditor = $("#inspector-editor");
  this.editorIFrame = $(document.getElementById("fr"));
  this.editorFrame = this.editorIFrame.contents().find("body");
  this.templateSettingsForm = $("#template_settings_form");
  this.widgetsList = $("#items-list #items-list-content");
  this.bExportLayout = $();
  this.bSaveChanges = EW.addAction("Save Layout", $.proxy(this.updateUIS, this)).hide().addClass("btn-success");
  this.bPreview = EW.addAction("Preview Layout", $.proxy(this.previewLayout, this)).hide().addClass("btn-info");
  this.bDelete = EW.addAction("Delete", $.proxy(this.deleteUIS, this), {
    display: "none"
  });
  this.bSaveAndStart = EW.addAction("Save And Start Editing", $.proxy(this.addUIStructure, this), {
    display: "none"
  });

  this.bSavePref = EW.addAction("Save Changes", $.proxy(this.updateUIS, this, true), {
    display: "none"
  }, "layout-form-actions").addClass("btn-success");

  if (EW.getActivity({
    activity: "webroot/api/widgets-management/export-uis"
  })) {
    $("#layout-form-actions").append("<a class='btn btn-text btn-primary pull-right export-btn' href=api/webroot/widgets-management/export-uis?uis_id=" + this.uisId + ">Export Layout</a>");
    this.bExportLayout = $("#layout-form-actions a.export-btn");
    this.bExportLayout.hide();
  }

  this.widgetsList.on('item-selected', function (event) {
    var data = event.originalEvent.detail.data;
    if (data.path === 'panel') {
      _this.addPanel(data.parentId);
    } else {
      _this.widgetForm(data.path, data.parentId, data.feeder_type);
    }
  });

  $("#perview_url").EW().inputButton({
    title: "Apply",
    id: "set_url_btn",
    onClick: _this.reloadFrame
  });

  var itemsList = $("#items-list");
  itemsList[0].close = function () {
    itemsList.animate({
      left: -400
    }, 200);
  };

  this.inspectorEditor[0].isValidParent = function (item, parent) {
    //UIUtil.hasCSSClass(item, "block")
    var $parent = $(parent);
    if (System.ui.utility.hasClass(item, "block") && !$parent.is(".layout-components")) {
      return false;
    }

    if (System.ui.utility.hasClass(item, "widget") && $parent.is(".layout-components")) {
      return false;
    }

    if (System.ui.utility.hasClass(item, "panel") && $parent.is(".layout-components")) {
      return false;
    }

    return true;
  };

  this.inspectorEditor[0].onDrop = function (item, parent, index) {
//console.log(parent,index)
    var frameBody = $(document.getElementById("fr").contentDocument.body);
    if (!parent) {
      return;
    }

    //var $parent = $(parent);
    var linkedParentId = parent.parentNode.dataset.linkedPanelId;
    var linkedPanelId = item.dataset.linkedPanelId;
    var linkedWidgetId = item.dataset.linkedWidgetId;
    //oldContainer.removeClass("highlight");
    var $parent = frameBody.find("[data-panel-id='" + linkedParentId + "']");
    var baseContentPane = frameBody.find("#base-content-pane");

    if (!$parent.attr("data-block"))
    {
      //$parent = $parent.children().eq(0);
    }

    if ($parent.length <= 0)
    {
      var panel = frameBody.find("[data-panel-id='" + linkedPanelId + "']").detach();
      if (baseContentPane.children().length <= index)
      {
        baseContentPane.append(panel);
      } else
      {
        baseContentPane.children().eq(index).before(panel);
      }
      //_super(item);
      return;
    }

    if (linkedWidgetId)
    {

      //alert(linkedWidgetId);
      var widget = frameBody.find("[data-widget-id='" + linkedWidgetId + "']").parent().detach();

      //console.log($parent, $parent.children().length, index);

      if ($parent.children().length <= index) {
        $parent.append(widget);
      } else {

        $parent.children().eq(index).before(widget);
      }
    }
    if (linkedPanelId)
    {
      var panel = frameBody.find("[data-panel-id='" + linkedPanelId + "']").detach();
      if ($parent.length == 0)
      {
        $parent = baseContentPane;
      }
      if ($parent.children().length <= index)
      {
        $parent.append(panel);
      } else
      {
        $parent.children().eq(index).before(panel);
      }
    }

  };

  //console.log(this.inspectorEditor[0]);

  // Add 'Add Block' button to the inspector-panel
  var addBlockBtn = $("<button type='button' class='btn btn-primary btn-sm'>tr{Add Block}</button>");
  addBlockBtn.css({
    float: "none",
    margin: "10px auto",
    display: "block"
  });

  addBlockBtn.on("click", $.proxy(this.blockForm, this, null));
  this.inspectorEditor.append(addBlockBtn);

  //Add refresh event to inspector editor
  this.inspectorEditor.off('refresh').on('refresh', function () {
    var st = _this.uis_editor_tool_pane[0].scrollTop;
    _this.loadInspectorEditor();
    _this.uis_editor_tool_pane[0].scrollTop = st;

    // Save the first loaded structure as the default
    if (_this.defaultStructure === null) {
      _this.defaultStructure = _this.createContentHeirarchy();
    }
  });

  this.hEditor = {};

  // Load inspector editor when the content of frame has been loaded
  this.editorIFrame.load(function () {
    $(document.getElementById("fr").contentDocument.head).append("<style id='editor-style'>" + $("#editor-css").html() + "</style>");

    $("#template").off('change').on('change', $.proxy(_this.reloadFrame, _this));

    _this.templateChanged();
  });

  // Destroy preference modal on close
  var parentDialog = $.EW("getParentDialog", $("#ew-uis-editor"));
  parentDialog.on("close", function () {
    clearTimeout(repTimeout);
    if (_this.dpPreference)
      _this.dpPreference.trigger("destroy");
  });

  parentDialog.on("beforeClose", function () {
    //console.log(self.oldStructure, self.createContentHeirarchy(), self.createContentHeirarchy());
    if (_this.defaultStructure !== null && JSON.stringify(_this.defaultStructure) !== JSON.stringify(_this.createContentHeirarchy())) {
      return confirm("tr{You have unsaved changes. Are you sure you want to close?}");
    } else {
      return true;
    }
  });

  _this.layoutForm.on('refresh', function (event, data) {
    if (data.id) {
      $('#form-title').html('<span>tr{Edit}</span>' + data.name);
      _this.uisId = data.id;
      $("#layout-form-actions .export-btn").attr("href", "api/webroot/widgets-management/export-uis?uis_id=" + _this.uisId);
      _this.uisTemplate = data.template;

      if (data.template_settings) {
        _this.templateSettings = data.template_settings;
      } else
        _this.templateSettings = {};
      EW.setFormData("#template_settings_form", _this.templateSettings);
      _this.reloadFrame();
      _this.readTemplateClassAndId();
    }
    _this.init();
  });

  _this.relocateGlassPanes();
}

UISForm.prototype.setTemplateSettings = function (settings) {
  this.templateSettings = settings || {};
};

UISForm.prototype.previewLayout = function () {
  window.open('<?php echo EW_ROOT_URL ?>' + '?_uis=' + this.uisId + '&editMode=true');
};

UISForm.prototype.clearEditor = function () {
  var frBody = $(document.getElementById("fr").contentDocument.body);
  frBody.find("#editor-glass-pane").remove();
  frBody.find(".panel-overlay").remove();
  frBody.find(".panel-glass-overlay").remove();
  frBody.find(".widget-overlay").remove();
  frBody.find("#base-content-pane .panel").css({
    paddingBottom: "-=50px"
  });
};


UISForm.prototype.createInspector = function (element, init) {
  var self = this;
  var frameBody = $(document.getElementById("fr").contentDocument.body);
  //frameBody.children(".widget-glass-pane").remove();
  //var editorGlassPane = frameBody.children("#editor-glass-pane");

  var children = $(element).children();
  if (init)
  {
    children = $(element).find("[data-block]:not([data-not-editable] [data-block])");
  }
  var result = new Array();
  var skipBoxBlock = false;
  var skipChildren = false;
  //var liUl = null;
  var itemLabel;

  $.each(children, function (k, uisItem) {
    uisItem = $(uisItem);
    var liUl = null;
    //var div = $("<div></div>");
    if (uisItem.hasClass("panel") || uisItem.hasClass("block")) {
      liUl = $("<li><div href='#' class='item-label'>\n\
      <span class='handle panel'></span></div><a href='#' class='btn btn-primary btn-text add-item'>+</a><a href='#' class='close-icon' ></a></li>");
      itemLabel = liUl.find(".item-label");
      liUl.attr("data-linked-panel-id", uisItem.attr("data-panel-id"));
      skipBoxBlock = false;

      if (uisItem.hasClass("row")) {
        itemLabel.append("Block");
        liUl.find(".handle").attr("class", "handle block");
        liUl.addClass("block");
        itemLabel.click(function (e) {
          self.blockForm(uisItem.attr('data-panel-id'));
          e.preventDefault();
        });
      } else if (uisItem.children(".row").length > 0) {
        itemLabel.append("Panel");
        liUl.addClass("panel");
        // Set data link panel id for the panel
        liUl.attr("data-linked-panel-id", uisItem.attr('data-panel-id'));
        self.lastItem = liUl;

        itemLabel.click(function (e) {
          self.editPanel(uisItem.attr('data-panel-id'), uisItem.attr('data-container-id'));
          e.preventDefault();
        });
        //skipBoxBlock = true;
        skipChildren = true;
      } else {
        itemLabel.append("Panel: " + (uisItem.attr('id') || ''));
        liUl.addClass("panel");
        itemLabel.click(function (e) {
          self.editPanel(uisItem.attr('data-panel-id'), uisItem.attr('data-container-id'));
          e.preventDefault();
        });
      }

      // Add widget button for panels
      var addItem = liUl.find(".add-item");
      addItem.click($.proxy(self.showWidgetsList, self, uisItem.attr('data-panel-id')));
      addItem.hover(function () {
        liUl.addClass("highlight");
      }, function () {
        liUl.removeClass("highlight");
      });

      // Remove button
      liUl.find(".close-icon").click(function (e) {
        e.preventDefault();
        self.removePanel(uisItem.attr('data-panel-id'));
      });

      itemLabel.hover(function () {
        var panel = frameBody.find("[data-panel-id='" + uisItem.attr('data-panel-id') + "']");
        // Scroll to the panel if the panel is not in view port
        var offset = panel.offset();
//          var scrollTop = frameBody.scrollTop();
//
//          if (offset.top > (scrollTop + self.editorIFrame.height()) || offset.top + panel.outerHeight() < scrollTop) {
//            frameBody.stop().animate({
//              scrollTop: offset.top
//            }, 500);
//          }

        self.currentElementHighlight.css({
          top: offset.top,
          left: offset.left,
          position: "absolute",
          width: panel.outerWidth(),
          height: panel.outerHeight(),
          margin: "0"
        });

        self.currentElementHighlight.show();
      }, function () {
        self.currentElementHighlight.hide();
      });

      var ul = $("<ul></ul>");
      ul.append(self.createInspector(uisItem));
      // Skip adding panel block to the editor
      if (skipBoxBlock) {
        if (self.lastItem) {
          self.lastItem.find(".add-item").unbind("click").click(function (e) {
            e.preventDefault();
            self.showWidgetsList(uisItem.attr('data-panel-id'));
          });

          self.lastItem.append(ul);
          self.lastItem = null;
        }
      } else {
        if (skipChildren) {
          skipChildren = false;
          ul.html(self.createInspector(uisItem.children().eq(0)));
          liUl.append(ul);
        } else {
          liUl.append(ul);
        }
        result.push(liUl);
      }
    }

    if (uisItem.hasClass("widget-container")) {
      var widgetGlassPane = $(document.createElement("div"));
      widgetGlassPane.addClass("widget-glass-pane");
      widgetGlassPane.data("widget-element", uisItem);
      frameBody.append(widgetGlassPane);

      var widget = uisItem.children().eq(0);

      var editWidget = function (e) {
        self.editWidget(widget.attr('data-widget-id'));
        e.preventDefault();
      };

      widgetGlassPane.off('click').on('click', editWidget);

      var li = $("<li class='widget'><div href='#' class='item-label'><span class='handle widget'></span></div><a href='#' class='close-icon' ></a></li>");
      li.attr("data-linked-widget-id", widget.attr("data-widget-id"));
      var widgetTitle = li.find(".item-label");

      widgetGlassPane.on({
        mouseenter: function () {
          widgetTitle.addClass('hover');
        },
        mouseleave: function () {
          widgetTitle.removeClass('hover');
        }
      });


      widgetTitle.append(widget.attr("data-widget-title") + ' | ' + (widget.attr('id') || ''));
      widgetTitle.click(editWidget);

      li.find(".close-icon").on('click', function (e) {
        e.preventDefault();
        self.removeWidget(widget.attr('data-widget-id'));
      });

      var inlineEditor = self.inlineEditor[widget.attr('data-widget-id')];

      if (inlineEditor) {
        inlineEditor.css({
          fontSize: "24px",
          position: "absolute",
          top: widget.offset().top,
          left: widget.offset().left,
          backgroundColor: "rgba(255,255,255,.8)"
        });
        frameBody.find("#editor-glass-pane").append(inlineEditor);
      }
      // Show bloack glass on hover for widget

      //var widgetClone = widget.clone();
      li.hover(function () {
        //console.log(widget);
        //var widget = frameBody.find("[data-widget-id='" + widget.attr('data-widget-id') + "']");
        // Scroll to the widget if the panel is not in view port
        var offset = widget.offset();
//          var scrollTop = frameBody.scrollTop();
//
//          if (offset.top > (scrollTop + self.editorIFrame.height()) || offset.top + widget.outerHeight() < scrollTop) {
//
//            frameBody.stop().animate({
//              scrollTop: widget.offset().top
//            }, 500);
//          }

        self.currentElementHighlight.css({
          top: offset.top,
          left: offset.left,
          position: "absolute",
          width: widget.outerWidth(),
          height: widget.outerHeight()
        });
        self.currentElementHighlight.show();
      }
      , function () {
        self.currentElementHighlight.hide();

      });
      result.push(li);
    }
  });
  return result;
};

UISForm.prototype.loadInspectorEditor = function () {
  var self = this;
  self.editor = document.getElementById("fr").contentWindow;
  var frameBody = $(document.getElementById("fr").contentDocument.body);
  var parentNode = frameBody.find("#base-content-pane");
  var panelIndex = 1;
  var widgetIndex = 1;

  if (parentNode[0]) {
    $.each(parentNode[0].querySelectorAll(".block, .panel, .widget"), function (i, element) {
      element = $(element);

      if (element.hasClass("panel") || element.hasClass("block")) {
        if (!element.attr("data-panel-id"))
          element.attr("data-panel-id", "panel-" + panelIndex);
        panelIndex++;
      }

      if (element.hasClass("widget")) {
        if (!element.attr("data-widget-id"))
          element.attr("data-widget-id", "widget-" + widgetIndex);
        widgetIndex++;
      }
    });
  }

  var inspectorEditorList = this.inspectorEditor.children(".layout-components");
  inspectorEditorList.empty();

  // Add div to create glass effect to make the iframe content unselectable
  frameBody.children(".widget-glass-pane").remove();

  // Add a div to represent the highlight of current element
  frameBody.find("div.current-element").remove();
  this.currentElementHighlight = $("<div class='current-element'></div>");
  frameBody.append(this.currentElementHighlight);

  inspectorEditorList.append(self.createInspector(parentNode, true));
};

/**
 * Relocate all the widget's glasspanes every second to keep them over their corresponding widget   
 */
var repTimeout = repTimeout || null;
UISForm.prototype.relocateGlassPanes = function () {
  var self = this;
  var fr = document.getElementById("fr");
  if (fr && fr.contentDocument.body) {
    $.each(fr.contentDocument.body.querySelectorAll(".widget-glass-pane"), function (i, glass) {
      glass = $(glass);
      var widgetContainer = glass.data("widget-element"),
              widget = widgetContainer.children().eq(0),
              widgetoffset = widget.offset(),
              pos = widgetoffset.top + '' + widgetoffset.left + '' + widget.outerWidth() + '' + widget.outerHeight();

      if (pos === glass[0].dataset.position)
        return;

      glass.css({
        top: widgetoffset.top,
        left: widgetoffset.left,
        width: widget.outerWidth(),
        height: widget.outerHeight()
      });

      glass[0].dataset.position = pos;
    });
  }
  //if (!repTimeout) {
  clearTimeout(repTimeout);
  repTimeout = setTimeout(function () {
    repTimeout = null;
    self.relocateGlassPanes();
  }, 500);
  //}
};

/** Create a json string from current layout structure heirarchy 
 * 
 
 * @returns {String} */
UISForm.prototype.createContentHeirarchy = function () {
  var self = this;
  if (!self.editor)
    return {};
  var panels = $("#fr").contents().find("body #base-content-pane").find("[data-block]:not([data-not-editable] [data-block])");
  var root = {
  };

  $.each(panels, function (i, v) {
    v = $(v).clone();
    v.removeClass("panel").removeClass("block");
    //alert(v.attr("data-panel-parameters"))
    root[i] = {
      type: "panel",
      class: v.prop("class"),
      id: v.attr("id"),
      panelParameters: JSON.parse(v.attr("data-panel-parameters") || '{}'),
      //"blockName": v.attr("data-block-name"),
      children: self.readPanels(v)
    };
  });

  //console.log(root);
  return root;
};

UISForm.prototype.readPanels = function (elm) {
  var self = this;
  var child = {
  };
  var index = 0;
  $.each(elm.children("[data-panel],[data-widget-container]"), function (i, v) {
    v = $(v).clone();

    if (v.attr("data-panel")) {
      v.removeClass("panel");
      child[index++] = {
        type: "panel",
        class: v.prop("class"),
        id: v.attr("id"),
        /*"panelParameters": JSON.parse(v.attr("data-panel-parameters") || '{}'),*/
        // Read the childeren of the panel
        //children: self.readPanels(v.children().eq(0))
        children: self.readPanels(v)
      };
    } else if (v.attr("data-widget-container")) {
      v.removeClass("widget-container");
      var w = v.children(".widget");
      w.removeClass("widget");
      //alert(w.prop("class"));
      child[index++] = {
        type: "widget",
        class: v.prop("class"),
        widgetClass: w.prop("class"),
        id: w.attr("id"),
        widgetType: w.attr("data-widget-type"),
        widgetParameters: self.editor.ew_widget_data[w.attr("data-widget-id")]
      };
    }

  });

  return child;
};

/**
 * Return the widget with the specified id
 * @param {string} id The widget id
 * @returns {jQuery}    */
UISForm.prototype.getEditorItem = function (id) {
  var item = $("#fr").contents().find("body").find("div[data-widget-id='" + id + "']");
  item.data("container", item.parent());
  return item;
};

UISForm.prototype.getEditor = function () {
  return $("#fr").contents();
};

UISForm.prototype.getLayoutBlocks = function () {
  var items = $("#fr").contents().find("body [data-block]:not([data-not-editable] [data-block])");
  //item.data("container", item.parent());
  return items;
};

UISForm.prototype.getLayoutWidgets = function () {
  var items = $("#fr").contents().find("body [data-widget]:not([data-not-editable] [data-widget])");
  //item.data("container", item.parent());
  return items;
};

UISForm.prototype.init = function () {
  var self = this;
  // if uis id exist show the save change button, else show the save and start editing button
  if (self.uisId && self.uisId != 0)
  {
    $('#ew-uis-editor-tabs a[href="#template-control"]').show();
    $('#ew-uis-editor-tabs a[href="#inspector"]').show();
    self.bSaveAndStart.comeOut(200);
    self.bSaveChanges.comeIn(300);
    self.bPreview.comeIn(300);
    self.bExportLayout.show();
  } else
  {
    $('#ew-uis-editor-tabs a[href="#pref"]').tab('show');
    $('#ew-uis-editor-tabs a[href="#template-control"]').hide();
    $('#ew-uis-editor-tabs a[href="#inspector"]').hide();
    self.bSaveAndStart.comeIn(300);
    self.bSaveChanges.comeOut(200);
    self.bPreview.comeOut(200);
    self.bExportLayout.hide();
  }
};

UISForm.prototype.templateChanged = function () {
  var _this = this;
  var template = $("#template").val();
  _this.templateSettingsForm.empty();

  if (template) {
    $.post('api/webroot/widgets-management/get-template-settings-form', {
      path: template
    }, function (response) {
//      console.log(response);
      _this.frameLoader.dispose();
      _this.uisTemplate = template;
      _this.templateSettingsForm.off("getData");
      _this.templateSettingsForm.html(response.data['html']);
      EW.setFormData("#template_settings_form", _this.templateSettings);
      _this.updateTemplateBody();
    });
  } else {
    _this.frameLoader.dispose();
  }
};

UISForm.prototype.readTemplateClassAndId = function () {
};

UISForm.prototype.addUIStructure = function () {
  var self = this;
  $('#name').removeClass("red");
  if (!$('#name').val())
  {
    $('#name').addClass("red");
    return;
  }
  //EW.lock(self.dpPreference, "Saving...");
  var defaultUIS = $("#uis-default").is(":checked");
  var homeUIS = $("#uis-home-page").is(":checked");
  self.templateSettings = self.templateSettingsForm.serializeJSON();
  self.templateSettingsForm.trigger("getData");

  $.post('api/webroot/widgets-management/add-uis', {
    name: $('#name').val(),
    template: $('#template').val(),
    template_settings: JSON.stringify(self.templateSettings),
    defaultUIS: defaultUIS,
    homeUIS: homeUIS
  }, function (data) {
    self.uisTemplate = $('#template').val();
    self.uisId = data.uisId;

    EW.setHashParameters({
      "uisId": self.uisId
    });

    $(document).trigger("uis-list.refresh");
    self.reloadFrame();
    self.init();
    $("body").EW().notify(data).show();
  }, "json");
};

UISForm.prototype.updateUIS = function (reload) {
  var self = this;
  $('#name').removeClass("red");
  if (!$('#name').val()) {
    $('#name').addClass("red");
    return;
  }

  var lock = System.ui.lock({
    element: $.EW("getParentDialog", $("#ew-uis-editor"))[0],
    akcent: "loader center"
  }, .5);

  var structure = (self.createContentHeirarchy());
  var defaultUIS = $("#uis-default").is(":checked");
  var homeUIS = $("#uis-home-page").is(":checked");
  self.templateSettings = self.templateSettingsForm.serializeJSON();
  self.templateSettingsForm.trigger("getData");

  $.post('api/webroot/widgets-management/update-uis', {
    name: $('#name').val(),
    template: $('#template').val(),
    template_settings: self.templateSettings,
    perview_url: $("#perview_url").val(),
    structure: structure,
    uisId: self.uisId,
    defaultUIS: defaultUIS,
    homeUIS: homeUIS
  }, function (data) {
    $("body").EW().notify(data).show();
    $(document).trigger("uis-list.refresh");
    $('#form-title').html("<span>Edit</span> " + data.data.title);
    self.defaultStructure = structure;
    if (reload === true) {
      self.reloadFrame();
    } else {
      self.defaultStructure = self.createContentHeirarchy();
    }
    lock.dispose();
  }, "json");
};

UISForm.prototype.updateTemplateBody = function () {
  // Update template body with current template settings
  var _this = this;
  var lock = System.ui.lock({
    element: this.editorWindow[0]
    ,
    akcent: "loader center"
  }, .5);

  //var originalTemplateSettings = self.templateSettings;
  // Read template settings from template settings form
  _this.templateSettingsForm.trigger("getData");

  $.get('api/webroot/widgets-management/get-layout/', {
    uisId: _this.uisId,
    template: _this.uisTemplate,
    template_settings: JSON.stringify(_this.templateSettings)
  }, function (response) {
    var myIframe = _this.editorIFrame[0],
            myIframeContent = $(myIframe).contents(),
            head = myIframeContent.find("head"),
            body = myIframeContent.find("body");
    body.off();
    head.find("#template-script").remove();
    head.find("#widget-data").remove();
    if ($('#template').val()) {
      body.find("#template-css").attr("href", "public/rm/" + $('#template').val() + "/template.css");
    }

    body.find("#base-content-pane").remove();

    var widgetData = myIframe.contentWindow.document.createElement("script");
    widgetData.id = "widget-data";
    widgetData.innerHTML = response.data["widget_data"];
    myIframe.contentWindow.document.head.appendChild(widgetData);
    var templateBody = myIframe.contentWindow.document.createElement("div");
    templateBody.id = "base-content-pane";
    templateBody.className = "container";
    templateBody.innerHTML = response.data["template_body"];
    myIframe.contentWindow.document.body.appendChild(templateBody);

    // Adding template script after adding template body
    if (response.data["template_script"]) {
      var script = myIframe.contentWindow.document.createElement("script");
      //script.type = "text/javascript";
      script.id = "template-script";
      var templateScript = $('<script>' + response.data["template_script"] + '</script').attr("id", "template-script");
      script.innerHTML = templateScript.html();

      myIframe.contentWindow.document.head.appendChild(script);
    }
    // Find scripts inside the template body and run them
    var scripts = [];
    var bodyContent = myIframe.contentWindow.document.body;
    scripts = findScriptTags(bodyContent);
    for (script in scripts) {
      evalScript(scripts[script], myIframe);
    }

//      var evt = document.createEvent('Event');
//      evt.initEvent('load', false, false);
//      myIframe.contentWindow.dispatchEvent(evt);

    _this.inspectorEditor.trigger("refresh");
    lock.dispose();
  }, "json");
};

function findScriptTags(element) {
  var scripts = [];
  var ret = element.childNodes;
  if (ret) {
    for (var i = 0; ret[i]; i++) {
      if (ret[i].childNodes.length > 0)
        scripts = scripts.concat(findScriptTags(ret[i], scripts));
      if (scripts && nodeName(ret[i], "script") && (!ret[i].type || ret[i].type.toLowerCase() === "text/javascript")) {
        scripts.push(ret[i].parentNode ? ret[i].parentNode.removeChild(ret[i]) : ret[i]);
      }
    }
  }
  return scripts;
}

function nodeName(elem, name) {
  return elem.nodeName && elem.nodeName.toUpperCase() === name.toUpperCase();
}

function evalScript(elem, frame) {
  var data = (elem.text || elem.textContent || elem.innerHTML || "");
  //var frame = document.getElementById("fr");
  var head = frame.contentWindow.document.getElementsByTagName("head")[0] || frame.contentWindow.document.documentElement;
  var script = frame.contentWindow.document.createElement("script");
  script.appendChild(frame.contentWindow.document.createTextNode(data));
  if (elem.src)
    script.src = elem.src;

  if (elem.parentNode) {
    elem.parentNode.removeChild(elem);
  }

  head.insertBefore(script, head.firstChild);
  head.removeChild(script);
}

UISForm.prototype.addWidget = function (html, parentId) {
  var scripts = [
  ];
  var ret = $(html)[0];
  parentId.appendChild(ret);
  findScriptTags(ret, scripts);
  for (var script in scripts)
  {
    evalScript(scripts[script]);
  }
};

UISForm.prototype.replaceWidget = function (html, parentId) {
  var scripts = [
  ];
  var ret = $(html)[0];
  parentId.parentNode.replaceChild(ret, parentId);
  findScriptTags(ret, scripts);
  for (var script in scripts)
  {
    evalScript(scripts[script]);
  }
};
/** Set data for specified widget
 * 
 * @returns {Boolean} false id data is not a valid json format
 */
UISForm.prototype.setWidgetData = function (widgetId, data) {
  try
  {
    //console.log(typeof (data));
    if (typeof (data) != 'object')
      data = $.parseJSON(data);
  } catch (e) {
    return false;
  }
  this.editor.ew_widget_data[widgetId] = data;
  //console.log(this.editor.ew_widget_data);
}

UISForm.prototype.dispose = function () {
  var self = this;
  self.bDelete.remove();
};

UISForm.prototype.reloadFrame = function (t) {
  var url = !($("#perview_url").val) ? "index.php" : $("#perview_url").val();
  this.frameLoader = System.ui.lock({
    element: $.EW("getParentDialog", $("#ew-uis-editor"))[0],
    akcent: "loader center"
  }, .5);

  $("#inspector-editor > .layout-components").empty();
  $('#fr').attr({
    src: '<?= EW_ROOT_URL ?>' + url + '?_uis=' + this.uisId + '&editMode=true'
  });
};

UISForm.prototype.showWidgetLayoutManager = function (parameters) {
  $("#widget-size-and-layout").stop().animate({
    left: "0px"
  }, 300);
};

UISForm.prototype.showWidgetsList = function (parentId) {
  var _this = this;

  $("#items-list").stop().animate({
    left: "0px"
  }, 300);

  $.post('api/webroot/widgets-management/widgets-types', {
    template: _this.uisTemplate,
    uisId: _this.uisId
  }, function (response) {
    var items = [
      {
        title: 'Panel',
        description: 'Add a panel',
        path: 'panel'
      }
    ];

    items = items.concat(response['data']);
    items.forEach(function (item) {
      item.parentId = parentId;
    });

    _this.widgetsList[0].data = items;
  }, "json");
  return false;
};

UISForm.prototype.blockForm = function (id, name) {
  var self = this;
  var d = EW.createModal({
    class: "left"
  });

  self.currentDialog = d;
  $.post('html/webroot/widgets-management/block-form.php', {
    template: self.uisTemplate,
    uisId: self.uisId,
    id: id
  }, function (data) {
    d.html(data);
  });
  return false;
};

UISForm.prototype.addPanel = function (containerId) {
  var self = this;
  $("#items-list").stop().animate({
    left: "-400px"
  }, 300);

  var panelFormDialog = EW.createModal({
    class: "left"
  });

  $.post('html/webroot/widgets-management/uis-panel.php', {
    template: self.uisTemplate,
    uisId: self.uisId,
    containerId: containerId
  }, function (data) {
    panelFormDialog.html(data);
  });
};

UISForm.prototype.editPanel = function (pid, containerId) {
  var self = this;
  var panelFormDialog = EW.createModal({
    class: "left"
  });

  $.get('html/webroot/widgets-management/uis-panel.php', {
    template: self.uisTemplate,
    uisId: self.uisId,
    panelId: pid,
    containerId: containerId
  }, function (data) {
    panelFormDialog.html(data);
  });
};

UISForm.prototype.widgetForm = function (widgetType, parentId, feederType) {
  var self = this;

  $("#items-list").stop().animate({
    left: "-400px"
  }, 300);

  var d = EW.createModal({
    class: "center"
  });

  self.currentDialog = d;

  $.post("html/webroot/widgets-management/layouts/widget-form/component.php", {
    template: self.uisTemplate,
    widgetType: widgetType,
    feederType: feederType,
    uisId: self.uisId,
    panelId: parentId
  }, function (data) {
    d.html(data);
  });

  return false;
};

function scale(full, target, base) {
  //console.log(target + " " + full + " " + base)
  return ((target / full) * 100) / 100;
}

function scaleBy(target, ratio) {
  return Math.floor(target * ratio);
}

UISForm.prototype.editWidget = function (wId) {
  var self = this;
  var widgetFormDialog = EW.createModal({
    class: "left big"
  });

  self.currentDialog = widgetFormDialog;
  var widget = self.getEditorItem(wId);
  $.post("html/webroot/widgets-management/layouts/widget-form/component.php", {
    template: self.uisTemplate,
    widgetId: wId,
    widgetType: widget.attr("data-widget-type"),
    uiStructureId: self.uisId
  }, function (data) {
    widgetFormDialog.html(data);
  });
};

UISForm.prototype.removeWidget = function (wId) {
  var uisForm = this;
  if (confirm("Do you really want to remove this Widget?")) {
    uisForm.getEditorItem(wId).data("container").remove();
    this.inspectorEditor.trigger("refresh");
  }
};

UISForm.prototype.removePanel = function (wId) {
  if (confirm("Do you really want to remove this Panel?")) {
    $("#fr").contents().find("body #base-content-pane div[data-panel-id='" + wId + "']").remove();
    this.inspectorEditor.trigger("refresh");
  }
};

function setView() {
  $('#fr').contentDocument.getElementById('dynamicStyle').innerHTML = $('#style').value;
  $('#fr').contentDocument.getElementById('<?php echo $name ?>').className = 'Panel <?php echo $class ?> ' + $('#class').value;
  $('#classValue').innerHTML = 'Panel <?php echo $class ?> ' + $('#class').value;
}

UISForm.prototype.resizeEditorFrame = function (simWidth, width) {
  this.simulatorWidth = simWidth;
  this.editorFrame = $(document.getElementById("fr")).contents().find("body");
  var self = this;
  var left = (($("#uis-editor-preview-pane").width() - width) / 2);
  //var width = $(window).width() - sidebarWidth;
  self.editorFrame.find(".widget-glass-pane").hide();
  //console.log(self.editorFrame)
  $("#editor-container").stop().animate({
    left: left,
    width: width
  }, 500, "Power1.easeInOut", function () {
    //self.loadInspectorEditor();
    self.editorFrame.find(".widget-glass-pane").show();
  });

  var fh = self.editorWindow.outerHeight();
  $("#neuis").css("height", fh);
  var newHeight = (fh * this.simulatorWidth) / width;
  var leftOffset = width - this.simulatorWidth;
  var topOffset = fh - newHeight;

  $("#fr").stop().animate({
    //left: left,
    width: this.simulatorWidth - 2,
    transform: "scale(" + scale(this.simulatorWidth - 2, width) + ")",
    top: topOffset / 2,
    left: leftOffset / 2,
    height: newHeight
  }, 500, "Power1.easeInOut", function () {
    //self.loadInspectorEditor();
    self.editorFrame.find(".widget-glass-pane").show();
    self.relocateGlassPanes();
  });
};

EW.setHashParameter("screen", null, "neuis");
EW.addURLHandler(function () {
  var screen = EW.getHashParameter("screen", "neuis");
  var sidebarWidth = 430;
  var windowWidth = $("#uis-editor-preview-pane").width();

  var defScreen = "large";
  var left = sidebarWidth;
  var width = $("#uis-editor-preview-pane").width();
  var simWidth = 1920;
  if (screen === "normal" /*&& windowWidth >= 1100*/)
  {
    defScreen = "normal";
    //left = ((windowWidth - 1100) / 2) + sidebarWidth;
    width = 1100;
    simWidth = 1100;
  }
  if (screen === "tablet" /*&& windowWidth >= 800*/)
  {
    defScreen = "tablet";
    //left = ((windowWidth - 800) / 2) + sidebarWidth;
    width = 800;
    simWidth = 800;
  }
  if (screen === "mobile" /*&& windowWidth >= 420*/)
  {
    defScreen = "mobile";
    //left = ((windowWidth - 420) / 2) + sidebarWidth;
    width = 420;
    simWidth = 420;
  }

  if (defScreen === "large")
  {
    screen = "large";
    width -= 10;
    simWidth = 1920;
    //left = sidebarWidth;
    //width = windowWidth;
  }

  if (windowWidth < width) {
    width = windowWidth;
  }

  if (uisForm.oldScreem !== screen) {
    uisForm.resizeEditorFrame(simWidth, width);
  }

  uisForm.oldScreen = screen;

  if (!$("input[value='" + screen + "']").is(":checked"))
  {
    $("input[value='" + screen + "']").click();
    $("input[value='" + screen + "']").prop("checked", true);
  }

  return "NEUISHandler";
}, "neuis");
