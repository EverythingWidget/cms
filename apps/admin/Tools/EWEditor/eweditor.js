

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
   this.controlRow;

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
   this.editorToolbar.parent().css({zIndex: 1, backgroundColor: '#e0e0e0', position: 'absolute', top: '0px', width: '100%', height: '80px', border: '0px solid #000', borderRadius: '6px'});
   this.editorContent = $("<div>");
   this.editorContent.css({position: 'absolute', top: '80px', bottom: 0, width: '100%'});
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
      self.controlRow = self.iFrame[0].contentWindow.Editor.controlRow;
      self.initPlugins(settings.plugins);
      self.initEventListeners();
      self.editorContentBody.html(self.editorValue);
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

   var cra = false;
   var currentElement;
   this.controlRow.find('button').on('click', function ()
   {
      if (currentElement.is('#container'))
      {
         self.addRow();
      }
      else if (currentElement.is('.row'))
      {
         self.addColumn(currentElement);
      }
      else if (currentElement.is('.column'))
      {
         self.addRow(currentElement);
      }
   });
   this.controlRow.on('mouseenter', function ()
   {
      cra = true;
   });
   this.controlRow.on('mouseleave', function ()
   {
      cra = false;
   });

   var olsStyle = {};
   var target = {};

   setInterval(function ()
   {
      if (currentElement)
      {
         target.offset = currentElement.offset();
         target.size = {width: currentElement.outerWidth(), height: currentElement.outerHeight()};
         if (JSON.stringify(target) === JSON.stringify(olsStyle))
         {
            //return;
         }
         self.controlRow.css({top: currentElement.offset().top + currentElement.outerHeight() - 54,
            left: currentElement.offset().left, width: currentElement.outerWidth()});
         olsStyle.offset = currentElement.offset();
         olsStyle.size = {width: currentElement.outerWidth(), height: currentElement.outerHeight()};
      }
   }, 50);
   this.editorContentBody.on('element-select', function (e, element)
   {
      if (element/* && element.is('.column, .row')*/)
      {
         currentElement = element;
         //self.controlRow.show();
      }
      //else if (!cra)
      //self.controlRow.hide();
   });
}

EWEditor.prototype.initEventListeners = function ()
{
   var self = this;

   var frameWindow = this.iFrame[0].contentWindow;
   var frameDoc = this.iFrame[0].contentDocument;

   this.editorContentBody.on('focus', function (e)
   {
      self.editorContentBody.attr('contenteditable', true);
      self.iFrame[0].contentWindow.Editor.selectElement(self.editorContentBody);
      //self.editorContentBody.focus();
      if (self.activeElement)
         self.activeElement.removeClass('active');
      self.activeElement = self.editorContentBody;
      self.triggerSelectElement();
   });
   this.editorContentBody.on('blur', function (e)
   {
      //self.editorContentBody.attr('contenteditable', false);
      //self.activeElement = null;
      //self.triggerSelectElement();
   });
   this.editorContentBody.on('keydown', function (e) {
      //e.preventDefault();
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

EWEditor.prototype.addRow = function (col)
{
   var self = this;
   var row = $("<div class='row' tabindex='1'>");

   row.on('focus', function (e)
   {
      row.attr('contenteditable', true);
      //row.focus();
      row.addClass('active');
      if (self.activeElement)
         self.activeElement.removeClass('active');
      self.activeElement = row;
      self.triggerSelectElement();
   });
   row.on('blur', function (e)
   {
      //self.activeElement = null;
      //row.attr('contenteditable', false);
      //self.triggerSelectElement();
   });
   this.iFrame[0].contentWindow.Editor.addElement(row, col);
}

EWEditor.prototype.addColumn = function (row)
{
   var self = this;
   var col = $("<div class='col-xs-12 pull-left column' tabindex='1' contenteditable='true'></div>");

   col.on('focus', function (e)
   {
      col.addClass('active');
      if (self.activeElement)
         self.activeElement.removeClass('active');
      col.attr('contenteditable', true);
      //col.focus();
      /* if (self.activeElement)
       self.activeElement.removeClass('active');*/
      self.activeElement = col;
      self.iFrame[0].contentWindow.Editor.showEditor(self.activeElement);
      self.triggerSelectElement();
      e.preventDefault();
   });
   col.on('blur', function (e)
   {
      //self.activeElement = null;
      col.attr('contenteditable', false);
      //self.triggerSelectElement();
   });
   this.iFrame[0].contentWindow.Editor.addColumn(col, row);
}

EWEditor.prototype.setContent = function (content)
{
   this.editorValue = content;
   //this.editorContentBody.html(content);
}

EWEditor.prototype.triggerSelectElement = function (content)
{
   this.editorContentBody.trigger('element-select', [this.activeElement]);
   //if (this.activeElement)
   //this.iFrame[0].contentWindow.Editor.selectElement(this.activeElement);

}

