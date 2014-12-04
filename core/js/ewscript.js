//Form data to JSOn Object
$.fn.serializeJSON = function ()
{
   var o = {
   };
   var a = this.serializeArray();
   $.each(a, function () {
      if (o[this.name] !== undefined) {
         if (!o[this.name].push) {
            o[this.name] = [o[this.name]];
         }
         o[this.name].push(this.value || '');
      } else {
         o[this.name] = this.value || '';
      }
   });
   //alert(JSON.stringify(o));
   return JSON.stringify(o);
};

var customHashes = new Object();

function EverythingWidgets()
{
   var self = this;
   this.urlHandlers = new Array();
   this.mainContent = $("#main-content");
   this.currentTab = null;
   $("#components-pane").hide();
   var oldSize = "";
   $(document).mousedown(function (event) {
      //console.log("clicked: " + $(event.target).text());
      EverythingWidgets.prototype.activeElement = $(event.target);
   });
   $(window).resize(function () {

      if ($(window).width() < 768 && oldSize !== "xs")
      {
         $(window).trigger("ew.screen.xs");
         oldSize = "xs";
      }
      else if ($(window).width() >= 768 && $(window).width() < 992 && oldSize !== "sm")
      {
         $(window).trigger("ew.screen.sm");
         oldSize = "sm";
      }
      else if ($(window).width() >= 992 && $(window).width() < 1360 && oldSize !== "md")
      {
         $(window).trigger("ew.screen.md");
         oldSize = "md";
      }
      else if ($(window).width() >= 1360 && oldSize !== "lg")
      {
         $(window).trigger("ew.screen.lg");
         oldSize = "lg";
      }
   });
   this.customFunction = function ()
   {

   };
   this.showAllComponents = function ()
   {
      //$("#components-pane").animate({top: 0}, 300);
      //$("#components-pane").show(0);
      var ew = this;
      $("#components-pane").show();
      $("#components-pane").css({
         top: "-100px",
         left: "-100px",
         opacity: 0
      });
      /*$(window).on("resize", function () {
       $("#components-pane").css({
       left: ($("body").outerWidth() - $("#components-pane").outerWidth()) / 2
       });
       });*/

      $("#components-pane").stop().animate({
         top: "0px",
         left: "0px",
         opacity: 1,
         display: "block"
      },
      500, "Power3.easeOut");
      this.lock("body", " ");
      $(".glass-pane-lock").bind("click", function (e) {
         if (parseInt($('#components-pane').css('top')) === 0)
         {
            $("#components-pane").stop().animate({
               top: "-100px",
               left: "-100px",
               opacity: 0,
               display: "none"
            },
            500, "Power3.easeOut", function () {
               //$("#components-pane").hide(0);
            });
            ew.unlock("body");
            //$("#components-pane").animate({top: -200}, 300);
            $(".glass-pane-lock").unbind("click");
         }
      });
   };

   /**
    * @syntax addAction(text, handler, css)
    * @param {String} text title of button
    * @param {Object} handler
    * @param {map} css custom style for action button
    * @description Create action with text as title and handler for click event
    * @return {action} Jquery Object
    */
   this.addListItem = function (text, handler, css)
   {
      var li = $(document.createElement("li"));
      var action = $(document.createElement("a"));
      action.addClass("button");
      if (css)
         li.css(css);
      //alert(handler);
      action.attr("type", "button");
      action.text(text).click(handler);
      li.append(action);
      $("#action-bar-items").append(li);
      li.data("a", action);
      return li;
   };

   this.addAction = function (text, handler, css, parent)
   {
      var li = $(document.createElement("li"));
      var action = $("<button>" + text + "</button>");
      action.attr("data-label", text);
      action.addClass("btn btn-primary");
      if (typeof css == "string")
      {
         parent = css;
      }

      action.attr("type", "button");
      action.click(handler);

      if ($("#" + parent).length != 0)
      {
         $("#" + parent).append(action);
      }
      else
      {
         $(".action-bar-items").last().append(action);
      }
      if (typeof css != "string" && css)
      {
         action.css(css);
         return action;
      }
      //action.width(action.width());
      return action;
   };

   this.addNotification = function (css)
   {
      var li = $(document.createElement("li"));
      var notification = $(document.createElement("label"));
      if (css)
         li.css(css);
      //alert(handler);
      //notification.text(text);
      li.append(notification);
      li.setText = function (text, time)
      {
         if (text)
         {
            notification.text(text);
            notification.fadeIn(100);
         }
         else
         {
            notification.animate({
               width: "toggle"
            },
            500, function () {
               notification.attr({
                  class: ""
               });
               notification.text("");
               li.remove();
            });
         }
         if (time)
            setTimeout(function () {
               li.setText("");
            }, time);
      };

      $("#action-bar-items").append(li);
      return li;
   };

   this.newTopPane = function (onClose, decoration)
   {
      var topPane = $(document.createElement("div"));
      //EW.lock($("body"), " ");
      topPane.onClosed = function () {
      };
      if (onClose)
      {
         topPane.onClosed = onClose;
      }
      var xBtn = $("<a></a>");
      xBtn.click(function () {
         topPane.dispose();
      });
      if (typeof onClose == "string")
         decoration = onClose;
      //xBtn.find("a").removeClass("button");
      xBtn.addClass("close-button x-icon");
      xBtn.css("z-index", "2");
      if (decoration == "full")
      {
         topPane.addClass("top-pane col-xs-12 full");
         xBtn.css({
            position: "absolute",
            right: "10px",
            top: "10px",
            width: "30px",
            height: "30px"
         });
      }
      else if (decoration == "modal")
      {
         topPane.addClass("top-pane center");
      }
      else
      {
         topPane.addClass("top-pane col-xs-12");
         xBtn.css({
            position: "absolute",
            right: "50px",
            top: "10px",
            width: "30px",
            height: "30px"
         });
      }
      topPane.hide();
      topPane.addClass("scale-out transparent");
      topPane.css({
         position: "absolute",
         top: "0px"
      });
      $("body").append(topPane);

      xBtn.hide();
      $("body").append(xBtn);
      xBtn.delay(300).fadeIn(200);
      topPane.show();
      topPane.removeClass("scale-out transparent");
      topPane.dispose = function () {

         //this.fadeOut(200, function() {
         //EW.unlock($("body"));
         $(this).remove();
         //});
         xBtn.remove();
         topPane.onClosed();
      };
      return topPane;
   };
   $(document).ready(function () {
      // ew_activity handler
      var oldEWActivity = null;
      var modal = null;
      self.addURLHandler(function ()
      {
         var activity = self.getHashParameter("ew_activity");
         if (activity && activity != oldEWActivity)
         {
            var settings = {closeHash: {}, /*hash: {key: "ew_activity", value: activity},*/ onOpen: function () {
                  var modal = this;
                  self.lock(this);
                  var postData = self.getHashParameters();
                  // Manage post data if it is set
                  if (self.activities[activity].postData)
                  {
                     // Add user defined post data to the postData variable
                     // Call post data if it is a function
                     if (typeof self.activities[activity].postData == 'function')
                     {
                        $.extend(postData, self.activities[activity].postData());
                     }
                     else
                        $.extend(postData, self.activities[activity].postData);
                  }
                  $.ajax({
                     type: "POST",
                     url: self.activities[activity].url,
                     data: postData,
                     success: function (data) {
                        modal.html(data);
                     },
                     error: function (result) {
                        alert(result.responseJSON.message);
                        //alert("sss");
                        self.customAjaxErrorHandler = true;
                     }
                  });

               },
               onClose: function () {
                  var closeHashParameters = {ew_activity: null};
                  //var customHashParameters = {};
                  if (self.activities[activity].onDone)
                  {
                     if (typeof self.activities[activity].onDone == 'function')
                        self.activities[activity].onDone(closeHashParameters);
                     else
                        $.extend(closeHashParameters, self.activities[activity].onDone);
                  }
                  // Trigger close activity event and pass closeHashParameters to it
                  $(document).trigger(activity + ".close", closeHashParameters);
                  $.extend(closeHashParameters, settings.closeHash);
                  self.setHashParameters(closeHashParameters);
               }};
            if (self.activities[activity])
            {
               // Trigger open activity event and pass settings to it before creating modal
               $(document).trigger(activity + ".open", settings);

               // Do not create modal if activity has a modal already
               //if (self.activities[activity].hasModal)
               //return;

               $.extend(settings, self.activities[activity].modal);
               //modal = self.createModal(settings);
               self.activities[activity].modalObject = self.createModal(settings);
            }
            else
            {
               alert("Activity not found");
               self.setHashParameters({ew_activity: null});
            }
            oldEWActivity = activity;
         }
         else if (oldEWActivity != activity)
         {//alert(activity+" "+oldEWActivity);
            if (oldEWActivity && self.activities[oldEWActivity].modalObject)
               self.activities[oldEWActivity].modalObject.trigger("close");
            //if (self.activities[oldEWActivity].hasModal)
            //  self.activities[oldEWActivity].hasModal = false;
            oldEWActivity = activity;
         }

      });

      self.addURLHandler(function ()
      {
         var activity = self.getHashParameter("ew_activity", "FORMLESS_ACTIVITY");
         if (activity)
         {
            if (self.activities[activity])
            {
               $(document).trigger(activity + ".call", self.activities[activity]);
               var postData = self.getHashParameters("FORMLESS_ACTIVITY");
               // Manage post data if it is set
               if (self.activities[activity].postData)
               {
                  // Overwrite the content of postData variable  with the user defined post data
                  // Call postData if it is a function
                  if (typeof self.activities[activity].postData == 'function')
                  {
                     postData = self.activities[activity].postData();
                  }
                  else
                     postData = self.activities[activity].postData;
               }
               if (!postData)
                  return;
               $.post(self.activities[activity].url, postData, function (data) {
                  if (self.activities[activity].onDone)
                  {
                     self.activities[activity].onDone(data);
                  }
                  $(document).trigger(activity + ".done", data);
               }, "json");
            }
            else
            {
               alert("Formless activity not found");
               //self.setHashParameters({ew_activity: null});
            }
         }
         self.setHashParameters({ew_activity: null}, "FORMLESS_ACTIVITY");
      }, "FORMLESS_ACTIVITY");
   });
}
/**
 * Create activity function and return it
 * @param {json} conf <b>activity</b>, <b>defaultClass</b>, <b>title</b>, <b>postData</b>, <b>onDone</b> 
 * @returns {EverythingWidgets.prototype.getActivity.activityCaller}
 */
