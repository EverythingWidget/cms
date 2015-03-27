<?php
session_start();
//include_once $_SESSION['ROOT_DIR'] . '/config.php';
//include_once 'WidgetsManagementCore.php';
?>


<div class="header-pane row">
   <h1 id="form-title" class="col-xs-12">
      <span>tr{New}</span>tr{Layout Structure}
   </h1>  
</div>
<div id="ew-uis-editor" class="form-content row" style="padding:0px;">
   <div class="list-modal" style="width:300px;z-index:3;background-color:#fff;left:-300px;" id="items-list">      
      <h1 class="pull-left">Select an item</h1><a href='javascript:void(0)' class='close-icon pull-right' style="margin:5px;"></a>      
      <div  id="items-list-content" ></div>
   </div>
   <ul id="ew-uis-editor-tabs" class="nav nav-tabs xs-nav-tabs nav-sm" style="width:300px;float:left;margin: 0px;">
      <li class="active"><a href="#inspector" data-toggle='tab'>tr{Structure}</a></li>
      <li class="disable"><a href="#template-control" data-toggle='tab'>tr{Template}</a></li>
      <li class=""><a href="#pref" data-toggle='tab'>tr{Settings}</a></li>
   </ul>
   <div class="tab-content fix" style="width:300px;top:40px;">
      <div class="tab-pane active" id="inspector">
         <form name="inspector-editor" id="inspector-editor" onsubmit="return false">

            <ul class="items" >
            </ul>

         </form>
      </div>
      <div class="tab-pane col-xs-12" id="template-control">
         <form id="template_settings_form">

         </form>
      </div>
      <div class="tab-pane col-xs-12" id="pref">
         <form id="uis-preference" onsubmit="return false;">
            <div class="row mar-top">
               <div class="col-xs-12" >
                  <input class="text-field" data-label="UIS Name" name="name" id="name">
               </div>
            </div>

            <div class="row">
               <div class="col-xs-12" >
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                     <label class="btn btn-default " data-tooltip="Use this layout as default layout for all the pages">
                        <input type="checkbox" name="uis-default" id="uis-default" value="true" > Default UIS
                     </label>
                     <label class="btn btn-default" data-tooltip="Use this layout as home page layout">
                        <input type="checkbox" name="uis-home-page" id="uis-home-page"  value="true" > Home Page UIS
                     </label>
                  </div>
               </div>
            </div>
            <div class="row mar-top">
               <div class="col-xs-12" >

                  <select data-label="UIS Template" id="template" name="template">
                     <option value="">---</option>
                     <?php
                     global $EW;
                     $templates = json_decode($EW->get_templates(), true);
                     //print_r($templates);
                     foreach ($templates as $t)
                     {
                        ?>
                        <option value="<?php echo $t["templatePath"] ?>"><?php echo $t["templateName"] ?></option>
                        <?php
                     }
                     ?>
                  </select>

               </div>
            </div>

            <div id="uis-preference-actions" class="actions-bar action-bar-items" ></div>
         </form>
      </div>
   </div>
   <div style="position:absolute;right:15px;top:0px;overflow:hidden;left:315px;" >
      <input style="margin-top:20px;" class="text-field" data-label="UIS Perview URL" name="perview_url" id="perview_url">
   </div>
   <div id="editor-window" style="position:absolute;right:0px;top:62px;bottom:0px;overflow:hidden;left:300px;border:0px solid #ccc;">
      <form id="neuis" style="overflow:auto;display:block;height:100%;width: 100%;z-index:0;" class="col-xs-12">
         <iframe id="fr" style="position:absolute;top:0px;right:0px;bottom:0px;left:0px;border:none;min-width:410px;width:100%;height:100%;overflow:scroll;background-image: url('./templates/default/glass-pane-bg-small.png');padding:0px;"                               
                 src="">
         </iframe>
         <input type="submit" style="display: none;" value="ثبت">
      </form>
   </div>
</div>
<div class="footer-pane row actions-bar action-bar-items">
   <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 pull-right" >
      <div class="btn-group btn-group-justified" data-toggle="buttons">
         <label class="btn btn-default ">
            <input type="radio" name="screen" id="hidden-on" value="mobile" onchange="EW.setHashParameter('screen', 'mobile', 'neuis')"> Mobile
         </label>
         <label class="btn btn-default ">
            <input type="radio" name="screen" id="hidden-on" value="tablet" onchange="EW.setHashParameter('screen', 'tablet', 'neuis')"> Tablet
         </label>
         <label class="btn btn-default ">
            <input type="radio" name="screen" id="hidden-on" value="normal" onchange="EW.setHashParameter('screen', 'normal', 'neuis')"> Normal
         </label>
         <label class="btn btn-default ">
            <input type="radio" name="screen" id="hidden-on" value="large" onchange="EW.setHashParameter('screen', 'large', 'neuis')"> HD
         </label>
      </div>
   </div>
