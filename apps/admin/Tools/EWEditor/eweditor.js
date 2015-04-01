

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
   this.editorToolbar = $("<div class='col-xs-12'><div class=row></div></div>").find('.row');
   this.editorToolbar.parent().css({zIndex: 1, backgroundColor: '#e0e0e0', position: 'absolute', top: '0px', width: '100%', height: '50px', border: '0px solid #000', borderRadius: '2px'});
   this.editorBottomToolbar = $("<div id='control-row' class='control-row actions-bar' >");
   this.editorBottomToolbar.css({zIndex: 1, backgroundColor: '#fff', position: 'absolute', bottom: '0px', width: '100%', border: '0px solid #000'})
   this.editorContent = $("<div>");
   this.editorContent.css({position: 'absolute', top: '50px', bottom: 0, width: '100%'});
   this.iFrame = $("<iframe src='app-admin/Tools/EWEditor/editor.html'>");
   this.editorComponent.css({backgroundColor: '#fff', position: 'absolute', left: '10px', top: '5px', right: '10px', bottom: '5px'});
   this.iFrame.css({width: '100%', height: '100%', border: '0px solid #000'});
   this.editorComponent.append(this.editorToolbar.parent());
   this.editorComponent.append(this.editorContent);
   this.editorComponent.append(this.editorBottomToolbar);
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

   //self.addRow();

   this.sizeSlider = $('<input class="col-xs-12" type="text" name="col-lg-" id="col-lg-" value="12" data-slider="true" data-slider-range="1,12" data-slider-snap="true" data-slider-highlight="true" data-slider-step="1" >');
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
   var ssp = $('<div class="col-xs-6 pull-right">');
   ssp.append(this.sizeSlider);
   this.editorToolbar.append(ssp);

   var cra = false;
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
   this.editorBottomToolbar.append(addBlock);

   var addImage = $("<button type='button' class='btn btn-info add-photo'><i class='fa fa-picture-o fa-lg'></i></button>");
   addImage.on('click', function (e)
   {
      EW.activeElement = addImage;
      var dp = EW.createModal({});
      $.post(self.settings.ew_media_url, function (data) {
         dp.append("<div class='footer-pane row actions-bar action-bar-items' ></div>");
         // create button to add photo to the editor
         var bSelectPhoto = EW.addAction("Select Photo", function () {
            EW.setHashParameter("select-photo", true, "Media");
         }, {display: "none"}).addClass("btn-success");
         // create handler to track selected
         var EWhandler = function ()
         {
            var url = EW.getHashParameter("absUrl", "Media");
            if (url)
               bSelectPhoto.comeIn(300);
            else
               bSelectPhoto.comeOut(200);
            if (EW.getHashParameter("select-photo", "Media"))
            {
               EW.setHashParameter("select-photo", null, "Media");
               dp.dispose();
               EW.setHashParameters({"albumId": null});
               //if (EW.getHashParameter("url", "Media"))
               self.editorFrame.addElement($("<img src='" + EW.getHashParameter("absUrl", "Media") + "' alt=''>"), currentElement);
            }
         };
         EW.addURLHandler(EWhandler, "Media");
         // add the media section content to the dialog
         // after the footer-pane because the buttons should be added to the dooter-pane instead of main actions bar
         var d = $("<div class='form-content'></div>").append(data);
         dp.prepend(d);
         // add header at begining
         dp.prepend("<h1>EW Media</h1>");
      });
   });
   this.editorBottomToolbar.append(addImage);

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
   this.editorBottomToolbar.append(addText);

   var oldStyle = {};
   var target = {};
   self.editorFrame.on('element-select', function (element)
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
   {
      this.editorContentBody.find('.active').removeClass('active');
      this.editorValue = this.editorContentBody.html();
   }
   return this.editorValue;
}