EverythingWidgets.prototype.getActivity = function (conf)
{
   var self = this;
   var settings = {title: "", defaultClass: "btn-primary", activity: null};
   $.extend(settings, conf);
   if (!self.activities[settings.activity])
   {
      console.log("activity does not exist: " + settings.activity);
      return null;
   }
   if (self.activities[settings.activity].modalObject && settings.modal && settings.modal.class)
   {

      self.activities[settings.activity].modalObject.animate({className: "top-pane col-xs-12 " + settings.modal.class}, 300);
   }
   /*if (conf.postData)
    {
    self.activities[settings.activity].postData = conf.postData;
    }
    if (conf.onDone)
    {
    self.activities[settings.activity].onDone = conf.onDone;
    }*/
   $.extend(self.activities[settings.activity], conf);
   //self.activities[settings.activity].modalSetting = conf.modalSetting;

   var activityCaller = function (hash) {
      var hashParameters = {ew_activity: settings.activity};

      // Call hash if it is a function
      if (typeof hash == 'function')
      {
         hash = hash();
      }

      $.extend(hashParameters, hash);
      if (self.activities[settings.activity].form)
      {
         self.setHashParameters(hashParameters);
      }
      else
      {
         //console.log(hashParameters);
         self.setHashParameters(hashParameters, "FORMLESS_ACTIVITY");
      }
   };

   return activityCaller;
};
/**
 * Create activity button and add it to the <b>action-bar-items</b> as default or to the parent if specified
 * @param {json} conf <b>activity</b>, <b>defaultClass</b>, <b>title</b>, <b>parent</b>, <b>css</b>, <b>postData</b>, <b>onDone</b> 
 * @returns {JQuery}
 */
EverythingWidgets.prototype.addActivity = function (conf)
{
   var self = this;
   var settings = {title: "", defaultClass: "btn-primary", activity: null};
   $.extend(settings, conf);
   var activityCaller = self.getActivity(conf);
   if (!activityCaller)
   {
      console.log("Undefined activity: " + settings.activity);
      //var emptyObj = $().data("activityCaller", null);
      return $();
   }
   var li = $(document.createElement("li"));
   var action = $("<button>" + settings.title + "</button>");
   action.attr("data-label", settings.title);
   action.addClass("btn " + settings.defaultClass + " " + settings.class);

   action.attr("type", "button");
   action.click(function () {
      activityCaller(settings.hash);
   });
   action.data("activity", activityCaller);

   if ($("#" + settings.parent).length != 0)
   {
      $("#" + settings.parent).append(action);
   }
   else
   {
      $(".action-bar-items").last().append(action);
   }
   if (typeof settings.css != "string" && settings.css)
   {
      action.css(settings.css);
      return action;
   }
   //action.width(action.width());
   return action;
};

EverythingWidgets.prototype.getHashParameters = function (hashName)
{
   var hashValue = window.location.hash;


   if (customHashes[hashName])
   {
      hashValue = customHashes[hashName].hash;
   }
   else if (hashName)
   {
      return {};
   }
   //var hashValue = window.location.hash;
   if (hashValue.indexOf("#") !== -1)
   {
      hashValue = hashValue.substring(1);
   }
   var result = {};
   var pairs = hashValue.split("&");
   $.each(pairs, function (k, v)
   {
      var keyAndValue = v.split("=");
      result[keyAndValue[0]] = keyAndValue[1];
   });
   return result;
};

EverythingWidgets.prototype.newOverPane = function (onClose)
{
   var self = this;
   var topPane = $(document.createElement("div"));
   var content = $(document.createElement("div"));
   content.addClass("content");
   topPane.append(content);
   topPane.content = content;
   topPane.next().addClass("top-pane right");
   topPane.css({
      display: "none"
   });
   topPane.onClosed = function () {
   };
   if (onClose)
   {
      topPane.onClosed = onClose;
   }
   topPane.dispose = function ()
   {
      this.fadeOut(300, function ()
      {
         topPane.remove();
      });
      xButton.remove();
      topPane.onClosed();
   };
   topPane.html = function (data)
   {
      if (!data)
         return topPane.content.html();
      topPane.content.html(data);
   };

   var xButton = $(document.createElement("a"));
   xButton.css({
      position: "fixed",
      top: "10px",
      right: "10px"
   });
   xButton.addClass("close-button");
   xButton.text("");
   xButton.click(function ()
   {
      topPane.dispose();
   });
   topPane.append(xButton);
   topPane.fadeIn(300);

   $("body").append(topPane);
   return topPane;
};

