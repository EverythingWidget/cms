

function EWEditor(config)
{
   var settings = $.extend({id: null}, config);
   this.editorWindow;
   this.activeElement;

   this.editorToolbar;
   this.editorContent;
   this.editorContentBody;
   this.editorContentHead;
   //this.editor = {};
   this.iFrame;
   this.editorValue;

   this.init(settings);

}

EWEditor.prototype.init = function (settings)
{
   var self = this;
   if (settings.id === null)
      return;
   this.editorWindow = $(settings.id);
   this.editorValue = this.editorWindow.html();
   this.editorWindow.empty();
   this.editorToolbar = $("<div class='col-xs-12'><div class=row></div></div>").find('.row');
   this.editorToolbar.parent().css({zIndex: 1, backgroundColor: '#e0e0e0', position: 'absolute', top: '0px', width: '100%', height: '100px', border: '0px solid #000', borderRadius: '6px'});
   this.editorContent = $("<div>");
   this.editorContent.css({position: 'absolute', top: '100px', bottom: 0, width: '100%'});
   this.iFrame = $("<iframe src='app-admin/Tools/EWEditor/editor.html'>");
   this.editorWindow.css({backgroundColor: '#fff', position: 'absolute', left: '10px', top: '5px', right: '10px', bottom: '5px'});
   this.iFrame.css({width: '100%', height: '100%', border: '0px solid #000'});
   this.editorWindow.append(this.editorToolbar.parent());
   this.editorWindow.append(this.editorContent);
   this.editorContent.html(this.iFrame);
   this.iFrame.contents().find("body").append("<div id='container' class='container'>");

   //this.editorContentHead = this.iFrame.contents().find("head");
   /* this.editorContentHead.append("<link rel='stylesheet' href='" + settings.bootstrap + "'/>");
    this.editorContentHead.append("<style>\n\
    html, body{padding:10px 2px;margin:0px} \n\
    .container{width:100%} \n\
    //p{outline:1px dashed #aaa} \n\
    .row{border:2px solid #ccc;} \n\
    .row:focus{outline:none;border-color:#888;}\n\
    .row:before {content: 'ROW';line-height: 20px;display:block;padding:0px 4px;  font-size: 11px;  font-weight: bold;}\n\
    .column{border:2px solid rgba(0,0,0,.1);padding-top:20px;min-height:44px;}\n\
    .column:focus:before{background-color:#3cf;}\n\
    .column:focus{border-color:#3cf;outline:none;}\n\
    .column:before {content: 'COLUMN';line-height: 20px;display:block;padding:0px 4px;font-size: 11px;font-weight:bold;background-color: rgba(0,0,0,.1);margin:-20px -15px 0;}\n\
    </style>");
    
    this.editorContentHead.append("<script src='app-admin/Tools/EWEditor/medium-editor.js'></script>");
    this.editorContentHead.append("<script>\n\
    setTimeout(function(){var editor = new MediumEditor('.column');},'500');\n\
    \n\
    </script>");*/

   //this.editorContentBody.html(this.editorValue);
   //alert(this.editorValue);
   this.iFrame.on('load', function ()
   {
      self.editorContentBody = self.iFrame.contents().find("#container");
      self.initPlugins(settings.plugins);
      self.initEventListeners();
   });

   //iFrame.css({height: editor.outerHeight()});

}
var EWEditorPlugin = {
   plugins: [],
   add: function (pluginName, pluginInit)
   {
      this.plugins[pluginName] = pluginInit;
   }
}
EWEditor.prototype.initPlugins = function (plugins)
{
   var self = this;
   self.addRow();
   var checkBtn = $("<button type='button' class='btn btn-info'>What is the parent</button>");
   checkBtn.on('click', function (e)
   {
      var frameWindow = self.iFrame[0].contentWindow;
      var frameDoc = self.iFrame[0].contentDocument;

      var range = frameDoc.createRange();
      var sel = frameWindow.getSelection();
      if (sel.baseNode)
         console.log(sel.baseNode.parentNode.nodeName);

   });
   //this.editorToolbar.append(checkBtn);
   var formatToolbar = $("<select class='col-xs-4' data-label='format'><option></option></select>")
   //this.editorToolbar.append(formatToolbar);

   var gridToolbar = $("<div class='btn-group'></div>");
   var addRow = $("<button type='button' class='btn btn-info btn-sm'>+Row</button>");
   var addColumn = $("<button type='button' class='btn btn-info btn-sm'>+Column</button>");
   gridToolbar.append([addRow, addColumn]);
   this.editorToolbar.append(gridToolbar);

   var extra = $("<div class='btn-group' data-toggle='buttons'><button type='button' class='btn btn-info btn-sm' data-toggle='true'>Extra</button></div>");
   this.editorToolbar.append(extra);

   var alignBtns = $("<div class='btn-group'></div>");
   var alignLeft = $("<button type='button' class='btn btn-info btn-sm'>Left</button>");
   alignLeft.on('click', function () {
      var select = self.getSelection();
      if (select.baseNode && select.baseNode.parentNode && $(select.baseNode.parentNode).not("div"))
         $(select.baseNode.parentNode).css({textAlign: "left"});
   });
   var alignCenter = $("<button type='button' class='btn btn-info btn-sm'>Center</button>");
   alignCenter.on('click', function () {
      var select = self.getSelection();
      if (select.baseNode && select.baseNode.parentNode && $(select.baseNode.parentNode).not("div"))
         $(select.baseNode.parentNode).css({textAlign: "center"});
   });
   var alignRight = $("<button type='button' class='btn btn-info btn-sm'>Right</button>");
   alignRight.on('click', function () {
      var select = self.getSelection();
      if (select.baseNode && select.baseNode.parentNode && $(select.baseNode.parentNode).not("div"))
         $(select.baseNode.parentNode).css({textAlign: "right"});
   });
   alignBtns.append([alignLeft, alignCenter, alignRight]);
   this.editorToolbar.append(alignBtns);

   this.sizeSlider = $('<input class="col-xs-12" type="text" name="col-lg-" id="col-lg-" value="12" data-slider="true" data-slider-range="1,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >');
   this.sizeSlider.on('change', function ()
   {
      var currentClass = self.activeElement.attr('class');
      currentClass = currentClass.replace(/col-xs-(\d*)/, 'col-xs-' + self.sizeSlider.val());
      self.activeElement.attr('class', currentClass);
   });
   this.editorContentBody.on('element-select', function (e, element)
   {
      if (element && element.is('.column'))
      {
         self.sizeSlider.parent().show();
         var myString = element.attr('class');
         var myRegexp = /col-xs-(\d*)/;
         var match = myRegexp.exec(myString);
         //alert(match[1]);  // abc
         self.sizeSlider.val(match[1]).change();
      }
      else
         self.sizeSlider.parent().hide();
   });
   var ssp = $('<div class="row"><div class="col-xs-6 pull-right"></div></div>');
   ssp.children().eq(0).append(this.sizeSlider);
   this.editorToolbar.parent().append(ssp);
}

