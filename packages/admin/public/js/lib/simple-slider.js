/*
 jQuery Simple Slider
 
 Copyright (c) 2012 James Smith (http://loopj.com)
 
 Licensed under the MIT license (http://mit-license.org/)
 */
var __slice = [].slice,
        __indexOf = [].indexOf || function (item) {
   for (var i = 0, l = this.length; i < l; i++) {
      if (i in this && this[i] === item)
         return i;
   }
   return -1;
};

(function ($)
{
   var SimpleSlider;
   SimpleSlider = (function () {

      function SimpleSlider(input, options) {
         var ratio, _this = this;
         this.currentValueLabel = $("<label>").css({position: "absolute", width: "100px", marginLeft: "-50px"}).addClass("current");
         this.minValueLabel = $("<label>").css({float: "left"}).addClass("min");
         this.maxValueLabel = $("<label>").css({float: "right"}).addClass("max");
         this.input = input;
         this.defaultOptions = {
            animate: true,
            snapMid: false,
            classPrefix: null,
            classSuffix: null,
            theme: null,
            highlight: false,
            labels: true
         };
         //alert(input.html());
         this.settings = $.extend({}, this.defaultOptions, options);
         if (this.settings.theme) {
            this.settings.classSuffix = "-" + this.settings.theme;
         }
         // Put the element inside EW wrapper
         if (!this.input.parent().attr("data-element-wrapper"))
            this.input.EW().putInWrapper();
         this.slider = this.input.parent();
         this.input.hide();
         this.slider.addClass("slider" + (this.settings.classSuffix || "")).css({
            userSelect: "none",
            boxSizing: "border-box",
         });
         // Add the input classes to the slider
         this.slider.addClass(this.input.attr("class"));
         if (this.input.attr("id")) {
            this.slider.attr("id", this.input.attr("id") + "-slider");
         }
         this.track = this.createDivElement("track").css({
            width: "100%"
         });
         if (this.settings.highlight) {
            this.highlightTrack = this.createDivElement("highlight-track").css({
               width: "0"
            });
         }

         this.dragger = this.createDivElement("dragger");
         this.slider.css({
            minHeight: this.dragger.outerHeight()/*,
             marginLeft: this.dragger.outerWidth() / 2,
             marginRight: this.dragger.outerWidth() / 2*/
         });
         this.track.css({
            marginTop: this.track.outerHeight() / -2
         });
         if (this.settings.highlight) {
            this.highlightTrack.css({
               marginTop: this.track.outerHeight() / -2
            });
         }
         this.dragger.css({
            marginTop: this.dragger.outerHeight() / -2,
            marginLeft: this.dragger.outerWidth() / -2
         });
         // Add the labels to the slider bar
         if (this.settings.labels) {

            this.currentValueLabel.css({bottom: this.dragger.outerHeight()});
            this.minValueLabel.css({marginTop: this.dragger.outerHeight()});
            this.maxValueLabel.css({marginTop: this.dragger.outerHeight()});
            this.slider.append(this.minValueLabel);
            this.minValueLabel.text(this.getRange().min);
            this.slider.append(this.maxValueLabel);
            this.maxValueLabel.text(this.getRange().max);
            this.slider.append(this.currentValueLabel);
            this.slider.css({marginTop: this.currentValueLabel.outerHeight(true)});
         }

         this.track.mousedown(function (e) {
            return _this.trackEvent(e);
         });
         if (this.settings.highlight) {
            this.highlightTrack.mousedown(function (e) {
               return _this.trackEvent(e);
            });
         }
         this.dragger.mousedown(function (e) {
            if (e.which !== 1) {
               return;
            }
            _this.dragging = true;
            _this.dragger.addClass("dragging");
            _this.domDrag(e.pageX, e.pageY);
            return false;
         });
         $("body").mousemove(function (e) {
            if (_this.dragging) {
               _this.domDrag(e.pageX, e.pageY);
               return $("body").css({
                  cursor: "pointer"
               });
            }
         }).mouseup(function (e) {
            if (_this.dragging) {
               _this.dragging = false;
               _this.dragger.removeClass("dragging");
               return $("body").css({
                  cursor: "auto"
               });
            }
         });
         this.pagePos = 0;
         if (this.input.val() === "") {
            this.value = this.getRange().min;

         } else {
            this.value = this.nearestValidValue(this.input.val());

         }
         this.input.val(this.value)
         this.syncSliderUI();
         this.setSliderPositionFromValue(this.value);
         ratio = this.valueToRatio(this.value);
         this.input.trigger("slider:ready", {
            value: this.value,
            ratio: ratio,
            position: ratio * this.slider.width(),
            el: this.slider
         });
         this.input.on("change", function (e, d) {
            if (!e.value)
               _this.setValue((_this.nearestValidValue(_this.input.val())));
            //alert(d+" "+JSON.stringify(e));
         });

      }

      SimpleSlider.prototype.syncSliderUI = function ()
      {
         var $this = this;
         var oldWidth;
         setInterval(function ()
         {
            if (oldWidth != $this.slider.outerWidth())
            {
               oldWidth = $this.slider.outerWidth();
               $this.setValue($this.value);
               //$this.slider.css({left: $this.slider.css("padding-left"), right: $this.slider.css("padding-right")});
               $this.track.css({width: $this.slider.width()});
               //$this.dragger.css({marginLeft: (this.dragger.outerWidth() / -2) + $this.slider.css("padding-left")});
//          $this.highlightTrack.css({width: $this.slider.width()});
            }
         }, 30);
      };

      SimpleSlider.prototype.createDivElement = function (classname) {
         var item;
         item = $("<div>").addClass(classname).css({
            position: "absolute",
            top: "50%",
            userSelect: "none",
            cursor: "pointer"
         }).appendTo(this.slider);
         return item;
      };

      SimpleSlider.prototype.setRatio = function (ratio) {
         var value;
         ratio = Math.min(1, ratio);
         ratio = Math.max(0, ratio);
         value = this.ratioToValue(ratio);
         this.setSliderPositionFromValue(value);
         return this.valueChanged(value, ratio, "setRatio");
      };

      SimpleSlider.prototype.setValue = function (value) {
         if (this.input.is(":disabled"))
         {
            //alert("haa");
            return;
         }
         var ratio;
         value = this.nearestValidValue(value);
         ratio = this.valueToRatio(value);
         this.setSliderPositionFromValue(value);
         return this.valueChanged(value, ratio, "setValue");
      };

      SimpleSlider.prototype.trackEvent = function (e) {
         if (e.which !== 1) {
            return;
         }
         this.domDrag(e.pageX, e.pageY, true);
         this.dragging = true;
         return false;
      };

      SimpleSlider.prototype.domDrag = function (pageX, pageY, animate) {
         if (this.input.is(":disabled"))
         {
            //alert("haa2");
            return;
         }
         var pagePos, ratio, value;
         if (animate == null) {
            animate = false;
         }
         pagePos = pageX - (this.slider.offset().left + (this.slider.outerWidth() - this.slider.width()) / 2);
         pagePos = Math.min(this.slider.width(), pagePos);
         pagePos = Math.max(0, pagePos);
         if (this.pagePos !== pagePos) {
            this.pagePos = pagePos;
            ratio = pagePos / this.slider.width();
            value = this.ratioToValue(ratio);
            this.valueChanged(value, ratio, "domDrag");
            if (this.settings.snap) {
               return this.setSliderPositionFromValue(value, animate);
            } else {
               return this.setSliderPosition(pagePos, animate);
            }
         }
      };

      SimpleSlider.prototype.setSliderPosition = function (position, animate) {
         if (animate == null) {
            animate = false;
         }

         if (animate && this.settings.animate) {
            this.dragger.stop().animate({
               left: position + parseInt(this.slider.css("paddingLeft"))
            }, 200);
            this.currentValueLabel.stop().animate({
               left: position + parseInt(this.slider.css("paddingLeft"))
            }, 200);
            if (this.settings.highlight) {
               return this.highlightTrack.stop().animate({
                  width: position
               }, 200);
            }
         } else {
            this.dragger.css({
               left: position + parseInt(this.slider.css("paddingLeft"))
            });
            this.currentValueLabel.css({
               left: position + parseInt(this.slider.css("padding-left"))
            });
            if (this.settings.highlight) {
               return this.highlightTrack.css({
                  width: position
               });
            }
         }
      };

      SimpleSlider.prototype.setSliderPositionFromValue = function (value, animate) {
         var ratio;
         if (animate == null) {
            animate = false;
         }
         ratio = this.valueToRatio(value);

         //alert(ratio + " " + this.slider.outerWidth());
         return this.setSliderPosition(ratio * this.slider.width(), animate);
      };

      SimpleSlider.prototype.getRange = function () {
         if (this.settings.allowedValues) {
            return {
               min: Math.min.apply(Math, this.settings.allowedValues),
               max: Math.max.apply(Math, this.settings.allowedValues)
            };
         } else if (this.settings.range) {
            return {
               min: parseFloat(this.settings.range[0]),
               max: parseFloat(this.settings.range[1])
            };
         } else {
            return {
               min: 0,
               max: 1
            };
         }
      };

      SimpleSlider.prototype.nearestValidValue = function (rawValue) {
         var closest, maxSteps, range, steps;
         range = this.getRange();
         rawValue = Math.min(range.max, rawValue);
         rawValue = Math.max(range.min, rawValue);
         if (this.settings.allowedValues) {
            closest = null;
            $.each(this.settings.allowedValues, function () {
               if (closest === null || Math.abs(this - rawValue) < Math.abs(closest - rawValue)) {
                  return closest = this;
               }
            });
            return closest;
         }
         else if (this.settings.step)
         {
            maxSteps = (range.max - range.min) / this.settings.step;
            steps = Math.floor((rawValue - range.min) / this.settings.step);
            if ((rawValue - range.min) % this.settings.step > this.settings.step / 2 && steps < maxSteps) {
               steps += 1;
            }
            return steps * this.settings.step + range.min;
         } else {
            return rawValue;
         }
      };

      SimpleSlider.prototype.valueToRatio = function (value) {
         var allowedVal, closest, closestIdx, idx, range, _i, _len, _ref;
         if (this.settings.equalSteps) {
            _ref = this.settings.allowedValues;
            for (idx = _i = 0, _len = _ref.length; _i < _len; idx = ++_i) {
               allowedVal = _ref[idx];
               if (!(typeof closest !== "undefined" && closest !== null) || Math.abs(allowedVal - value) < Math.abs(closest - value)) {
                  closest = allowedVal;
                  closestIdx = idx;
               }
            }
            if (this.settings.snapMid) {
               return (closestIdx + 0.5) / this.settings.allowedValues.length;
            } else {
               return closestIdx / (this.settings.allowedValues.length - 1);
            }
         } else {
            range = this.getRange();
            return (value - range.min) / (range.max - range.min);
         }
      };

      SimpleSlider.prototype.ratioToValue = function (ratio) {
         var idx, range, rawValue, step, steps;
         if (this.settings.equalSteps) {
            steps = this.settings.allowedValues.length;
            step = Math.round(ratio * steps);
            idx = Math.min(step, this.settings.allowedValues.length - 1);
            return this.settings.allowedValues[idx];
         }
         else
         {
            range = this.getRange();
            rawValue = ratio * (range.max - range.min) + range.min;
            //console.log(rawValue);
            return this.nearestValidValue(rawValue);
         }
      };

      SimpleSlider.prototype.valueChanged = function (value, ratio, trigger) {
         var eventData;
         if (value.toString() === this.value.toString()) {
            return;
         }
         this.value = value;
         this.currentValueLabel.text(this.value);
         eventData = {
            value: value,
            ratio: ratio,
            position: ratio * this.slider.width(),
            trigger: trigger,
            el: this.slider
         };
         return this.input.val(value).trigger($.Event("change", eventData)).trigger("slider:changed", eventData);
      };

      return SimpleSlider;

   })();

   $.fn.simpleSlider = function ()
   {
      var params, publicMethods, settingsOrMethod;
      settingsOrMethod = arguments[0], params = 2 <= arguments.length ? __slice.call(arguments, 1) : [];
      publicMethods = ["setRatio", "setValue"];
      return $(this).each(function () {
         var obj, settings;
         if (settingsOrMethod && __indexOf.call(publicMethods, settingsOrMethod) >= 0)
         {
            obj = $(this).data("slider-object");
            return obj[settingsOrMethod].apply(obj, params);
         }
         else
         {
            settings = settingsOrMethod;
            if (!settings)
            {
               settings = {};
               var $el, allowedValues, settings, x;
               $el = $(this);
               allowedValues = $el.data("slider-values");
               if (allowedValues) {
                  settings.allowedValues = (function () {
                     var _i, _len, _ref, _results;
                     _ref = allowedValues.split(",");
                     _results = [];
                     for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                        x = _ref[_i];
                        _results.push(parseFloat(x));
                     }
                     return _results;
                  })();
               }
               if ($el.data("slider-range")) {
                  settings.range = $el.data("slider-range").split(",");
               }
               if ($el.data("slider-step")) {
                  settings.step = $el.data("slider-step");
               }
               settings.snap = $el.data("slider-snap");
               settings.equalSteps = $el.data("slider-equal-steps");
               if ($el.data("slider-theme")) {
                  settings.theme = $el.data("slider-theme");
               }
               if ($el.attr("data-slider-highlight")) {
                  settings.highlight = $el.data("slider-highlight");
               }
               if ($el.data("slider-animate") != null) {
                  settings.animate = $el.data("slider-animate");
               }
            }
            if (!$(this).attr("data-slider-active"))
            {
               $(this).attr("data-slider-active", true);
               return $(this).data("slider-object", new SimpleSlider($(this), settings));
            }
         }
      });
   };
   return $(function () {
      return $("[data-slider]").each(function () {
         var $el, allowedValues, settings, x;
         $el = $(this);
         return $el.simpleSlider();
      });
   });
})(jQuery);