EverythingWidgets.prototype.setFormData = function (formId, jsonData, handler)
{
   var setInputData = function (key, val)
   {
      if (handler)
      {
         $(formId + " [id='" + key + "']").val(handler(key, val));
      }
      else
      {
         var elm = $(formId + " input[name='" + key + "'][value='" + val + "']");
         if (elm.length == 0)
         {
            elm = $(formId + " [id='" + key + "']");
         }
         else
         {
            if (elm.is(":radio") || elm.is(":checkbox"))
               if (!elm.is(":checked"))
               {
                  elm.click();
                  elm.prop("checked", true);
               }
         }
         if (elm.is("input") || elm.is("select") || elm.is("textarea"))
         {
            elm.val(val).change();
         }
         else if (elm.is("img"))
         {
            elm.prop("src", "/res/images/" + val);
            elm.attr("data-file-extension", /[^.]+$/.exec(val));
            elm.attr("data-filename", /^[^.]+/.exec(val));
         }
         else
            elm.text(val);
      }
   };
   if (!jsonData)
   {
      $.each($(formId + " input[id]," + formId + " label[id]," + formId + " select[id]," + formId + " textarea[id]"), function (elm) {
         var field = $(elm);
         if (field.is(":radio") || field.is(":checkbox"))
         {
            if (field.is(":checked"))
            {
               field.click();
               field.prop("checked", false);
            }
         }
         else if (field.is("input") || field.is("select") || field.is("textarea"))
         {
            field.val("").change();
         }
         else if (field.is("img"))
         {
            field.prop("src", "");
            field.attr("data-file-extension", "");
            field.attr("data-filename", "");
         }
         else
            field.text("");
      });
      $(formId).data("form-data", {});
      $(formId).trigger("refresh", [{}]);
      return;
   }

   $.each(jsonData, function (key, val) {
      //alert(typeof(val) + " " + key);
      if (typeof (val) == "object" && typeof (key) && val)
      {

         $.each(val, function (key1, val1) {
            setInputData(key1, val1);
         });
      }
      else
         setInputData(key, val);
   });
   //alert(formId);
   $(formId).data("form-data", jsonData);
   $(formId).trigger("refresh", [jsonData]);
};

EverythingWidgets.prototype.getParentDialog = function (element)
{
   var parentDialog = element.closest(".top-pane");
   //alert("ha");
   return parentDialog;
};
/**
 * Create new modal pane and add it to the DOM
 * @param {mixed} onClose
 * @param {String} closeAction
 * @returns {modalPaneWidgets.prototype.createModal.dialogPane|jQuery|$}
 */
EverythingWidgets.prototype.createModal = function (onClose, closeAction)
{
   var self = this;
   var originElement;
   var animationDiv;
   var xButton;
   var methods = {
      // Set X button at the top tight corner
      setCloseButton: function ()
      {
         var mw = ($("#base-pane").outerWidth() - modalPane.outerWidth()) /*/ 2*/;
         if (mw < 0)
            mw = 0;
         xButton.css({
            left: modalPane.offset().left + modalPane.width() - 10,
            //right:"10px",
            top: parseInt(modalPane.css("top")) + 10
         });
         xButton.show();
      }};
   var settings = {
      class: "center",
      initElement: true,
      beforeClose: function ()
      {
         return true;
      },
      //closeAction: "hide",
      autoOpen: true
   };

   if (typeof (onClose) == "object")
   {
      // If hash is set, change default behaviors
      if (onClose.hash)
      {
         settings.closeAction = "hide";
         settings.autoOpen = false;
      }
      $.extend(settings, onClose);
   }
   else
      settings = {
         onClose: onClose,
         closeAction: closeAction,
         autoOpen: true,
         initElement: true,
         class: "center"
      };

   this.isOpen = false;
   var modalPane = $(document.createElement("div"));

   modalPane.addClass("top-pane col-xs-12");
   modalPane.addClass(settings.class);
   xButton = $("<a class='close-button x-icon'>");
   xButton.css({
      position: "absolute",
      display: "none",
      zIndex: 2
   });
   xButton.click(function ()
   {
      modalPane.trigger("close");
   });

   $(window).resize(function ()
   {
      methods.setCloseButton();
   });

   modalPane.on("beforeClose", function ()
   {
      return true;
   });
   // Close event
   modalPane.on("close", function ()
   {
      if (modalPane.triggerHandler("beforeClose"))
      {

         // Close the modal if it is open
         if (modalPane.isOpen)
         {
            if (!animationDiv)
               animationDiv = $("<div class='s-to-d-scale-anim'>").css({width: modalPane.outerWidth(), height: modalPane.outerHeight(), top: modalPane.offset().top, left: modalPane.offset().left, position: "absolute", backgroundColor: "#aaa", zIndex: modalPane.css("z-index")});
            modalPane.before(animationDiv);
            //animationDiv.text("");

            self.unlock($("body"));
            // Detach the modal if close action is hide
            if (settings.closeAction === "hide")
            {
               modalPane.hide();
               xButton.detach();
            }
            else
            {
               modalPane.remove();
               xButton.remove();
            }
            if (settings.onClose)
            {
               settings.onClose.apply(modalPane, null);
            }
            modalPane.isOpen = false;

            if (!originElement || !$.contains(document, originElement[0]))
            {
               animationDiv.text("");
               animationDiv.delay(30).animate({top: "+=50px", left: "+=50px", width: "-=100px", height: "-=100px", opacity: 0 /*, lineHeight: ce.outerHeight() + "px"*/}, 360, "Power1.easeOut", function () {
                  if (originElement)
                     originElement.css("visibility", "");
                  animationDiv.remove();
               });
            }
            else
            {
               animationDiv.css("textShadow", "");
               animationDiv.animate({top: originElement.offset().top, left: originElement.offset().left, width: originElement.outerWidth(), height: originElement.outerHeight(), lineHeight: originElement.outerHeight() + "px", fontSize: originElement.css("fontSize")}, 360, "Power3.easeOut", function () {
                  originElement.css("visibility", "");
                  animationDiv.fadeOut(120, function () {
                     animationDiv.remove();
                  });
               });
            }
         }
      }
   });
   // Open event
   modalPane.on("open", function ()
   {
      // Open the modal if it is not open
      if (!modalPane.isOpen)
      {
         self.lock($("body"), " ");
         modalPane.show();
         modalPane.css({opacity: "0"});
         //xButton.hide();
         if (!$.contains(document.body, modalPane))
         {
            xButton.hide();
            $("body").append(modalPane);
            $("body").append(xButton);
         }
         originElement = self.activeElement;

         if (settings.initElement && originElement)
         {
            if (originElement.is("p,h1,h2,h3,h4,h5,h6,span"))
               originElement = originElement.parent();
            // Get current element background color
            originElement.css("visibility", "hidden");
            var bgColor = originElement.css("backgroundColor");
            var loadingLabel = originElement.data("label");
            bgColor = (bgColor == "rgba(0, 0, 0, 0)" || bgColor == "transparent") ? "#aaa" : bgColor;
            animationDiv = $("<div class='s-to-d-scale-anim'>").css({overflow: "hidden", whiteSpace: "nowrap", textShadow: "-1px -1px 1px rgba(0,0,0,.3)", color: "#fff", textAlign: "center", fontSize: "3em", top: originElement.offset().top, left: originElement.offset().left, position: "absolute", backgroundColor: bgColor, zIndex: modalPane.css("z-index")});
            animationDiv.text(loadingLabel);
            modalPane.before(animationDiv);
            animationDiv.css({width: originElement.outerWidth(), height: originElement.outerHeight()});
            //tempDiv.animate({top: modalPane.offset().top + modalPane.outerHeight() / 6, left: modalPane.offset().left + modalPane.outerWidth() / 6, width: modalPane.outerWidth() / 1.5, height: modalPane.outerHeight() / 1.5}, {duration:420,queue:false});
            animationDiv.animate({top: modalPane.offset().top, left: modalPane.offset().left, width: modalPane.outerWidth(), height: modalPane.outerHeight(), lineHeight: modalPane.outerHeight() + "px"}, 360, "Power3.easeOut", function () {
               modalPane.delay((!loadingLabel) ? 0 : 120).animate({opacity: "1"}, 240, function () {
                  methods.setCloseButton();
                  animationDiv.remove();
               });
            });
         }
         else
         {
            modalPane.animate({right: "-=15%"}, 0);
            modalPane.animate({opacity: "1", right: "0px"}, 420, "Power3.easeOut", methods.setCloseButton);
         }
         //xButton.show();

         //modalPane.removeClass("scale-out transparent");
         modalPane.isOpen = true;
         if (settings.onOpen)
         {
            settings.onOpen.apply(modalPane, null);
         }
      }
   });
   modalPane.dispose = function ()
   {
      modalPane.trigger("close");
   };
   modalPane.open = function ()
   {
      modalPane.trigger("open");
   };

   if (settings.hash)
   {
      self.addURLHandler(function () {
         if (self.getHashParameter(settings.hash.key, settings.hash.name) === settings.hash.value)
         {
            modalPane.trigger("open");
         }
         else
         {
            modalPane.trigger("close");
         }
      });
   }
   if (settings.autoOpen)
   {
      modalPane.trigger("open");
   }
   return modalPane;
};

