

function EWEditor(config)
{
   var settings = $.extend({id: null}, config);
   this.editorWindow;

   this.editorToolbar;
   this.editorContent;
   this.editorContentBody;
   this.editorContentHead;
   //this.editor = {};
   this.iFrame;

   this.init(settings);
   this.initEventListeners();
}

EWEditor.prototype.init = function (settings)
{
   if (settings.id === null)
      return;
   this.editorWindow = $(settings.id);
   this.editorToolbar = $("<div class='actions-bar'>");
   this.editorToolbar.css({zIndex: 1, backgroundColor: '#e0e0e0', position: 'absolute', top: '0px', width: '100%', height: '60px', border: '0px solid #000', borderRadius: '6px'});
   this.editorContent = $("<div>");
   this.editorContent.css({position: 'absolute', top: '60px', bottom: 0, width: '100%'});
   this.iFrame = $("<iframe>");
   this.editorWindow.css({backgroundColor: '#fff', position: 'absolute', left: '10px', top: '5px', right: '10px', bottom: '5px'});
   this.iFrame.css({width: '100%', height: '100%', border: '0px solid #000'});
   this.editorWindow.append(this.editorToolbar);
   this.editorWindow.append(this.editorContent);
   this.editorContent.html(this.iFrame);
   this.iFrame.contents().find("body").append("<div id='container' class='container'>");
   this.editorContentBody = this.iFrame.contents().find("#container");
   this.editorContentHead = this.iFrame.contents().find("head");
   this.editorContentHead.append("<link rel='stylesheet' href='" + settings.bootstrap + "'/>");
   this.editorContentHead.append("<style>\n\
   html, body{padding:10px 2px;margin:0px} \n\
   .container{width:100%} \n\
   //p{outline:1px dashed #aaa} \n\
.row{border:1px dashed #aaa;} \n\
.row:before {content: 'ROW';line-height: 20px;display:block;padding:0px 4px;  font-size: 11px;  font-weight: bold;}\n\
.column:focus:before{background-color:#3cf;}\n\
.column:focus{outline:none;}\n\
.column:before {content: 'COLUMN';line-height: 20px;display:block;padding:0px 4px;  font-size: 11px;  font-weight: bold; background-color: rgba(0,0,0,.1);}\n\
   </style>");
   this.activeElement;
   this.initPlugins(settings.plugins);
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
   this.editorToolbar.append(checkBtn);

   var addRowBtn = $("<button type='button' class='btn btn-info'>+Row</button>");
   this.editorToolbar.append(addRowBtn);

   var extra = $("<div class='btn-group' data-toggle='buttons'><button type='button' class='btn btn-info' data-toggle='true'>Extra</button></div>");
   this.editorToolbar.append(extra);

   var alignBtns = $("<div class='btn-group'></div>");
   var alignLeft = $("<button type='button' class='btn btn-info'>Left</button>");
   alignLeft.on('click', function () {
      var select = self.getSelection();
      if (select.baseNode && select.baseNode.parentNode && $(select.baseNode.parentNode).not("div"))
         $(select.baseNode.parentNode).css({textAlign: "left"});
   });
   var alignCenter = $("<button type='button' class='btn btn-info'>Center</button>");
   alignCenter.on('click', function () {
      var select = self.getSelection();
      if (select.baseNode && select.baseNode.parentNode && $(select.baseNode.parentNode).not("div"))
         $(select.baseNode.parentNode).css({textAlign: "center"});
   });
   var alignRight = $("<button type='button' class='btn btn-info'>Right</button>");
   alignRight.on('click', function () {
      var select = self.getSelection();
      if (select.baseNode && select.baseNode.parentNode && $(select.baseNode.parentNode).not("div"))
         $(select.baseNode.parentNode).css({textAlign: "right"});
   });
   alignBtns.append([alignLeft,alignCenter,alignRight]);
   this.editorToolbar.append(alignBtns);
}

EWEditor.prototype.initEventListeners = function ()
{
   var self = this;
   this.editorContentBody.attr('tabindex', '-1');
   this.editorContentBody.attr("contenteditable", true);
   this.editorContentBody.on('blur', function (e) {
      self.editorContentBody.attr("contenteditable", false);
   })
   //self.addElement($("<div class='row' tabindex=1><div class='col-xs-12 column' tabindex=1><p>default</p></div></div>"));
   self.addRow();

   var frameWindow = this.iFrame[0].contentWindow;
   var frameDoc = this.iFrame[0].contentDocument;

   this.editorContentBody.on("keydown", function (e)
   {
      //if(e.which)

      if (e.shiftKey == true && e.which === 13)
      {
         e.preventDefault();
         self.addRow();
         //self.addElement($("<div class='row' tabindex=1><div class='col-xs-12 column' tabindex=2><p>shift+enter</p></div></div>"), true);
      }
      else if (e.which === 13)
      {
         e.preventDefault();
         if (self.activeElement && self.activeElement.is(".row"))
            self.addColumn(self.activeElement);
         else
            self.addElement($("<p>Pharagraph</p>"), true, self.activeElement);
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
   if (parent)
      parent.append(e);
   else
      this.editorContentBody.append(e);
   var node = e[0];
   if (focus === true)
      this.focusElement(node);
}

EWEditor.prototype.addRow = function (focus)
{
   var self = this;
   var row = $("<div class='row' tabindex='1'>");
   row.on('focus', function (e)
   {
      //row.attr('contenteditable', true);
      //row.focus();
      self.activeElement = row;
      //console.log("focus");
   });
   row.on('blur', function (e)
   {
      //row.attr('contenteditable', false);
   });
   this.editorContentBody.append(row);
   row.focus();
   //var node = row[0];
   //if (focus === true)
   //this.focusElement(node);
}

EWEditor.prototype.addColumn = function (row)
{
   var self = this;
   var col = $("<div class='col-xs-12 column' tabindex='1'><p>ha</p></div>");
   col.on('focus', function (e)
   {
      col.attr('contenteditable', true);
      //row.focus();
      self.activeElement = col;
      console.log("focus");
   });
   col.on('blur', function (e)
   {
      col.attr('contenteditable', false);
   });
   row.append(col);
   col.focus();
   var node = col.find('p')[0];
   //if (focus === true)
   this.focusElement(node);
}

