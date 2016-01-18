(function () {
   UIUtil = {
      viewRegex: /\{\{([^\{\}]*)\}\}/g,
      populate: function (template, data) {
         template = template.replace(this.viewRegex, function (match, key) {
            //eval make it possible to reach nested objects
            return eval("data." + key) || "";
         });
         return template;
      },
      stringifyAttribute: function (element, attr, value) {
         if ("object" === typeof value) {
            value = JSON.stringify(value);
            //element.setAttribute(attr, value);
            //return false;
         }
         return value;
         //return true;
      },
      addCSSClass: function (element, className) {
         if (element.classList)
            element.classList.add(className);
         else
            element.className += ' ' + className;
      },
      removeCSSClass: function (element, className) {
         if (element.classList)
            element.classList.remove(className);
         else
            element.className = element.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
      }
   };
})();

(function (xtag) {

   var EW_List = {
      _element: null,
      data: [],
      value: null,
      render: function (data) {
         //var data = this.data;
         this.innerHTML = "";
         var a = null;
         for (var i = 0, len = data.length; i < len; i++) {
            var item = xtag.createFragment(UIUtil.populate(this.template, data[i]));
            a = xtag.query(item, "a")[0];
            
            if (data[i].id)
               this.links[data[i].id] = a;

            this.links[i] = a;
            a.dataset.index = i;

            this.appendChild(item);
         }
      },
      selectItem: function (i, element) {
         if (EW_List.methods.onItemSelected) {
            EW_List.methods.onItemSelected(EW_List._element.data[i], i, element);
         }

         if (EW_List.selectedElement) {
            UIUtil.removeCSSClass(EW_List.selectedElement, "selected");
         }

         EW_List.selectedElement = element;
         UIUtil.addCSSClass(EW_List.selectedElement, "selected");
      }
   };
   EW_List.lifecycle = {
      created: function () {
         EW_List._element = this;
         this.template = this.innerHTML;
         this.innerHTML = "";
         this.links = {};
         this.data = [];
         this.value = null;
      },
      inserted: function () {
      },
      removed: function () {
      },
      attributeChanged: function (attrName, oldValue, newValue) {
      }
   };
   EW_List.methods = {
      onSetData: function (data) {
      },
      onItemSelected: function (item, i, element) {
      }
   };

   EW_List.accessors = {
      data: {
         attribute: {},
         set: function (value) {
            
            EW_List.value = null;
            if ("object" !== typeof value) {
               EW_List.data = [];
               value = [];
               //return;
            }

            EW_List.data = value;

            if (EW_List.methods.onSetData) {
               EW_List.methods.onSetData(value);
            }

            EW_List.render.call(this, value);
         },
         get: function () {
            return EW_List.data;
         }
      },
      onItemSelected: {
         attribute: {},
         set: function (value) {
            EW_List.methods.onItemSelected = value;
         }
      },
      onSetData: {
         attribute: {},
         set: function (value) {
            EW_List.methods.onSetData = value;
         }
      },
      value: {
         attribute: {},
         set: function (value) {
            value = parseInt(value);

            if (value > -1 && value !== this.value && EW_List.data.length) {
               EW_List.selectItem(value, this.links[value]);
            }

            EW_List.value = value;
         },
         get: function () {
            return EW_List.value;
         }
      }
   };

   EW_List.events = {
      "click:delegate(a)": function (e) {
         e.preventDefault();
         EW_List._element.value = e.target.dataset.index;
      }
   };

   xtag.register("ew-list", EW_List);
})(xtag);