EverythingWidgets.prototype.setWidgetParam = function (field, key, val)
{
   var obj = $(field).val();
   if (obj)
      obj = $.parseJSON(obj);
   if (typeof obj != "object")
      obj = new Object();
   obj[key] = val;
   $(field).val(JSON.stringify(obj));
};
EverythingWidgets.prototype.getWidgetParam = function (field, key)
{
   var obj = $(field).val();
   if (obj)
      obj = $.parseJSON(obj);
   if (typeof obj != "object")
      return obj[key];
   return null;
};

JSON.stringify = JSON.stringify || function (obj) {
   var t = typeof (obj);
   if (t != "object" || obj === null) {
      // simple data type
      if (t == "string")
         obj = '"' + obj + '"';
      return String(obj);
   }
   else {
      // recurse array or object
      var n, v, json = [], arr = (obj && obj.constructor == Array);
      for (n in obj) {
         v = obj[n];
         t = typeof (v);
         if (t == "string")
            v = '"' + v + '"';
         else if (t == "object" && v !== null)
            v = JSON.stringify(v);
         json.push((arr ? "" : '"' + n + '":') + String(v));
      }
      return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
   }
};

function HashListener(name)
{
   this.name = name;
   this.handlers = new Array();
   this.oldHash = "";
   this.hash = "#";
   this.newHandler = false;
   this.Check;
   var thisHL = this;

   var detect = function () {
      if (thisHL.oldHash !== customHashes[name].hash || thisHL.newHandler)
      {
         thisHL.newHandler = false;
         thisHL.oldHash = customHashes[name].hash;
         $(window).trigger(name);
         /*for (var i = 0; i < thisHL.handlers.length; i++)
          {
          thisHL.handlers[i].call();
          }*/
      }
   };

   this.Check = setInterval(function ()
   {
      detect();
   }, 50);

   this.addHandler = function (handlerName, handlerFunction)
   {
      $(window).off(name + "." + handlerName);
      $(window).on(name + "." + handlerName, handlerFunction);
   };
}

/**
 * @syntax setHashParameter(key, value)
 * @param {String} key Name of parameter
 * @param {String} value value of parameter
 * @param {String} hashName value of parameter
 * @description If key does not exist in URL hash, add key with value to the end of URL hash 
 * else, change the current value of the key to new value
 */
EverythingWidgets.prototype.setHashParameter = function (key, value, hashName)
{
   var data = {};
   if (key)
   {
      //alert(key + " " + value)
      data[key] = value;
   }
   this.setHashParameters(data, hashName);

   /*var hashValue = window.location.hash;
    
    if (hashName)
    {
    // create new hash listener if new hash name has been passed
    if (!customHashes[hashName])
    {
    //alert(hashName);
    customHashes[hashName] = new HashListener(hashName);
    hashValue = customHashes[hashName].hash;
    }
    else
    {
    this.removeURLHandler("", hashName);
    hashValue = customHashes[hashName].hash;
    }
    }
    if (hashValue.indexOf("#") !== -1)
    {
    hashValue = hashValue.substring(1);
    }
    var pairs = hashValue.split("&");
    var newHash = "#";
    var keyExisted = false;
    var i = 0;
    var and = false;
    if (hashValue.indexOf(key) == -1 && value == null)
    {
    return;
    }
    //alert(pairs.length + " " + value);
    $.each(pairs, function (i, v)
    {
    var keyAndValue = v.split("=");
    if (keyAndValue[0] == key && value == null)
    {
    return;
    }
    if (and && !newHash.match(/&$/))
    {
    newHash += "&";
    and = false;
    }
    if (keyAndValue[0] == key && value != null)
    {
    newHash += keyAndValue[0] + "=" + value;
    keyExisted = true;
    and = true;
    }
    
    else if (keyAndValue[0])
    {
    newHash += keyAndValue[0] + "=" + keyAndValue[1];
    and = true;
    }
    
    });
    if (!keyExisted && key && value != null)
    {
    if (newHash != "#" && !newHash.match(/&$/))
    newHash += "&";
    newHash += key + "=" + value;
    }
    // set newHash for corresponding hash name if it has been passed
    if (hashName)
    {
    customHashes[hashName].hash = newHash;
    //alert(customHashes[hashName].hash);
    }
    // set url hash if no hash name specified
    else
    window.location.hash = newHash;*/

};

EverythingWidgets.prototype.setHashParameters = function (parameters, hashName)
{
   var hashValue = window.location.hash;

   if (hashName)
   {
      // create new hash listener if new hash name has been passed
      if (!customHashes[hashName])
      {
         //alert(hashName);
         customHashes[hashName] = new HashListener(hashName);
         hashValue = customHashes[hashName].hash;
      }
      else
      {
         this.removeURLHandler("", hashName);
         hashValue = customHashes[hashName].hash;
      }
   }
   if (hashValue.indexOf("#") !== -1)
   {
      hashValue = hashValue.substring(1);
   }
   var pairs = hashValue.split("&");
   var newHash = "#";
   var and = false;


   /*if (hashValue.indexOf(key) == -1 && value == null)
    {
    return;
    }*/
   //alert(pairs.length + " " + value);
   // Existed keys
   $.each(pairs, function (i, v)
   {
      var keyAndValue = v.split("=");
      var keyExisted = false;

      // set new value for existed key
      if (parameters[keyAndValue[0]] != null)
      {
         if (and)
         {
            newHash += "&";
            and = false;
         }
         newHash += keyAndValue[0] + "=" + parameters[keyAndValue[0]];
         and = true;
         delete parameters[keyAndValue[0]];
      }
      // Set pervious value for existed key
      else if (!parameters.hasOwnProperty(keyAndValue[0]) && v)
      {
         if (and)
         {
            newHash += "&";
            and = false;
         }
         newHash += keyAndValue[0] + "=" + keyAndValue[1];
         and = true;
      }
      //alert(newHash);
   });

   // New keys
   $.each(parameters, function (key, value)
   {
      if (key && value)
      {
         if (and && !newHash.match(/&$/))
         {
            newHash += "&";
         }
         newHash += key + "=" + value;
         and = true;
      }
   });
   // set newHash for corresponding hash name if it has been passed
   if (hashName)
   {
      customHashes[hashName].hash = newHash;
      //alert(customHashes[hashName].hash);
   }
   // set url hash if no hash name specified
   else
      window.location.hash = newHash;
};


EverythingWidgets.prototype.getHashParameter = function (key, hashName)
{
   var hashValue = window.location.hash;


   if (customHashes[hashName])
   {
      hashValue = customHashes[hashName].hash;
   }
   else if (hashName)
   {
      return null;
   }
   if (hashValue.indexOf("#") !== -1)
   {
      hashValue = hashValue.substring(1);
   }
   var pairs = hashValue.split("&");
   for (test in pairs)
   {
      var keyAndValue = pairs[test].split("=");
      if (keyAndValue[0] === key)
      {
         return keyAndValue[1];
      }
   }
   return null;
};

