(function (xtag) {

   //var listUrl, viewUrl, editUrl, deleteUrl;
   function EW_Table() {
   }

   EW_Table.prototype.lifecycle = {};
   EW_Table.prototype.lifecycle.created = function () {
      var _this = this;
      var columns = [];

      for (var i = 0, len = this.children.length; i < len; i++) {
         if (this.children[i].tagName.toLowerCase() !== "table-column")
            continue;
         var child = _this.removeChild(this.children[i]);
         columns.push({
            name: child.getAttribute("name"),
            title: child.innerHTML
         });
         i--;
         len--;
      }
      
      var tempDom = document.createDocumentFragment();
      this.controlBar = xtag.queryChildren(this, "table-control-bar")[0];

      this.tableContainer = document.createElement("div");
      System.UI.util.addClass(this.tableContainer, "table-container");
      tempDom.appendChild(this.tableContainer);

      this.tableStickyHeader = document.createElement("div");
      System.UI.util.addClass(this.tableStickyHeader, "table-header");
      this.tableContainer.appendChild(this.tableStickyHeader);

      this.table = document.createElement("table");
      this.tableContainer.appendChild(this.table);

      this.tableHeader = document.createElement("thead");
      this.table.appendChild(this.tableHeader);

      this.tableBody = document.createElement("tbody");
      this.table.appendChild(this.tableBody);

      this.appendChild(tempDom);
      console.log($(this));
   };

   EW_Table.prototype.lifecycle.inserted = function () {
   };

   EW_Table.prototype.lifecycle.removed = function () {
   };

   EW_Table.prototype.lifecycle.attributeChanged = function (attrName, oldValue, newValue) {
   };

   EW_Table.prototype.methods = {
      read: function () {

      }
   };

   EW_Table.prototype.accessors = {};
   EW_Table.prototype.accessors.listUrl = {
      attribute: {},
      set: function (value) {
      }
   };

   xtag.register("ew-table", new EW_Table());
})(xtag);