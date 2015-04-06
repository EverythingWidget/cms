
var ImageEditor = {
   img: null,
   controlPanel: null,
   init: function ()
   {
      var imgControlPanel = $("<div class='btn-group' data-toggle='buttons'></div>");
      var left = $("<label class='btn btn-info left'><i class='fa fa-align-left'></i><input type='radio'/></label>");
      left.on('click', $.proxy(this.setFloat, this, 'left'))
      var center = $("<label class='btn btn-info none'><i class='fa fa-align-center'></i><input type='radio'/></label>");
      center.on('click', $.proxy(this.setFloat, this, 'none'))
      var right = $("<label class='btn btn-info right'><i class='fa fa-align-right'></i><input type='radio'/></label>");
      right.on('click', $.proxy(this.setFloat, this, 'right'))
      imgControlPanel.append([left, center, right]);
      this.controlPanel = imgControlPanel;
   },
   setImage: function (img)
   {
      this.img = img;
      this.controlPanel.find(".center").click();
      if (this.img.css('float'))
         this.controlPanel.find("." + this.img.css('float')).click();

   },
   setFloat: function (float)
   {
      this.img.css({float: float, margin: '0 auto', maxWidth: '100%'});
   }
}

function EWEditor(config)
{
   this.settings = $.extend({id: null}, config);
   this.editorComponent;
   this.activeElement;

   this.editorToolbar;
   this.editorContent;
   this.editorBottomToolbar;
   this.editorContentBody;
   this.editorContentHead;
   //this.editor = {};
   this.iFrame;
   this.editorValue;
   this.nodeCP;
   //this.editorBottomToolbar;

   this.init(this.settings);

}