EverythingWidgets.prototype.loadMainContent = function (url)
{
   $.post(url, function (data) {
      $("#main-content").html(data);
   });
};

EverythingWidgets.prototype.setCurrentTab = function (obj)
{
   if (obj)
   {
      //alert(obj.parent().parent().html());
      obj.parent().parent().find("a.selected").removeClass("selected");
   }
   obj.addClass("selected");
   this.currentTab = obj;
};

EverythingWidgets.prototype.lock = function (obj, string)
{
   var self = this;
   var settings = {
      class: "",
      text: ""
   };
   var glass = $(document.createElement("div"));
   glass.addClass("glass-pane-lock");
   glass.css({
      position: "absolute",
      top: "0px",
      left: "0px",
      right: "0px",
      bottom: "0px",
      opacity: 0
   });
   if (!string)
      glass.html("<span class='loader'></span>");
   else if (typeof (string) == "object")
   {
      $.extend(settings, string);
      glass.addClass(settings.class);
      if (settings.text)
         glass.html("<span>" + settings.text + "</span>");
   }
   else
      glass.html("<span>" + string + "</span>");
   $(obj).append(glass);

   var height = $(obj).outerHeight(true) === 0 ? "100%" : $(obj).outerHeight(true) - 20;

   glass.animate({
      opacity: 1
   },
   0);
   return glass;
};

EverythingWidgets.prototype.unlock = function (obj)
{
   var ll = $(obj).children(".glass-pane-lock:not(.unlock)").last();
   ll.addClass("unlock").animate({
      opacity: 0
   },
   0, function ()
   {
      $(this).remove();
   });
};


EverythingWidgets.prototype.createHeadersRow = function (headers)
{
   var tr = $(document.createElement("tr"));
   $.each(headers, function (k, v) {
      var th = $(document.createElement("th"));
      th.css("width", v.width);
      th.css("display", v.display);
      th.html(k);
      tr.append(th);
   });
   return tr;
};

function EWTable()
{
   var $base = this;
   this.config = {
   };
   this.container = $("<div class='report row'></div>");
   this.tableHeaderDiv = $("<div class='table-header' ></div>");
   this.tableBodyDiv = $("<div class='table-body'></div>");
   this.tableContainer = $("<div class='table-container'></div>");
   this.table;
   this.controls = $("<div class='controls'></div>");
   this.dynamicHeader = $();
   this.token = 0;
   this.pageSize = 10;
   this.url;
   this.cmd;
   this.data;
   this.next;
   this.previous;
   this.pageInfo;

   this.tableContainer.append(this.tableHeaderDiv);
   this.tableContainer.append(this.tableBodyDiv);

   this.container.append(this.controls);
   this.container.append(this.tableContainer);

   this.tableHeaderDiv.css({
      position: "absolute",
      display: "none",
      zIndex: "2"
   });
   this.tableBodyDiv.scroll(function ()
   {

      if ($(this).scrollTop() > 0 && !$base.tableHeaderDiv.is(":visible"))
      {
         $base.tableHeaderDiv.css("width", $base.table.outerWidth());
         $.each($base.headers.children(), function (k, v)
         {
            $(v).css({
               width: $base.dynamicHeader.children().eq(k).css("width")
            });
         });
         $base.tableHeaderDiv.show();
      }
      else if ($(this).scrollTop() <= 0 && $base.tableHeaderDiv.is(":visible"))
         $base.tableHeaderDiv.stop().animate({
            height: "toggle"
         },
         200);
      $base.tableHeaderDiv.css("left", $base.table.position().left);
   });
   // add listener to the windows width in the case of resize, the listener is added only once
   if (!$.data($base.tableHeaderDiv, "responsive"))
   {
      $base.tableHeaderDiv.data("responsive", true);
      $(window).resize(function ()
      {
         $base.tableHeaderDiv.css("width", $base.table.outerWidth());
         $.each($base.headers.children(), function (k, v)
         {
            $(v).css({
               width: $base.dynamicHeader.children().eq(k).css("width")
            });
         });
      });
   }
}

EWTable.prototype.createRow = function (val, rc)
{
   var ewTable = this;
   var tableRow = $(document.createElement("tr"));
   tableRow.data("field-id", val.id);
   tableRow.attr("data-field-id", val.id);
   if (ewTable.config.onClick)
   {
      tableRow.click(function () {
         ewTable.config.onClick(tableRow.data("field-id"));
      });
   }
   if (ewTable.config.ondblClick)
   {
      tableRow.dblclick(function () {
         ewTable.config.ondblClick(tableRow.data("field-id"));
      });
   }

   var actionsCell = $(document.createElement("td"));
   if (ewTable.config.onEdit)
   {
      var edit = $(document.createElement("button"));
      edit.attr("type", "button");
      edit.attr("data-label", "Edit");
      edit.addClass("btn edit");
      edit.click(function ()
      {
         ewTable.config.onEdit.apply(tableRow, new Array(tableRow.data("field-id")));
      });
      actionsCell.append(edit);
   }
   if (ewTable.config.onDelete)
   {
      var del = $(document.createElement("button"));
      del.attr("type", "button");
      del.addClass("btn delete");
      del.click(function ()
      {
         tableRow.confirm = function (text, delFunction)
         {
            var oldCells = tableRow.children().detach();
            var div = $("<td class=row-block>").hide();
            div.attr("colspan", oldCells.length);
            div.append("<span>" + text + "</span>");

            var delBtn = $("<button type=button class='btn btn-danger'>Delete</button>");
            div.append(delBtn);
            delBtn.on("click", function () {

               if (delFunction.apply(tableRow, new Array(tableRow.data("field-id"))))
               {
                  ewTable.removeRow(tableRow.data("field-id"));
               }
            });

            var cancelBtn = $("<button type=button class='btn btn-white' style='float:right'>Cancel</button>");
            div.append(cancelBtn);
            cancelBtn.on("click", function () {
               tableRow.html(oldCells);
            });

            tableRow.html(div);
            div.fadeIn(300);
         };
         if (ewTable.config.onDelete.apply(tableRow, new Array(tableRow.data("field-id"))))
            tableRow.removeRow(tableRow.data("field-id"));
      });
      actionsCell.append(del);
   }

   if (ewTable.config.buttons)
   {
      $.each(ewTable.config.buttons, function (k, v)
      {
         var action = $(document.createElement("button"));
         action.attr("type", "button");
         action.addClass("btn btn-primary");
         action.text(k);
         if (v)
         {
            action.click(function () {
               v.apply(ewTable, new Array(tableRow));
            });
         }
         actionsCell.append(action);
      });
   }
   delete val.id;
   var index = rc;
   if (ewTable.config.rowCount)
   {

   }
   // When user spacify columns attribute 
   if (ewTable.config.columns)
   {
      $.each(ewTable.config.columns, function (k, v) {

         $.each(val, function (k, v) {
            tableRow.data("field-" + k, v);
         });
         $('<td>' + val[v] + '</td>').appendTo(tableRow);

         index++;
      });
   }
   else
   {
      $.each(val, function (k, v) {

         if (ewTable.headers.children().eq(index).css("display") !== "none")
         {
            //alert(k+" "+index);
            tableRow.data("field-" + k, v);
            $('<td>' + v + '</td>').appendTo(tableRow);
         }
         index++;
      });
   }
   if (actionsCell.children().length > 0)
   {
      tableRow.append(actionsCell);
   }
   return tableRow;
};