</div>
<?php
//}
//}
// Begin of UI
?>
<div id="editor-css" style="display:none;">
   .focus-current-item {/*background-color:rgba(0,0,0,.6);*/}
   .focus-current-item .current-item, .current-item  {/*  visibility:hidden;  */}
   .current-element
   {
   border:1px solid #3cf;
   border-radius:0px;
   background-color:rgba(255,255,255,.1);
   z-index:15;
   position:absolute;
   display:none;
   box-shadow:0px 0px 10px 0px rgba(0,0,0,.5)
   }
   .highlight
   {
   box-shadow:0px 0px 10px 10px rgba(0,0,0,.8);
   outline:1px solid #fff;
   outline-offset:0px;
   opacity:1;
   z-index:15;
   }

   .widget-glass-pane
   {
   position:absolute;
   border: 1px solid #222;
   outline: 1px dashed #ddd;
   outline-offset: -1px;
   z-index:50;
   }

   /*.widget-glass-pane:hover
   {
   border:1px solid rgba(255,255,255,.5);
   outline:none;
   box-shadow:0px 4px 15px 0px rgba(0,0,0,.85);
   background-color:rgba(255,255,255,.2);
   }*/

   .widget-glass-pane .btn{float:left;margin:5px 0px 0px 5px;display:none;}
   .widget-glass-pane:hover .btn{display:block;}

   .wrapper
   {
   overflow:hidden;
   }

   .blue-shadow
   {
   background-color:#ddd;
   box-shadow: 0px 0px 5px 4px #3bd;
   }