EWEditor.prototype.init = function (settings)
{
   var self = this;
   if (settings.id === null)
      return;
   this.editorComponent = $(settings.id);
   this.editorValue = this.editorComponent.html();
   this.editorComponent.empty();
   this.nodeCP = $('<div class="col-xs-6 pull-right">');
   this.editorToolbar = $("<div class='row'></div>");
   this.editorToolbar.css({zIndex: 1, backgroundColor: '#e0e0e0', position: 'absolute', top: '0px', width: '100%', height: '64px', border: '0px solid #000', borderRadius: '2px', margin: '0px', padding: '10px 0px'});
   this.editorBottomToolbar = $("<div id='control-row' class='control-row actions-bar' >");
   this.editorBottomToolbar.css({zIndex: 1, backgroundColor: '#fff', position: 'absolute', bottom: '0px', width: '100%', border: '0px solid #000'})
   this.editorContent = $("<div>");
   this.editorContent.css({position: 'absolute', top: '68px', bottom: 0, width: '100%'});
   this.iFrame = $("<iframe src='app-admin/Tools/EWEditor/editor.html'>");
   this.editorComponent.css({backgroundColor: '#fff', position: 'absolute', left: '10px', top: '5px', right: '10px', bottom: '5px'});
   this.iFrame.css({width: '100%', height: '100%', border: '0px solid #000'});
   this.editorToolbar.append(this.nodeCP);
   this.editorComponent.append(this.editorToolbar);
   this.editorComponent.append(this.editorContent);
   //this.editorComponent.append(this.editorBottomToolbar);
   this.editorContent.html(this.iFrame);
   this.iFrame.contents().find("body").append("<div id='container' class='container'>");

   this.iFrame.on('load', function ()
   {
      self.editorFrame = self.iFrame[0].contentWindow.Editor;


      self.editorContentBody = self.iFrame.contents().find("#container");

      self.editorContentBody.html(self.editorValue);

      self.initPlugins(settings.plugins);
      self.initEventListeners();

      self.editorFrame.init();
   });
}
EWEditor.prototype.PluginManager = {
   plugins: [],
   register: function (pluginName, pluginInit)
   {
      this.plugins[pluginName] = pluginInit;
   }

}
EWEditor.prototype.Util = {
   /**
    * 
    * @param {type} e
    * @returns {Array|EWEditor.prototype.Util.getColumnSize.match}
    */
   getColumnSize: function (e)
   {
      var myString = e.attr('class');
      var myRegexp = /col-xs-(\d*)/;
      var match = myRegexp.exec(myString);
      if (!match || !match[1])
         return 0;
      return match[1];
   },
   setColumnSize: function (c, s)
   {
      var currentClass = c.attr('class');
      currentClass = currentClass.replace(/col-xs-(\d*)/, 'col-xs-' + s);
      c.attr('class', currentClass);
   }
}
EWEditor.prototype.initPlugins = function (plugins)
{
   var self = this;
   //var self.editorFrame = self.iFrame[0].contentWindow.Editor;
   var frameWindow = $(self.iFrame[0].contentDocument.body);
   var buttons = $('<div class="col-xs-6 col-btn-row">');
   //self.addRow();

   this.sizeSlider = $('<input class="col-xs-12" type="text" name="col-lg-" id="col-lg-" value="12" data-slider="true" data-slider-range="1,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >');
   //this.sizeSlider.css({marginTop: '-5px'});
   this.sizeSlider.on('change', function ()
   {
      self.Util.setColumnSize(self.editorFrame.activeElement, self.sizeSlider.val());
   });
   self.editorFrame.on('element-select', function (element)
   {
      if (element && element.is('.column'))
      {
         self.sizeSlider.parent().show();
         self.sizeSlider.val(self.Util.getColumnSize(element)).change();
      }
      else
         self.sizeSlider.parent().hide();
   });
   this.nodeCP.append(this.sizeSlider);

   var currentElement;
   var addBlock = $("<button type='button' class='btn btn-info add-row'><i class='fa fa-plus fa-lg'></i></button>");
   addBlock.on('click', function ()
   {
      if (!currentElement || currentElement.is('#container'))
      {
         self.editorFrame.addRow();
      }
      else if (currentElement.is('.row'))
      {
         self.editorFrame.addColumn(currentElement, 12 - self.Util.getColumnSize(currentElement.find(".column:last-child")));
      }
      else if (currentElement.is('.column'))
      {
         self.editorFrame.addRow(currentElement);
      }
   });
   buttons.append(addBlock);


   var addImage = $("<button type='button' class='btn btn-info add-photo'><i class='fa fa-picture-o fa-lg'></i></button>");
   addImage.on('click', function (e)
   {
      EW.activeElement = addImage;
      var dp = EW.createModal({});
      $.post(self.settings.ew_media_url, function (data) {
         dp.append("<div class='footer-pane row actions-bar action-bar-items' ></div>");
         // create button to add photo to the editor
         var bSelectPhoto = EW.addAction("Select Photo", function () {
            EW.setHashParameter("select-photo", true, "media");
         }, {display: "none"}).addClass("btn-success");
         // create handler to track selected
         var EWhandler = function ()
         {
            var url = EW.getHashParameter("absUrl", "media");
            if (url)
               bSelectPhoto.comeIn(300);
            else
               bSelectPhoto.comeOut(200);

            if (EW.getHashParameter("select-photo", "media") || EW.getHashParameter("cmd", "media") == "preview")
            {
               EW.setHashParameter("select-photo", null, "media");
               dp.dispose();
               EW.setHashParameters({"albumId": null, perview: null});

               self.editorFrame.addImage(currentElement, EW.getHashParameter("absUrl", "media"));
            }
         };
         EW.addURLHandler(EWhandler, "media");
         // add the media section content to the dialog
         // after the footer-pane because the buttons should be added to the dooter-pane instead of main actions bar
         var d = $("<div class='form-content'></div>").append(data);
         dp.prepend(d);
         // add header at begining
         dp.prepend("<h1>EW Media</h1>");
      });
   });
   ImageEditor.init();
   ImageEditor.controlPanel.hide();
   this.nodeCP.append(ImageEditor.controlPanel);
   self.editorFrame.on('element-select', function (element)
   {
      if (element && element.is('img'))
      {
         ImageEditor.setImage(element);
         ImageEditor.controlPanel.show();
         //self.sizeSlider.val(self.Util.getColumnSize(element)).change();
      }
      else
         ImageEditor.controlPanel.hide();
   });
   buttons.append(addImage);

   var addText = $("<button type='button' class='btn btn-info add-text'><i class='fa fa-font fa-lg'></i></button>");
   addText.on('click', function ()
   {
      if (!currentElement || currentElement.is('#container'))
      {
         self.editorFrame.addElement($('<p>Text</p>'));
      }
      else if (currentElement)
      {
         self.editorFrame.addElement($('<p>Text</p>'), currentElement);
      }
      /*else if (currentElement.is('.column'))
       {
       frameEditor.addRow(currentElement);
       }*/
   });
   buttons.append(addText);
   this.editorToolbar.append(buttons);

   var oldStyle = {};
   var target = {};
   self.editorFrame.on('element-select', function (element)
   {
      if (element/* && element.is('.column, .row')*/)
      {
         currentElement = element;
      }

   });

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
   {
      this.editorContentBody.find('.active').removeClass('active');
      this.editorContentBody.find("[contenteditable]").attr('contenteditable', null);
      this.editorValue = this.editorContentBody.html();
   }
   return this.editorValue;
}