EWTable.prototype.listRows = function ()
{
   var self = this;
   var rc = self.token + 1;
   // With row number
   var rows = new Array();
   if (self.config.rowCount)
   {
      $.each(self.data.result, function (k, v) {
         var row = self.createRow(v, 1);
         row.prepend("<td>" + rc + "</td>");
         rows.push(row);
         rc++;
      });
   }
   // Withour row number
   else
   {
      $.each(self.data.result, function (k, v) {
         var row = self.createRow(v, 0);
         rows.push(row);

      });
   }
   self.table.empty();
   self.table.append(rows);
};
// read the table data from given url
EWTable.prototype.read = function (customURLData)
{
   var self = this;
   $.EW("lock", self.table);
   var urlData = $.extend({
   }, {
      token: self.token,
      size: self.pageSize
   },
   customURLData);
   //alert(JSON.stringify(urlData));
   $.ajax({type: "POST",
      url: self.url,
      data: urlData,
      dataType: "json",
      success: function (data) {
         //console.log(data);

         var tillRow = (self.token + self.pageSize);
         if (self.token + self.pageSize > data.totalRows)
         {
            self.next.css('visibility', 'hidden');
            tillRow = data.totalRows;
         }
         else
         {
            self.next.css('visibility', 'visible');
         }
         if (self.token <= 0)
         {
            self.previous.css('visibility', 'hidden');
         } else
         {
            self.previous.css('visibility', 'visible');
         }
         self.data = data;
         //self.table.empty();
         self.listRows();
         self.dynamicHeader = self.headers.clone();
         self.dynamicHeader.addClass("dynamic-header");
         self.table.prepend(self.dynamicHeader);

         self.pageInfo.text(self.token + "-" + tillRow + " of " + data.totalRows);

      },
      error: function (o) {
         //console.log(o);
         self.data = {result: []};
         self.table.empty();
         self.next.css('visibility', 'hidden');
         self.previous.css('visibility', 'hidden');
         /*self.listRows();
          self.dynamicHeader = self.headers.clone();
          self.dynamicHeader.addClass("dynamic-header");
          self.table.prepend(self.dynamicHeader);*/
         self.pageInfo.html(o.responseJSON.message);
         EW.customAjaxErrorHandler = true;
         /*if (data.status == 403)
          {
          
          data.result = {"message": "You dont have access"};
          }*/
      }
   });
   /*$.post(self.url, urlData, function (data) {
    //console.log(data);
    
    var tillRow = (self.token + self.pageSize);
    if (self.token + self.pageSize > data.totalRows)
    {
    self.next.css('visibility', 'hidden');
    tillRow = data.totalRows;
    }
    else
    {
    self.next.css('visibility', 'visible');
    }
    if (self.token <= 0)
    {
    self.previous.css('visibility', 'hidden');
    } else
    {
    self.previous.css('visibility', 'visible');
    }
    self.data = data;
    self.table.empty();
    self.listRows();
    self.dynamicHeader = self.headers.clone();
    self.dynamicHeader.addClass("dynamic-header");
    self.table.prepend(self.dynamicHeader);
    
    self.pageInfo.text(self.token + "-" + tillRow + " of " + data.totalRows);
    
    }, "json");*/
};

EWTable.prototype.refresh = function (data)
{
   this.read(data);
};

EWTable.prototype.removeRow = function (dataId)
{
   this.table.find("tr[data-field-id='" + dataId + "']").remove();
};

EverythingWidgets.prototype.createTable = function (conf)
{
   var ewTable = new EWTable();
   ewTable.config = conf;
   ewTable.pageSize = conf.pageSize;
   ewTable.url = conf.url;
   ewTable.data = conf.data;
   // create a div element with 'table-container' class which contains the table element
   var bodyTable = $(document.createElement("table"));
   bodyTable.addClass("data");
   bodyTable.attr("id", conf.name);
   ewTable.table = bodyTable;
   var next = $(document.createElement("button"));
   next.addClass("button next");
   next.click(function (e) {
      e.preventDefault();
      ewTable.token += ewTable.pageSize;
      ewTable.read();
   });
   ewTable.controls.append(next);
   ewTable.next = next;
   var previous = $(document.createElement("button"));
   previous.addClass("button previous");
   previous.click(function (e) {
      e.preventDefault();
      ewTable.token -= ewTable.pageSize;
      ewTable.read();
   });
   ewTable.controls.append(previous);
   var pageInfo = $(document.createElement("label"));
   pageInfo.css({
      float: "right",
      marginRight: "10px"
   });
   ewTable.controls.append(pageInfo);
   ewTable.pageInfo = pageInfo;
   ewTable.previous = previous;
   var hr = EW.createHeadersRow(conf.headers);
   if (conf.rowCount)
   {
      hr.prepend("<th class='no'></th>");
   }
   if (conf.onDelete || conf.onEdit || conf.buttons)
   {
      hr.append("<th class='actions'></th>");
   }
   var headerTable = $(document.createElement("table"));
   headerTable.addClass("data");
   headerTable.append(hr);
   //controls.append(headers);  
   ewTable.headers = hr;
   ewTable.tableHeaderDiv.append(headerTable);
   ewTable.tableBodyDiv.append(bodyTable);

   ewTable.read();
   return ewTable;
};


EverythingWidgets.prototype.addURLHandler = function (handler, hashName)
{
   var handlers = this.urlHandlers;
   //var newAdded = EW.newHandler;
   if (hashName)
   {
      // create new hash listener if new hash name has been passed
      if (!customHashes[hashName])
      {
         customHashes[hashName] = new HashListener(hashName);
         //handlers = customHashes[hashName].handlers;
      }
      else
      {
         //handlers = customHashes[hashName].handlers;
      }
      customHashes[hashName].addHandler(hashName, handler);
   }
   else
   {
      //var exist = false;
      for (var i = 0; i < handlers.length; i++) {
         //alert(handlers[i] + " " + handler);
         if (" " + handlers[i] == " " + handler) {
            handlers[i] = null;
            handlers[i] = handler;
            return;
         }
      }
      handlers.push(handler);
      this.urlHandlers = handlers;
   }
   /*for (var i = 0; i < handlers.length; i++)
    {
    // alert(' ' + EW.urlHandlers[i] + ' ' + handler);
    if (' ' + handlers[i] === ' ' + handler)
    return;
    }*/

   //this.removeURLHandler(handler, hashName);
   //alert(hashName);

   //handlers.push(handler);
   if (hashName)
   {
      customHashes[hashName].newHandler = true;
   }
   else
      this.newHandler = true;
   return handler;
   //handler.call();
};
EverythingWidgets.prototype.removeURLHandler = function (handler, hashName)
{
   var handlers = this.urlHandlers;
   //var newAdded = EW.newHandler;
   if (customHashes[hashName])
   {
      handlers = customHashes[hashName].handlers;
   }
   for (var i = 0;
           i < handlers.length;
           i++)
   {

      if (' ' + handlers[i] === ' ' + handler)
      {
         handlers.splice(i, 1);
         i = 0;
      }
      //return;
   }
};



function hashHandler()
{
   this.oldHash = window.location.hash;
   this.Check;
   //var that = this;

//$("#component-chooser").html(window.location.hash);
   var detect = function () {
      //$("#component-chooser").html(hashDetection.oldHash+' '+window.location.hash);
      if (this.oldHash !== window.location.hash || EW.newHandler)
      {
         EW.newHandler = false;
         this.oldHash = window.location.hash;
         /*if (EW.customFunction !== null)
          {
          if (EW.customFunction())
          {
          }
          }*/
         for (var i = 0;
                 i < EW.urlHandlers.length;
                 i++)
         {
            EW.urlHandlers[i].call();
         }
      }
   };

   this.Check = setInterval(function ()
   {
      detect();
   }, 50);
}