EWEditor.prototype.initEventListeners = function ()
{
   var self = this;
   this.editorWindow.on('change', function ()
   {
      //alert("asf");
   });
   this.editorContentBody.attr('tabindex', '-1');
   this.editorContentBody.attr("contenteditable", true);
   this.editorContentBody.on('blur', function (e) {
      self.editorContentBody.attr("contenteditable", false);
   })
   //self.addElement($("<div class='row' tabindex=1><div class='col-xs-12 column' tabindex=1><p>default</p></div></div>"));
   //this.iFrame[0].contentWindow.Editor.addRow();

   var frameWindow = this.iFrame[0].contentWindow;
   var frameDoc = this.iFrame[0].contentDocument;

   this.editorContentBody.on("keydown", function (e)
   {
      if (e.shiftKey == true && e.which === 13)
      {
         e.preventDefault();
         console.log(self.activeElement);
         if (!self.activeElement || self.activeElement.is(".column"))
            self.addRow(self.activeElement);
         else
            self.addRow();
      }
      else if (e.which === 13)
      {
         
         //var ae = this.iFrame[0].contentWindow.Editor.activeElement
         if (self.activeElement && self.activeElement.is(".row"))
         {
            e.preventDefault();
            self.addColumn(self.activeElement);
         }
         //else
         //self.addElement($("<p>Pharagraph</p>"), true, self.activeElement);
      }
      //console.log(e);
   });
}

EWEditor.prototype.getSelection = function ()
{
   var frameWindow = this.iFrame[0].contentWindow;
   var frameDoc = this.iFrame[0].contentDocument;

   var range = frameDoc.createRange();
   var sel = frameWindow.getSelection();
   return sel;
}

EWEditor.prototype.focusElement = function (node)
{
   //this.editorContentBody.append(node);
   var frameWindow = this.iFrame[0].contentWindow;
   var frameDoc = this.iFrame[0].contentDocument;

   var range = frameDoc.createRange();
   var sel = frameWindow.getSelection();

   range.selectNodeContents(node);
   //range.setEnd(node, 1);
   //range.collapse(false);
   sel.removeAllRanges();
   sel.addRange(range);
}

EWEditor.prototype.addElement = function (e, focus, parent)
{
   //alert(parent);
   /* if (parent)
    parent.append(e);
    else
    this.editorContentBody.append(e);
    var node = e[0];
    if (focus === true)
    this.focusElement(node);*/
}

EWEditor.prototype.addRow = function (col)
{
   var self = this;
   var row = $("<div class='row' tabindex='1'>");
   row.on('focus', function (e)
   {
      row.attr('contenteditable', true);
      self.activeElement = row;
      self.triggerSelectElement();
   });
   row.on('blur', function (e)
   {
      //self.activeElement = null;
      row.attr('contenteditable', false);
      self.triggerSelectElement();
   });
   this.iFrame[0].contentWindow.Editor.addElement(row, col);
}

EWEditor.prototype.addColumn = function (row)
{
   var self = this;
   var col = $("<div class='col-xs-12 pull-left column' tabindex='1' contenteditable='true'></div>");
   col.on('focus', function (e)
   {
      //col.attr('contenteditable', true);
      self.activeElement = col;
      self.iFrame[0].contentWindow.Editor.showEditor(self.activeElement);
      self.triggerSelectElement();
   });
   col.on('blur', function (e)
   {
      self.activeElement = null;
      //col.attr('contenteditable', false);
      self.triggerSelectElement();
   });
   this.iFrame[0].contentWindow.Editor.addColumn(col,row);
   /*var node = col.find('p')[0];
    this.focusElement(node);*/
}

EWEditor.prototype.setContent = function (content)
{
   this.editorValue = content;
   //this.editorContentBody.html(content);
}

EWEditor.prototype.triggerSelectElement = function (content)
{
   this.editorContentBody.trigger('element-select', [this.activeElement]);
   
}