</div>
<script  type="text/javascript">

   function UISForm()
   {
      var self = this;
      this.currentDialog;
      this.dpPreference = null;
      this.uisId;
      this.uisTemplate = "";
      self.oldStructure = "{}";
      this.inlineEditor = {};
      /*this.bAdd = EW.addAction("Save and Start Editing", this.addUIS, {
       display: "none"
       });*/
      this.bSaveChanges = EW.addAction("Save Layout", $.proxy(this.updateUIS, this)).hide().addClass("btn-success");
      this.bPreview = EW.addAction("Preview Layout", $.proxy(this.previewLayout, this)).hide().addClass("btn-info");
      //this.bApply = EW.addAction("Apply Changes", this.editUIS, {display: "none"});
      //this.bAddPanel = EW.addAction("Add Block", $.proxy(this.showBlocksList, this, 0));
      //this.bSettings = EW.addAction("Preferences", $.proxy(this.preferencePanel, this));
      this.bDelete = EW.addAction("Delete", $.proxy(this.deleteUIS, this), {
         display: "none"
      });
      this.bSaveAndStart = EW.addAction("Save And Start Editing", $.proxy(this.addUIStructure, this), {
         display: "none"
      });
      this.bSavePref = EW.addAction("Save Changes", $.proxy(this.updateUIS, this, true), {
         display: "none"
      }, "uis-preference-actions").addClass("btn-success");

      //this.bExportUIS = EW.addActivity({title: "tr{Export UIS}", activity: "app-admin/WidgetsManagement/export_uis", hash: {uis_id: this.uisId}});
      if (EW.getActivity({activity: "app-admin/WidgetsManagement/export_uis"}))
      {
         $("#uis-preference-actions").append("<a class='btn btn-link pull-right export-btn' href=app-admin/WidgetsManagement/export_uis?uis_id=" + this.uisId + ">Export Layout</a>");
         this.bExportLayout = $("#uis-preference-actions a.export-btn");
         this.bExportLayout.hide();
      }
      $("#perview_url").EW().inputButton({title: "Apply", id: "set_url_btn", onClick: self.reloadFrame});

      // Add close action to the items list
      $("#items-list a.close-icon").on("click", function (e) {
         e.preventDefault();
         $("#items-list").animate({left: -300}, 200);
      });

      // Add 'Add Block' button to the inspector-panel
      var addBlockBtn = $("<button type='button' class='btn btn-primary btn-sm'>tr{Add Block}</button>");
      addBlockBtn.css({float: "none", margin: "0px auto", marginBottom: "10px", display: "block"});
      addBlockBtn.on("click", $.proxy(this.blockForm, this, null));
      $("#inspector-editor").append(addBlockBtn);

      //Add refresh event to inspector editor
      $("#inspector-editor").off("refresh");
      $("#inspector-editor").on("refresh", function () {
         self.loadInspectorEditor();
      });

      this.hEditor = {
      };

      // Load inspector editor when the content of frame has been loaded
      $("#fr").load(function () {
         //self.editor = document.getElementById("fr").contentWindow;
         EW.unlock($("#editor-window"));
         //alert("ssss");
         $(document.getElementById("fr").contentDocument.head).append("<style id='editor-style'>" + $("#editor-css").html() + "</style>");
         //$("#inspector_data").trigger("refresh");
         self.oldStructure = self.createContentHeirarchy();
         $("#template").off("change");
         $("#template").change($.proxy(self.reloadFrame, self));
         self.changeTemplate();
      });

      // Adjust the width of preview window accorfing to the screen resolution
      $(window).resize(function () {
         var eww = $(window).width() - 300;
         var screen = "large";
         if (eww >= 420)
         {
            screen = "mobile";
         }
         if (eww >= 800)
         {
            screen = "tablet";
         }
         if (eww >= 1100)
         {
            screen = "normal";
         }
         if (eww >= 1360)
         {
            screen = "large";
         }
         EW.setHashParameter("screen", screen, "neuis");
      });

      // Destroy preference modal on close
      $.EW("getParentDialog", $("#ew-uis-editor")).on("close", function ()
      {
         if (self.dpPreference)
            self.dpPreference.trigger("destroy");
      });
      $.EW("getParentDialog", $("#ew-uis-editor")).on("beforeClose", function () {
         //console.log(self.oldStructure + "  " + self.createContentHeirarchy());
         if (self.oldStructure !== self.createContentHeirarchy())
         {
            return confirm("tr{You have unsaved changes. Are you sure you want to close?}");
         }
         else
         {
            return true;
         }
      });

      $("#uis-preference").on("refresh", function (e, data)
      {
         if (data.id)
         {
            //EW.setFormData("#inspector_data", data);
            $('#form-title').html('<span>tr{Edit}</span>' + data.name);
            //alert(JSON.stringify(data));
            self.uisId = data.id;
            $("#uis-preference-actions .export-btn").attr("href", "app-admin/WidgetsManagement/export_uis?uis_id=" + self.uisId);
            self.uisTemplate = data.template;
            if (data.template_settings)
               self.templateSettings = $.parseJSON(data.template_settings);
            else
               self.templateSettings = {};
            EW.setFormData("#template_settings_form", self.templateSettings);
            self.reloadFrame();
            self.readTemplateClassAndId();
         }
         self.init();
      });
      self.relocateGlassPanes();
   }

   UISForm.prototype.setTemplateSettings = function (settings)
   {
      if (typeof settings == "string")
      {
         settings = $.parseJSON(settings);
      }
      this.templateSettings = settings;
   }

   UISForm.prototype.previewLayout = function ()
   {
      window.open('<?php echo EW_ROOT_URL ?>' + '?_uis=' + this.uisId + '&editMode=true');
   }

   UISForm.prototype.clearEditor = function ()
   {
      $(document.getElementById("fr").contentDocument.body).find("#editor-glass-pane").remove();
      $(document.getElementById("fr").contentDocument.body).find(".panel-overlay").remove();
      $(document.getElementById("fr").contentDocument.body).find(".panel-glass-overlay").remove();
      $(document.getElementById("fr").contentDocument.body).find(".widget-overlay").remove();
      $(document.getElementById("fr").contentDocument.body).find("#base-content-pane .panel").css({
         paddingBottom: "-=50px"
      });
   };


   UISForm.prototype.createInspector = function (element, init)
   {
      var self = this;
      var frameBody = $(document.getElementById("fr").contentDocument.body);
      //var editorGlassPane = frameBody.children("#editor-glass-pane");

      var children = $(element).children();
      if (init)
      {
         children = $(element).find("[data-block]:not([data-not-editable] [data-block])");
      }
      var result = new Array();
      var skipBoxBlock = false;
      var skipChildren = false;
      $.each(children, function (k, v)
      {
         v = $(v);
         var div = $("<div></div>");
         if (v.hasClass("panel") || v.hasClass("block"))
         {
            var liUl = $("<li><div><a href='#' class='item-label'><img src='./templates/default/panel-icon.png' class='handle'/></a><a href='#' class='btn btn-primary add-item'>Add</a><a href='#' class='close-icon' ></a></div></li>");
            liUl.attr("data-linked-panel-id", v.attr("data-panel-id"));
            skipBoxBlock = false;
            /*if (v.hasClass("row") && v.parent().hasClass("panel"))
             {
             liUl.find(".item-label").append("Box Block");
             //Set box block id for the box 
             skipBoxBlock = true;
             //alert("asd");
             }
             else*/ if (v.hasClass("row"))
            {
               liUl.find(".item-label").append("Block");
               liUl.find("img").attr("src", "./templates/default/block-icon.png");
               liUl.addClass("block");
               liUl.find(".item-label").click(function (e) {
                  self.blockForm(v.attr('data-panel-id'));
                  e.preventDefault();
               });
            }
            else if (v.children(".row").length > 0)
            {
               liUl.find(".item-label").append("Panel");
               liUl.addClass("panel");
               // Set data link panel id for the panel
               liUl.attr("data-linked-panel-id", v.attr('data-panel-id'))
               self.lastItem = liUl;

               liUl.find(".item-label").click(function (e) {
                  self.editPanel(v.attr('data-panel-id'), v.attr('data-container-id'));
                  e.preventDefault();
               });
               //skipBoxBlock = true;
               skipChildren = true;
            }
            else
            {
               liUl.find(".item-label").append("Panel");
               liUl.addClass("panel");
               liUl.find(".item-label").click(function (e) {
                  self.editPanel(v.attr('data-panel-id'), v.attr('data-container-id'));
                  e.preventDefault();
               });
            }

            // Add widget button for panels
            var addItem = liUl.find(".add-item");
            addItem.click($.proxy(self.showWidgetsList, self, v.attr('data-panel-id')));
            addItem.hover(function () {
               liUl.addClass("highlight");
            }, function () {
               liUl.removeClass("highlight");
            });

            // Remove button
            liUl.find(".close-icon").click(function (e) {
               e.preventDefault();
               self.removePanel(v.attr('data-panel-id'));
            });

            liUl.find("div").hover(function () {
               var panel = frameBody.find("[data-panel-id='" + v.attr('data-panel-id') + "']");
               // Scroll to the panel if the panel is not in view port
               if (panel.offset().top > (frameBody.scrollTop() + frameBody.innerHeight())
                       || panel.offset().top + panel.outerHeight() < frameBody.scrollTop())
               {
                  frameBody.stop().animate({
                     scrollTop: panel.offset().top
                  },
                  'slow');
               }

               self.currentElementHighlight.css({
                  top: panel.offset().top,
                  left: panel.offset().left,
                  position: "absolute",
                  width: panel.outerWidth(),
                  height: panel.outerHeight(),
                  margin: "0px"
               });
               self.currentElementHighlight.show();
               addItem.stop().fadeIn(300);
               //console.log("a");

            }, function () {
               self.currentElementHighlight.hide();
               addItem.hide();

            });

            var ul = $("<ul></ul>");
            ul.append(self.createInspector(v));
            // Skip adding panel block to the editor
            if (skipBoxBlock)
            {
               $(self.lastItem).find(".add-item").unbind("click").click(function (e) {
                  e.preventDefault();
                  self.showWidgetsList(v.attr('data-panel-id'));
               });
//$($this.lastItem).find("ul").remove();
               $(self.lastItem).append(ul);
               self.lastItem = null;
               //$this.lastItem = null;
            }
            else
            {
               if (skipChildren)
               {
                  skipChildren = false;
                  ul.html(self.createInspector(v.children().eq(0)));
                  $(liUl).append(ul);
               }
               else
                  $(liUl).append(ul);
               result.push(liUl);
            }
         }
         if (v.hasClass("widget-container"))
         {
            v.find(".widget-glass-pane").remove();
            var widgetGlassPane = $("<div class='widget-glass-pane'>");
            /*if (v.children().css("overflow") == "visible")
             {
             v.children().css("overflow", "hidden");
             //alert(v.children().offset().top - parseInt(v.children().css("margin-top"),10));
             widgetGlassPane.css({top: v.children().position().top + parseInt(v.children().css("margin-top"), 10), left: parseInt(v.children().css("margin-left"), 10) + parseInt(v.css("padding-left"), 10), width: v.children().outerWidth(), height: v.children().outerHeight()});
             v.children().css("overflow", "");
             // alert(v.children().css("overflow") );
             }
             else
             widgetGlassPane.css({top: v.children().position().top + parseInt(v.children().css("margin-top"), 10), left: parseInt(v.children().css("margin-left"), 10) + parseInt(v.css("padding-left"), 10), width: v.children().outerWidth(), height: v.children().outerHeight()});*/
            widgetGlassPane.data("widget-element", v);
            frameBody.append(widgetGlassPane);

            var editWidget = function (e) {
               self.editWidget(v.children().attr('data-widget-id'));
               e.preventDefault();
            };

            var li = $("<li class='widget'><div><a href='#' class='item-label'><img src='./templates/default/widget-icon.png' class='handle'/></a><a href='#' class='close-icon' ></a></div></li>");
            li.attr("data-linked-widget-id", v.children().attr("data-widget-id"));

            li.find(".item-label").append(/*v.children().data("widget-id") +*/ v.children().attr("data-widget-title"));
            li.find(".item-label").click(editWidget);

            li.find(".close-icon").click(function (e) {
               e.preventDefault();
               self.removeWidget(v.children().attr('data-widget-id'));
            });

            //var sizeBtn = $("<ul class=list><li><button type='button' class='btn btn-info'>Size & Layout</button></li></ul>");
            //EW.getActivity({activity: "app-admin/WidgetsManagement/import_uis"})
            //widgetGlassPane.append(sizeBtn);
            //var size = widgetGlassPane.EW().createDropMenu({parent: "#editor-window", eventParent: frameBody});
            //$.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/uis-widget-size-layout.php', {},
            //function (data) {
            //size.children().eq(0).html(sizeBtn);

            //});
            //}

            var inlineEditor = self.inlineEditor[v.children().attr('data-widget-id')];

            if (inlineEditor)
            {
               inlineEditor.css({fontSize: "24px", position: "absolute", top: v.children().offset().top, left: v.children().offset().left, backgroundColor: "rgba(255,255,255,.8)"});
               frameBody.find("#editor-glass-pane").append(inlineEditor);
            }
            // Show bloack glass on hover for widget

            //var widgetClone = widget.clone();
            li.find("a").hover(function ()
            {
               var widget = frameBody.find("[data-widget-id='" + v.children().attr('data-widget-id') + "']");
               // Scroll to the widget if the panel is not in view port
               if (widget.offset().top > (frameBody.scrollTop() + frameBody.innerHeight())
                       || widget.offset().top + widget.outerHeight() < frameBody.scrollTop())
               {
                  frameBody.stop().animate({scrollTop: widget.offset().top}, 500);
               }
               //widgetClone.addClass("highlight");
               self.currentElementHighlight.css({
                  top: widget.offset().top,
                  left: widget.offset().left,
                  position: "absolute",
                  width: widget.outerWidth(),
                  height: widget.outerHeight()
               });
               self.currentElementHighlight.show();


            }, function () {

               self.currentElementHighlight.hide();

            });
            result.push(li);
         }
      });
      return result;
   };

   UISForm.prototype.loadInspectorEditor = function ()
   {
      var self = this;
      self.editor = document.getElementById("fr").contentWindow;
      var frameBody = $(document.getElementById("fr").contentDocument.body);
      var parentNode = frameBody.find("#base-content-pane");
      var panelIndex = 1;
      var widgetIndex = 1;

      $.each(parentNode.find(".block,.panel,.widget"), function (i, e)
      {
         e = $(e)
         //console.log(e.attr("data-panel-id"));
         if (e.is(".panel") || e.is(".block"))
         {
            if (!e.attr("data-panel-id"))
               e.attr("data-panel-id", "panel-" + panelIndex);
            panelIndex++;
         }
         if (e.is(".widget"))
         {
            if (!e.attr("data-widget-id"))
               e.attr("data-widget-id", "widget-" + widgetIndex);
            widgetIndex++;
         }
      });
      var inspectorEditorList = $("#inspector-editor > .items");
      inspectorEditorList.empty();

      // Add div to create glass effect to make the iframe content unselectable
      frameBody.find("#editor-glass-pane").remove();
      var editorGlassPane = $("<div>");
      editorGlassPane.css({
         position: "fixed",
         top: "0px",
         left: "0px",
         width: "100%",
         height: "100%",
         zIndex: 12
      });
      editorGlassPane.attr("id", "editor-glass-pane");
      //frameBody.append(editorGlassPane);

      // Add a div to represent the highlight of current element
      frameBody.find("div.current-element").remove();
      this.currentElementHighlight = $("<div class='current-element'></div>");
      frameBody.append(this.currentElementHighlight);

      inspectorEditorList.append(self.createInspector(parentNode, true));
      var oldContainer;
      var oldIndex;
      inspectorEditorList.sortable({
         handle: 'img.handle',
         isValidTarget: function (item, container) {
            //alert(item.attrclass"));
            if (item.hasClass("block") && !container.el.is(".items"))
            {
               return false;
            }
            if (item.hasClass("widget") && container.el.is(".items"))
            {
               //console.log(item.index() + "  " + container.el.children().eq(item.index() - 1).hasClass("block"));
               return false;
            }
            if (item.hasClass("panel") && container.el.is(".items"))
            {
               //console.log(item.index() + "  " + container.el.children().eq(item.index() - 1).hasClass("block"));
               return false;
            }
            //console.log(container.options.group)
            if (oldContainer)
               oldContainer.removeClass("highlight");
            container.el.parent().addClass("highlight");
            oldContainer = container.el.parent();
            //console.log(container);
            return true;
         },
         onDrop: function (item, container, _super) {
            frameBody = $(document.getElementById("fr").contentDocument.body);
            if (!container)
            {

               return;
            }
            var linkedParentId = container.el.parent().attr("data-linked-panel-id");
            var linkedPanelId = item.attr("data-linked-panel-id");
            var linkedWidgetId = item.attr("data-linked-widget-id");
            oldContainer.removeClass("highlight");
            var parent = frameBody.find("[data-panel-id='" + linkedParentId + "']");
            var baseContentPane = frameBody.find("#base-content-pane");

            if (!parent.attr("data-block"))
            {
               parent = parent.children().eq(0);
            }
            if (parent.length <= 0)
            {
               var panel = frameBody.find("[data-panel-id='" + linkedPanelId + "']").detach();
               if (baseContentPane.children().length <= item.index())
               {
                  baseContentPane.append(panel);
               }
               else
               {
                  baseContentPane.children().eq(item.index()).before(panel);
               }
               _super(item);
               return;
            }

            if (linkedWidgetId)
            {
               //alert(linkedWidgetId);
               var widget = frameBody.find("[data-widget-id='" + linkedWidgetId + "']").parent().detach();

               if (parent.children().length <= item.index())
               {
                  parent.append(widget);
               }
               else
               {
                  parent.children().eq(item.index()).before(widget);
               }
            }
            if (linkedPanelId)
            {
               var panel = frameBody.find("[data-panel-id='" + linkedPanelId + "']").detach();
               if (parent.length == 0)
               {
                  parent = baseContentPane;
               }
               if (parent.children().length <= item.index())
               {
                  parent.append(panel);
               }
               else
               {
                  parent.children().eq(item.index()).before(panel);
               }
            }

            _super(item);
         }
      });
   };

   /**
    * Relocate all the widget's glasspanes every half a second to keep them over their corresponding widget
    
    */
   UISForm.prototype.relocateGlassPanes = function ()
   {
      var self = this;
      if (document.getElementById("fr"))
         $.each($(document.getElementById("fr").contentDocument.body).find(".widget-glass-pane"), function (i, el)
         {
            //var widgetContainer = $(el).parent();
            var widgetContainer = $(el).data("widget-element");
            var widget = widgetContainer.children("[data-widget]");
            $(el).css({top: widget.offset().top,
               left: widget.offset().left,
               width: widget.outerWidth(), height: widget.outerHeight()});

         });
      setTimeout(function () {
         self.relocateGlassPanes();
      }, 500);
   }
   /** Create a json string from current layout structure heirarchy 
    * 
    
    * @returns {String} */
   UISForm.prototype.createContentHeirarchy = function ()
   {
      var self = this;
      if (!self.editor)
         return {};
      var panels = $("#fr").contents().find("body #base-content-pane").find("[data-block]:not([data-not-editable] [data-block])");
      var root = {
      };

      $.each(panels, function (i, v)
      {
         v = $(v).clone();
         v.removeClass("panel");
         v.removeClass("block");
         root[i] = {
            "type": "panel",
            "class": v.prop("class"),
            "id": v.attr("id"),
            "panelParameters": v.attr("data-panel-parameters"),
            //"blockName": v.attr("data-block-name"),
            "children": self.readPanels(v)
         };
      });
      //alert(JSON.stringify(root));
      return JSON.stringify(root);
   };

   UISForm.prototype.readPanels = function (elm)
   {
      var self = this;
      var child = {
      };
      var index = 0;
      $.each($(elm).children("[data-panel],[data-widget-container]"), function (i, v) {
         v = $(v).clone();

         if (v.attr("data-panel"))
         {
            v.removeClass("panel");
            child[index++] = {
               "type": "panel",
               "class": v.prop("class"),
               "id": v.attr("id"),
               "panelParameters": v.attr("data-panel-parameters"),
               // Read the childeren of the panel
               "children": self.readPanels(v.children().eq(0))
            };
         }
         else if (v.attr("data-widget-container"))
         {
            v.removeClass("widget-container");
            var w = $(v).children(".widget");
            w.removeClass("widget");
            //alert(w.prop("class"));
            child[index++] = {
               "type": "widget",
               "class": v.prop("class"),
               widgetClass: w.prop("class"),
               "id": w.attr("id"),
               "widgetType": w.attr("data-widget-type"),
               "widgetParameters": self.editor.EW.widget_data[w.attr("data-widget-id")]
            };
         }

      });

      return child;
   };

   /**
    * Return the widget with the specified id
    * @param {string} id The widget id
    * @returns {jQuery}    */
   UISForm.prototype.getEditorItem = function (id)
   {
      var item = $("#fr").contents().find("body").find("div[data-widget-id='" + id + "']");
      item.data("container", item.parent());
      return item;
   };

   UISForm.prototype.getEditor = function ()
   {
      return $("#fr").contents();
   };

   UISForm.prototype.getLayoutBlocks = function ()
   {
      var items = $("#fr").contents().find("body [data-block]:not([data-not-editable] [data-block])");
      //item.data("container", item.parent());
      return items;
   };

   UISForm.prototype.getLayoutWidgets = function ()
   {
      var items = $("#fr").contents().find("body [data-widget]:not([data-not-editable] [data-widget])");
      //item.data("container", item.parent());
      return items;
   };

   UISForm.prototype.init = function ()
   {
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
      }
      else
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
   UISForm.prototype.changeTemplate = function ()
   {
      var self = this;
      if ($("#template").val())
      {
         $.post("<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/get_template_settings_form", {
            path: $("#template").val()
         },
         function (data)
         {
            self.uisTemplate = $("#template").val();
            $("#template_settings_form").off("getData");
            $("#template_settings_form").empty();
            $("#template_settings_form").html(data);
            EW.setFormData("#template_settings_form", self.templateSettings);
            self.updateTemplateBody();
         });
      }
   }


   UISForm.prototype.readTemplateClassAndId = function ()
   {
      /*$("#cai").html("");
       if ($("#template").val())
       $.post("<?php echo EW_ROOT_URL; ?>app-admin/EWCore/parse_css", {
       path: $("#template").val() + "/template.css"
       },
       function (data)
       {
       $.each(data, function (k, v) {
       $("#cai").append(v + "<br>");
       });
       }, "json");*/
   };

   UISForm.prototype.addUIStructure = function ()
   {
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
      self.templateSettings = $("#template_settings_form").serializeJSON();
      $("#template_settings_form").trigger("getData");

      $.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/add_uis', {
         name: $('#name').val(),
         template: $('#template').val(),
         template_settings: JSON.stringify(self.templateSettings),
         defaultUIS: defaultUIS,
         homeUIS: homeUIS
      },
      function (data) {
         //EW.unlock(self.dpPreference);
         $("body").EW().notify(data).show();
         self.uisTemplate = $('#template').val();
         self.uisId = data.uisId;

         EW.setHashParameters({"uis-id": self.uisId, cmd: "edit-uis"});
         $(document).trigger("uis-list.refresh");
         self.reloadFrame();
         self.init();
         //self.dpPreference.dispose();
         //uisList.listUIStructures();

      }, "json");
   };

   UISForm.prototype.updateUIS = function (reload)
   {
      var self = this;
      $('#name').removeClass("red");
      if (!$('#name').val())
      {
         $('#name').addClass("red");
         return;
      }
      //alert("ajiiiiiiibe");
      EW.lock(self.dpPreference, "Saving...");
      EW.lock($("#ew-uis-editor"));

      var structure = self.createContentHeirarchy();
      var defaultUIS = $("#uis-default").is(":checked");
      var homeUIS = $("#uis-home-page").is(":checked");
      self.templateSettings = $("#template_settings_form").serializeJSON();
      //if(!self.templateSettings)
      //alert(JSON.stringify(self.templateSettings));
      $("#template_settings_form").trigger("getData");
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/update_uis', {
         name: $('#name').val(),
         template: $('#template').val(),
         template_settings: JSON.stringify(self.templateSettings),
         perview_url: $("#perview_url").val(),
         structure: structure,
         uisId: self.uisId,
         defaultUIS: defaultUIS,
         homeUIS: homeUIS
      },
      function (data) {
         //EW.unlock(self.dpPreference);
         EW.unlock($("#ew-uis-editor"));
         $("body").EW().notify(data).show();
         uisList.listUIStructures();
         $('#form-title').html("<span>Edit</span> " + data.data.title);

         if (reload === true)
         {
            self.reloadFrame();
         }
         else
            self.oldStructure = self.createContentHeirarchy();

      }, "json");
   };

   UISForm.prototype.updateTemplateBody = function ()
   {
      // Update template body with current template settings
      var self = this;
      EW.lock($("#editor-window"));

      //var originalTemplateSettings = self.templateSettings;
      // Read template settings from template settings form
      $("#template_settings_form").trigger("getData");
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/get_layout', {
         uisId: self.uisId,
         template: self.uisTemplate,
         template_settings: JSON.stringify(self.templateSettings)
      },
      function (data) {
         EW.unlock($("#editor-window"));
         //console.log(data);
         var myIframe = document.getElementById("fr");
         //$(myIframe.contentWindow.document).off();
         $("#fr").contents().find("body").off();
         $("#fr").contents().find("head #template-script").remove();
         $("#fr").contents().find("head #widget-data").remove();
         $("#fr").contents().find("body #base-content-pane").remove();

         $("#fr").contents().find("head #template-css").attr("href", $('#template').val() + "/template.css");

         var widgetData = myIframe.contentWindow.document.createElement("script");
         widgetData.id = "widget-data";
         widgetData.innerHTML = data["widget_data"];
         myIframe.contentWindow.document.head.appendChild(widgetData);

         var templateBody = myIframe.contentWindow.document.createElement("div");
         templateBody.id = "base-content-pane";
         templateBody.className = "container";
         templateBody.innerHTML = data["template_body"];
         myIframe.contentWindow.document.body.appendChild(templateBody);

         // Adding template script after adding template body
         if (data["template_script"])
         {
            var script = myIframe.contentWindow.document.createElement("script");
            //script.type = "text/javascript";
            script.id = "template-script";
            var templateScript = $(data["template_script"]).attr("id", "template-script");
            script.innerHTML = templateScript.html();

            myIframe.contentWindow.document.head.appendChild(script);
         }

         // Find scripts inside the template body and run them
         var scripts = [];
         var ret = myIframe.contentWindow.document.body;
         findScriptTags(ret, scripts)
         for (script in scripts)
         {
            evalScript(scripts[script]);
         }

         $("#inspector-editor").trigger("refresh");

      }, "json");
   };
   function findScriptTags(element, scripts)
   {
      var ret = element.childNodes;
      if (ret)
      {
         for (var i = 0; ret[i]; i++)
         {
            if (ret[i].childNodes.length > 0)
               findScriptTags(ret[i], scripts);
            if (scripts && nodeName(ret[i], "script") && (!ret[i].type || ret[i].type.toLowerCase() === "text/javascript")) {
               scripts.push(ret[i].parentNode ? ret[i].parentNode.removeChild(ret[i]) : ret[i]);
            }
         }
      }
   }

   function nodeName(elem, name)
   {
      return elem.nodeName && elem.nodeName.toUpperCase() === name.toUpperCase();
   }

   function evalScript(elem)
   {
      var data = (elem.text || elem.textContent || elem.innerHTML || "");
      var frame = document.getElementById("fr");
      var head = frame.contentWindow.document.getElementsByTagName("head")[0] || frame.contentWindow.document.documentElement,
              script = frame.contentWindow.document.createElement("script");
      script.appendChild(frame.contentWindow.document.createTextNode(data));
      if (elem.src)
         script.src = elem.src;
      //myIframe.contentWindow.document.body.appendChild(templateBody);    
      head.insertBefore(script, head.firstChild);
      head.removeChild(script);

      if (elem.parentNode) {
         elem.parentNode.removeChild(elem);
      }
   }

   UISForm.prototype.addWidget = function (html, parentId)
   {
      var scripts = [];
      var ret = $(html)[0];
      parentId.appendChild(ret);
      findScriptTags(ret, scripts);
      for (script in scripts)
      {
         evalScript(scripts[script]);
      }
   };

   UISForm.prototype.replaceWidget = function (html, parentId)
   {
      var scripts = [];
      var ret = $(html)[0];
      parentId.parentNode.replaceChild(ret, parentId);
      findScriptTags(ret, scripts);
      for (script in scripts)
      {
         evalScript(scripts[script]);
      }
   };
   /** Set data for specified widget
    * 
    * @returns {Boolean} false id data is not a valid json format
    */
   UISForm.prototype.setWidgetData = function (widgetId, data)
   {
      try
      {
         //console.log(typeof (data));
         if (typeof (data) != 'object')
            data = $.parseJSON(data);
      }
      catch (e)
      {
         return false;
      }
      this.editor.EW.widget_data[widgetId] = data;
      console.log(this.editor.EW.widget_data);
   }

   UISForm.prototype.dispose = function ()
   {
      var self = this;
      self.bDelete.remove();
   };

   UISForm.prototype.reloadFrame = function (t)
   {
      var url = !($("#perview_url").val) ? "index.php" : $("#perview_url").val();
      EW.lock($("#editor-window"));
      //$("#inspector-panel").empty();
      $("#inspector-editor > .items").empty();
      $('#fr').attr({
         src: '<?php echo EW_ROOT_URL ?>' + url + '?_uis=' + this.uisId + '&editMode=true'
      });
   };

   /*UISForm.prototype.showBlocksList = function ()
    {
    var self = this;
    $("#items-list").stop().animate({left: "0px"}, 300);
    $("#items-list #items-list-content").html("<h2 style='text-align:center;'>Please Wait</h2>");
    $.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/get_blocks', {
    template: self.uisTemplate,
    uisId: self.uisId
    },
    function (data) {
    var items = []
    $.each(data["result"], function (k, v) {
    var e = $("<div class='text-icon'><h4>" + v["title"] + "</h4><p>" + v["description"] + "</p></div>");
    e.on("click", $.proxy(self.blockForm, self, null, v["name"]));
    items.push(e);
    });
    
    $("#items-list #items-list-content").html(items);
    }, "json");
    return false;
    };*/

   UISForm.prototype.showWidgetsList = function (parentId)
   {
      var self = this;
      //var d = EW.createModal();
      //neuis.currentDialog = d;
      var $this = this;
      $("#items-list").stop().animate({left: "0px"}, 300);
      $("#items-list #items-list-content").html("<h2 style='text-align:center;'>Please Wait</h2>");
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/get_widgets_types', {
         template: self.uisTemplate,
         uisId: self.uisId
      },
      function (data) {
         var items = [];

         // Add panel item
         var e = $("<div class='text-icon' data-label='Panel'><h4>Panel</h4><p>Add a panel</p></div>");
         e.on("click", $.proxy($this.addPanel, $this, parentId));
         items.push(e);
         $.each(data["result"], function (k, v) {
            e = $("<div class='text-icon' data-label='" + v["title"] + "'><h4>" + v["title"] + "</h4><p>" + ((v["description"]) ? v["description"] : "") + "</p></div>");
            e.on("click", $.proxy($this.widgetForm, $this, v["path"], parentId, v["feeder_type"]));
            items.push(e);
         });

         $("#items-list #items-list-content").html(items);
      }, "json");
      return false;
   };

   UISForm.prototype.blockForm = function (id, name)
   {
      var self = this;
      //$("#items-list").stop().animate({left: "-300px"}, 300);
      var d = EW.createModal({class: "left"});
      self.currentDialog = d;
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/block-form.php', {
         template: self.uisTemplate,
         uisId: self.uisId,
         id: id
      },
      function (data) {
         d.html(data);
      });
      return false;
   };

   UISForm.prototype.addPanel = function (containerId)
   {
      var self = this;
      $("#items-list").stop().animate({left: "-300px"}, 300);
      var d = EW.createModal({class: "left"});
      self.currentDialog = d;
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/UISPanel.php', {
         template: self.uisTemplate,
         uisId: self.uisId,
         containerId: containerId
      },
      function (data) {
         d.html(data);
      });
      return false;
   };

   UISForm.prototype.editPanel = function (pid, containerId)
   {
      var self = this;
      var d = EW.createModal({class: "left"});
      self.currentDialog = d;
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/UISPanel.php', {
         template: self.uisTemplate,
         uisId: self.uisId,
         panelId: pid,
         containerId: containerId
      },
      function (data) {
         d.html(data);
      });
      return false;
   };

   UISForm.prototype.widgetForm = function (widgetType, parentId, feederType)
   {
      var self = this;
      $("#items-list").stop().animate({left: "-300px"}, 300);
      var d = EW.createModal();
      self.currentDialog = d;
      $.post("<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/uis-prewidget-form.php", {
         template: self.uisTemplate,
         widgetType: widgetType,
         feederType: feederType,
         uisId: self.uisId,
         panelId: parentId
      },
      function (data) {
         d.html(data);
      });
      return false;
   };

   UISForm.prototype.editWidget = function (wId)
   {
      var self = this;
      var d = EW.createModal();
      self.currentDialog = d;
      var w = self.getEditorItem(wId);
      //console.log(w);
      EW.lock(d);
      $.post("<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/uis-prewidget-form.php", {
         template: self.uisTemplate,
         widgetId: wId,
         widgetType: w.attr("data-widget-type"),
         uiStructureId: self.uisId
      },
      function (data) {
         d.html(data);
         //uisWidget.editWidgetForm();
      });
      return false;
   };

   UISForm.prototype.removeWidget = function (wId)
   {
      var self = this;
      if (confirm("Do you really want to remove this Widget?"))
      {
         self.getEditorItem(wId).data("container").remove();
         $("#inspector-editor").trigger("refresh");
      }
      return false;
   };

   UISForm.prototype.removePanel = function (wId)
   {
      if (confirm("Do you really want to remove this Panel?"))
      {
         $("#fr").contents().find("body #base-content-pane div[data-panel-id='" + wId + "']").remove();
         $("#inspector-editor").trigger("refresh");
      }
      return false;
   };

   function setView()
   {
      //obj('<?php // echo $styleId ? $styleId : 'testDiv'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     ?>').style.cssText = obj('style').value;
      //obj('fr').contentDocument.body.innerHTML = '<link href="../templates/SpapcoDefault/template.css" rel="stylesheet" type="text/css">';
      $('#fr').contentDocument.getElementById('dynamicStyle').innerHTML = $('#style').value;
      $('#fr').contentDocument.getElementById('<?php echo $name ?>').className = 'Panel <?php echo $class ?> ' + $('#class').value;
      $('#classValue').innerHTML = 'Panel <?php echo $class ?> ' + $('#class').value;

   }

   EW.setHashParameter("screen", null, "neuis");
   EW.addURLHandler(function ()
   {
      var screen = EW.getHashParameter("screen", "neuis");
      var windowWidth = $(window).width() - 300;

      var defScreen = "large";
      var left = 0;
      var width = 0;
      if (screen == "normal" && windowWidth >= 1100)
      {
         defScreen = "normal";
         left = ((windowWidth - 1100) / 2) + 300;
         width = 1100;
      }
      if (screen == "tablet" && windowWidth >= 800)
      {
         defScreen = "tablet";
         left = ((windowWidth - 800) / 2) + 300;
         width = 800;
      }
      if (screen == "mobile" && windowWidth >= 420)
      {
         defScreen = "mobile";
         left = ((windowWidth - 420) / 2) + 300;
         width = 420;
      }

      if (defScreen == "large")
      {
         screen = "large";
         left = 300;
         width = windowWidth;
      }
      if (uisForm.oldScreem != screen)
      {
         $(document.getElementById("fr").contentDocument.body).find(".widget-glass-pane").hide();
         $("#editor-window").stop().animate({
            left: left,
            width: width
         },
         500, "Power3.easeOut", function () {
            uisForm.loadInspectorEditor();
            $(document.getElementById("fr").contentDocument.body).find(".widget-glass-pane").show();
         });
      }
      uisForm.oldScreen = screen;

      if (!$("input[value='" + screen + "']").is(":checked"))
      {
         $("input[value='" + screen + "']").click();
         $("input[value='" + screen + "']").prop("checked", true);
      }

      return "NEUISHandler";
   }, "neuis");

   $(document).ready(function ()
   {
      uisForm = new UISForm();
      EW.uisForm = uisForm;
<?php
$uis_info = EWCore::process_command("admin", "WidgetsManagement", "get_uis", ["uisId" => $_REQUEST['uisId']]);
echo 'EW.setFormData("#uis-preference",' . (($uis_info != null) ? ($uis_info) : "null") . ');';
?>

   });


   //neuis.bSettings.comeIn(300);


</script>