EverythingWidgets.prototype.createForm = function (json)
{
   var form = $("<form></form>");
   form.addClass("col-xs-12");
   var formStructure = json;
   if (typeof formStructure == 'object')
   {
      $.each(formStructure, function (k, v) {

         var row = $("<div></div>");
         row.addClass("col-xs-12 col-sm-12 col-md-6 col-lg-4");
         var label;
         var input;
         //create label for input if label string is not empty
         if (v.type != "hidden")
         {
            label = $("<label></label>");
            label.text(v.label);
            label.attr("for", k);
         }
         // create select element
         if (v.type == "select")
         {
            var container = $("<div></div>");
            container.addClass("text-field");
            input = $("<select></select>");
            input.attr({
               id: k,
               name: k
            });
            container.append(input);
            if (typeof v.defValue == 'object')
            {
               $.each(v.defValue, function (v_k, v_v) {
                  var opt = $("<option></option>");
                  opt.attr("value", v_k);
                  opt.text(v_v);
                  input.append(opt);
               });
            }
            row.append(label);
            row.append(container);
            form.append(row);
         }
         // create textarea element
         else if (v.type == "text")
         {
            input = $("<textarea></textarea>");
            input.attr({
               id: k,
               name: k
            });
            input.val(v.defValue);
            row.append(label);
            row.append(input);
            form.append(row);
         }
         // create label element
         else if (v.type == "label")
         {
            input = $("<label></label>");
            input.attr({
               id: k
            });
            input.text(v.defValue);
            row.append(label);
            row.append(input);
            form.append(row);
         }
         // create input element with given type
         else if (v.type == "hidden")
         {
            input = $("<input>");
            input.attr({
               type: v.type,
               id: k,
               name: k
            });
            input.val(v.defValue);
            form.append(input);
         }
         // create input element with given type
         else if (v.type)
         {
            input = $("<input>");
            input.attr({
               type: v.type,
               id: k,
               name: k
            });
            input.val(v.defValue);
            label.prepend(input);
            row.html(label);
            form.append(row);
         }
         // create input element with default type
         else
         {
            input = $("<input>");
            input.addClass("text-field");
            input.attr({
               id: k,
               name: k
            });
            input.val(v.defValue);
            row.append(label);
            row.append(input);
            form.append(row);
         }

      });
   }
   return form;
};

var notify_defaults = {
   type: 'success',
   closable: true,
   transition: 'fade',
   fadeOut: {
      enabled: true,
      delay: 20000
   },
   delay: 5000,
   message: "Done Successfully",
   onClose: function () {
   },
   onClosed: function () {
   },
   position: "nw"
};
function EWNotification(element, options)
{
// Element collection
   console.log(options);
   this.$element = $(element);
   this.$note = $('<div class="notification alert"></div>');
   this.options = $.extend(true, {}, notify_defaults, options);
   this.$note.css({
      position: "fixed",
      zIndex: 9999
   });
   this.$note.attr("data-alert", "true");
   this.$note.attr("data-position", this.options.position);

   if (this.options.position == "ne")
      this.$note.css("right", "0px");

   if (this.options.status)
      this.$note.addClass('alert-' + this.options.status);
   else
      this.$note.addClass('alert-success');

   if (!this.options.message && this.$element.data("message") !== '') // dom text
      this.$note.html(this.$element.data("message"));
   else
   {
      if (typeof this.options.message === 'object')
      {
         if (this.options.message.html)
            this.$note.html(this.options.message.html);
         else if (this.options.message.text)
            this.$note.text(this.options.message.text);
      }
      else
      {
         this.$note.html(this.options.message);
      }

   }

   var $this = this;
   if (this.options.closable)
      var link = $('<a class="close close-icon" href="#"></a>');
   //link.css({})
   link.on('click', function (e) {
      e.preventDefault();
      $this.closeNotification();
   });
   this.$note.prepend(link);
   // Show notification
   this.show();
   return this;
}

EWNotification.prototype.closeNotification = function () {

   this.options.onClose();
   $(this.$note).remove();
   this.options.onClosed();
   return false;
};

EWNotification.prototype.show = function () {
   //if (this.options.fadeOut.enabled)
   //var $to = this;  
   var top = 0;
   var left = 0;
   var position = this.options.position;
   var note = this.$note;

   var v = this.$element.find("div[data-alert][data-position='" + this.options.position + "']").last();
   //alert(v.length);
   this.$note.css("opacity", "0");
   //alert(v.length + "   " + this.$note.outerWidth());
   this.$element.append(this.$note);
   if (v.length > 0)
   {
      if (position == "ne" || position == "nw" || position == "n")
         top = v.outerHeight(true) + v.offset().top;

      if (position == "se" || position == "sw")
         top = v.offset().top - note.outerHeight(true);
   }
   if (position == "n")
   {
      left = ($(window).width() - this.$note.outerWidth()) / 2;
   }
   this.$note.css({
      top: top,
      marginLeft: "+=25",
      left: left
   });
   var $this = this;
   this.$note.stop().animate({
      marginLeft: "-=25",
      opacity: "1"
   },
   300, function () {
      if ($this.options.delay !== "stay")
         $this.$note.delay($this.options.delay || 3000).fadeOut('slow', $.proxy($this.closeNotification, $this));
   });
};

EWNotification.prototype.hide = function () {
   //if (this.options.fadeOut.enabled)
   //var $to = this;
   this.$note.delay(this.options.delay || 3000).fadeOut('slow', $.proxy(this.closeNotification, this));
   //else
   //onClose.call(this);
};

