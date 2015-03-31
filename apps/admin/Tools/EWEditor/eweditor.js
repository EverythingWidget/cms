

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
   this.editorToolbar.parent().css({zIndex: 1, backgroundColor: '#e0e0e0', position: 'absolute', top: '0px', width: '100%', height: '50px', border: '0px solid #000', borderRadius: '6px'});
   this.editorContent = $("<div>");
   this.editorContent.css({position: 'absolute', top: '50px', bottom: 0, width: '100%'});
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
      self.iFrame[0].contentWindow.Editor.init();
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
   var frameEditor = self.iFrame[0].contentWindow.Editor;
   var frameWindow = $(self.iFrame[0].contentDocument.body);
   //self.addRow();

   this.sizeSlider = $('<input class="col-xs-12" type="text" name="col-lg-" id="col-lg-" value="12" data-slider="true" data-slider-range="1,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >');
   this.sizeSlider.on('change', function ()
   {
      var currentClass = frameEditor.activeElement.attr('class');
      currentClass = currentClass.replace(/col-xs-(\d*)/, 'col-xs-' + self.sizeSlider.val());
      frameEditor.activeElement.attr('class', currentClass);
   });
   frameEditor.on('element-select', function (element)
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
   var ssp = $('<div class="col-xs-6 pull-right">');
   ssp.append(this.sizeSlider);
   this.editorToolbar.append(ssp);

   var cra = false;
   var currentElement;

   this.controlRow.find('.add-row').on('click', function ()
   {
      if (!currentElement || currentElement.is('#container'))
      {
         frameEditor.addRow();
      }
      else if (currentElement.is('.row'))
      {
         frameEditor.addColumn(currentElement);
      }
      else if (currentElement.is('.column'))
      {
         frameEditor.addRow(currentElement);
      }
   });

   this.controlRow.find('.add-text').on('click', function ()
   {
      if (!currentElement || currentElement.is('#container'))
      {
         frameEditor.addElement($('<p>Text</p>'));
      }
      else if (currentElement)
      {
         frameEditor.addElement($('<p>Text</p>'), currentElement);
      }
      /*else if (currentElement.is('.column'))
       {
       frameEditor.addRow(currentElement);
       }*/
   });


   var oldStyle = {};
   var target = {};
   frameEditor.on('element-select', function (element)
   {
      if (element/* && element.is('.column, .row')*/)
      {
         currentElement = element;
      }

   });
   /*setInterval(function ()
    {//console.log(currentElement);
    if (currentElement)
    {
    target.offset = currentElement.offset();
    target.size = {width: currentElement.outerWidth(), height: currentElement.outerHeight()};
    if (JSON.stringify(target) === JSON.stringify(oldStyle))
    {
    
    return;
    }
    self.controlRow.css({top: currentElement.offset().top + currentElement.outerHeight(),
    left: currentElement.offset().left + ((currentElement.outerWidth() - 500) / 2), width: '500px'});
    oldStyle.offset = currentElement.offset();
    oldStyle.size = {width: currentElement.outerWidth(), height: currentElement.outerHeight()};
    
    }
    }, 50);*/

}

EWEditor.prototype.initEventListeners = function ()
{
   var self = this;

   var frameWindow = this.iFrame[0].contentWindow;
   var frameDoc = this.iFrame[0].contentDocument;

}

EWEditor.prototype.setContent = function (content)
{
   this.editorValue = content;
   if (this.editorContentBody)
      this.editorContentBody.html(content);
}

EWEditor.prototype.getContent = function ()
{
   if (this.editorContentBody)
      this.editorValue = this.editorContentBody.html();
   return this.editorValue;
}

