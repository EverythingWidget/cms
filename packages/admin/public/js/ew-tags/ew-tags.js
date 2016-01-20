(function () {
   UIUtil = {
      viewRegex: /\{\{([^\{\}]*)\}\}/g,
      populate: function (template, data) {
         template = template.replace(this.viewRegex, function (match, key) {
            //eval make it possible to reach nested objects
            var val = eval("data." + key);
            return "undefined" === typeof val ? "" : val;
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
      hasCSSClass: function (element, className) {
         if (element.classList)
            return  element.classList.contains(className);
         else
            return new RegExp('(^| )' + className + '( |$)', 'gi').test(element.className);
      },
      addCSSClass: function (element, className) {
         if (this.hasCSSClass(element, className))
            return;
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
   /*function builder(elementClass) {

   }*/

   var EWListBuilder = function () {
      var Root = {
         _element: null,
         data: [],
         value: null,
         render: function (data) {
            //var data = this.data;
            this.innerHTML = "";
            var a = null;
            for (var i = 0, len = data.length; i < len; i++) {
               data[i]._itemIndex = i;
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
            if (Root.methods.onItemSelected) {
               Root.methods.onItemSelected(Root._element.data[i], i, element);
            }

            /*if (EW_List.selectedElement) {
             UIUtil.removeCSSClass(EW_List.selectedElement, "selected");
             }
             
             EW_List.selectedElement = element;
             UIUtil.addCSSClass(EW_List.selectedElement, "selected");*/
         }
      };
      Root.lifecycle = {
         created: function () {
            Root._element = this;
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
      Root.methods = {
         onSetData: function (data) {
         },
         onItemSelected: function (item, i, element) {
         }
      };

      Root.accessors = {
         data: {
            attribute: {},
            set: function (value) {

               Root.value = null;
               if ("object" !== typeof value) {
                  Root.data = [];
                  value = [];
                  //return;
               }

               Root.data = value;

               if (Root.methods.onSetData) {
                  Root.methods.onSetData(value);
               }

               Root.render.call(this, value);
            },
            get: function () {
               return Root.data;
            }
         },
         onItemSelected: {
            attribute: {},
            set: function (value) {
               Root.methods.onItemSelected = value;
            }
         },
         onSetData: {
            attribute: {},
            set: function (value) {
               Root.methods.onSetData = value;
            }
         },
         value: {
            attribute: {},
            set: function (value) {
               value = parseInt(value);

               if (value > -1 /*&& value !== this.value*/ && Root.data.length) {
                  Root.selectItem(value, this.links[value]);
               }

               Root.value = value;
            },
            get: function () {
               return Root.value;
            }
         }
      };

      Root.events = {
         "click:delegate(a)": function (e) {
            e.preventDefault();
            Root._element.value = e.target.dataset.index;
         }
      };
      return Root;
   };

   xtag.register("ew-list", EWListBuilder());
})(xtag);