function EWFormValidator(element, options)
{
   var self = this;
   var errors = 0;
   var $form = $(element);
   var validateField = function (element, rule, errorsPanel)
   {
      //var errorFlag = false;
      var value = element.val();
//if(rule.indexOf("\\"))

      switch (rule)
      {
         case "r":
            if (!value)
            {
               errorsPanel.append("<li>This filed is required</li>");
               return false;
            }
            break;
         case "email":
            if (value)
            {
               var pattern = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
               if (!pattern.test(value))
               {
                  errorsPanel.append("<li>Invalid email address format</li>");
                  return false;
               }

            }
            else
            {
               return false;
            }
            break;
         case "url":
            if (value)
            {
               var pattern = /^(https?:\/\/)?((([a-z\d]([a-z\d-]*[a-z\d])*)\.)+[a-z]{2,}|((\d{1,3}\.){3}\d{1,3}))(\:\d+)?(\/[-a-z\d%_.~+]*)*(\?[;&a-z\d%_.~+=-]*)?(\#[-a-z\d_]*)?$/;
               if (!pattern.test(value))
               {
                  errorsPanel.append("<li>Invalid URL address format</li>");
               }
               return false;
            }
            break;
      }
      // range validation if exist
      var range = rule.match(/\[(\d*)-?(\d*)\]/);
      if (range)
      {
         //console.log(range);

         if (range[1] && value.length < range[1])
         {
            errorsPanel.append("<li>At least " + range[1] + " character</li>");
            return false;
         }
         if (range[2] && value.length > range[2])
         {
            errorsPanel.append("<li>Maximum length is " + range[2] + " character</li>");
            return false;
         }
      }
      var equalTo = rule.match(/eq\[(.*)\]/);
      if (equalTo)
      {
         var otherElement = $form.find("#" + equalTo[1]);
         if (value != otherElement.val())
         {
            errorsPanel.append("<li>Value should be equal to the value of " + otherElement.data("label") + " field</li>");
            return false;
         }
      }
      /*if (errorFlag)
       return false;*/
      //alert(rule);
      return true;
   };

   $form.find(".errors-list").remove();
   var inputs = $form.find("input, textarea, select");
   var errorPanel;
   $.each(inputs, function (i, elm) {
      var $currentElement = $(elm);

      if ($currentElement.data("validate"))
      {
         var rules = $currentElement.data("validate").split(",");
         if (!$currentElement.parent().attr("data-element-wrapper"))
         {
            $currentElement.EW().putInWrapper();
         }
         var wrapper = $currentElement.parent();
         if (wrapper.find(".errors-panel").length == 0)
            wrapper.append("<ul class='errors-list'>");
         errorPanel = $currentElement.parent().find(".errors-list");
         $.each(rules, function (i, val) {
            if (!validateField($currentElement, val, errorPanel))
            {
               errors++;
            }
         });
         //alert($currentElement.data("validate"));
      }
   });
   if (errors > 0)
   {
      return false;
   }
   errorPanel.remove();
   return true;
}


function ExtendableList(element, options)
{
   var base = this;
   this.$element = $(element);
   //this.$element.find("li:first-child").prepend('<div class="handle"></div>');

   this.firstItemClone = this.$element.find("li:first-child").clone();
   this.addNewRow = $("<button type='button' class='button'>Add</button>");
   this.addNewRow.on("click", function () {
      base.addNewRow.before(base.createItem());
   });
   var lastRow = $("<div data-add-item-row='true' class='row'><div class='col-xs-12'></div></div>");
   lastRow.children().append(this.addNewRow);


   /*var ar=options.value[0].split(',');
    if(ar.length>0)
    {
    alert(ar[0]);
    }*/
   base.$element.empty();
   var init = false;
   var oneValue = false;
   var items = new Array();
   var ci = null;
   $.each(options.value, function (k, v)
   {
      //alert(typeof (k)+" "+typeof (v));
      if (typeof (v) != "object")
      {
         if (!oneValue)
         {
            ci = base.createItem();
            oneValue = true;
            //init = true;
            items.push(ci);
         }

         ci.find("input[name='" + k + "']").val(v).change();
      }

      if (!oneValue)
      {
         if (!init)
         {
            for (var i = 0;
                    i < v.length;
                    i++)
            {
               ci = base.createItem();
               ci.find("input[name='" + k + "']").val(v[i]).change();
               items.push(ci);
               init = true;
            }

         }
         else
         {
            for (var i = 0;
                    i < v.length;
                    i++)
            {
               //alert(k);
               items[i].find("input[name='" + k + "']").val(v[i]).change();
            }
         }
         /*$.each(v, function (kk, vv)
          {
          console.log(k+"  "+v);
          $(items).find("input[name='" + k + "']").eq(kk).val(vv).change();
          //alert(vv + " " + kk + " " + k);
          });*/
      }
   });
   base.$element.append(items);
   //if (!init)
   //base.createItem();
   base.$element.append(lastRow);
   base.$element.sortable({
      handle: ".handle"
   });
}

ExtendableList.prototype.createItem = function ()
{
   //this.addNewRow.detach();
   //this.$element.find("[data-add-item-row]").remove();
   var originalModelClone = this.firstItemClone.clone();
   //var cRow = $("<div data-add-item-row='true' class='row'><div class='col-xs-12'></div></div>");
   //cRow.children().append(this.addNewRow);

   var controlRow = $("<div class='row control'></div>");

   var removeBtn = $("<button type='button' class='close-icon' ></button>");
   //removeBtn.css({position:"absolute",right:"15px",top:"0px",zIndex:1});
   removeBtn.click(function ()
   {
      originalModelClone.remove();
   });
   controlRow.append(removeBtn);

   //alert(this.$element.children().length);
   //cRow.children().append(removeBtn);
   originalModelClone.append(controlRow);
   //temp.append(cRow);
   return originalModelClone;
   //this.$element.append(cRow);
};

var ew_plugins;
$.EW = function ()
{
   var method = arguments[0];
   var args = Array.prototype.slice.call(arguments, 1);
   //alert(JSON.stringify(arguments[1]));
   if (EW[method])
   {
      return EW[method].apply(EverythingWidgets.prototype, args);
   }
};

var globalOptions = {
};
$.fn.EW = function (methodOrOptions)
{
   $.extend(globalOptions, methodOrOptions);
   $.extend(this, ew_plugins);
   return this;
};


ew_plugins = {
   notify: function (options) {
      return new EWNotification(this, options);
   },
   //Form validator
   validate: function (options) {
      return EWFormValidator(this, options);
   },
   putInWrapper: function (options) {
      var $element = $(this);
      if (!$element.parent().attr("data-element-wrapper"))
         $element.wrap('<div class="element-wrapper" style="position:relative" data-element-wrapper="true"></div>');
   },
   dynamicList: function (options) {
      var methods = {
         getJSON: function ()
         {
            var itemsJSON = {};
            var items = $(this).children("li");
            var i = 0;
            $.each(items, function (k, v) {
               itemsJSON[i++] = $(v).find("input").serializeJSON();
            });
            //alert(JSON.stringify(itemsJSON));
         }
      };
      var defaults = {};

      if (methods[options]) {
         return methods[options].apply(this, Array.prototype.slice.call(arguments, 1));
      }
      var settings = $.extend({}, defaults, options);
      //alert(this.html());
      return this.each(function () {
         if (!$.data(this, "ew-plugin-expandable-list"))
            $.data(this, "ew-plugin-expandable-list", new ExtendableList(this, settings));
      });
   },
   inputButton: function (options)
   {
      var defaults = {
         onClick: function (element) {
         },
         title: "button",
         class: "btn-primary",
         id: ""
      };
      function inputButton(element, options)
      {
         var base = this;
         //var $element = $(element);
         var $element = $(element);
         if ($element.prop('tagName').toUpperCase() !== 'INPUT' && $element.prop('tagName').toUpperCase() !== 'TEXTAREA')
         {
            return;
         }
         var settings = $.extend({
         }, defaults, options);

         if (!$element.parent().attr("data-element-wrapper"))
            $element.EW().putInWrapper();
         var wrapper = $element.parent();
         var inputBtn;
         var buttonsPanel = wrapper.find(".buttons-panel");
         // if the plugin has been called after again on same element         
         var exist = false;
         if ($element.attr("data-active-plugin-input-button"))
         {
            $.each(buttonsPanel.find("button"), function (i, e)
            {
               if ($(e).html() == settings.title)
                  exist = $(e);
            });
            // if the button is already exist then add the handler and break
            if (exist)
            {
               exist.click(function ()
               {
                  settings.onClick($element);
               });
               return;
            }
            inputBtn = $("<button type='button' class='btn btn-xs' style='min-width:24px;'>" + settings.title + "</button>");
            inputBtn.addClass(settings.class);
            inputBtn.attr("id", settings.id);
            inputBtn.css({
               float: "right"
            });
            buttonsPanel.append(inputBtn);
         }
         // If the plugin has been called for the first time
         else
         {
            buttonsPanel = $("<div class='buttons-panel'>");
            buttonsPanel.css({position: "absolute"});
            inputBtn = $("<button type='button' class='btn btn-xs' style='min-width:24px;'>" + settings.title + "</button>");
            inputBtn.addClass(settings.class);
            inputBtn.attr("id", settings.id);
            inputBtn.css({
               /* position: "absolute",
                right: $element.css("padding-right"),
                bottom: $element.css("padding-bottom")*/
               float: "right"
            });
            buttonsPanel.prepend(inputBtn);
            wrapper.prepend(buttonsPanel);
            buttonsPanel.css({
               right: $element.css("padding-right")
            });

            $element.attr("data-active-plugin-input-button", true);
            //alert();
            $element.css("padding-right", buttonsPanel.outerWidth() + (parseInt($element.css("padding-right")) * 2));
         }
         //alert("ha");
         inputBtn.click(function ()
         {
            settings.onClick($element);
         });
      }
      return this.each(function () {
         //if (!$.data(this, "ew-plugin-input-button")) {
         $.data(this, "ew_plugin_input_button", new inputButton(this, options));
         //}
      });
   },
};
