(function (Sys) {
   Sys.UI = new UI();

   function UI() {
      this.components = {};
      this.data = {};
   }

   UI.prototype.util = {
      call: function (o, f) {
         if ('undefined' !== typeof (f)) {
            f.call(o);
         }
      }
   };

   UI.prototype.addComponent = function (component) {
      this.components[component.id] = component;
      this.util.call(component, component.init);
      this.util.call(component, component.attach);
   };

})(System);