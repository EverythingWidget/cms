/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
'use strict';


define({
   ranges: {},
   allColumnsNames: [],
   addRange: function (range)
   {
      //console.log(range)
      this.ranges[range.prefix] = range;
      range.columnsNames = this.generateColumns(range.prefix, range.columns);
      //this.allColumnsNames.push(range.columnsNames);
      //range.columnsBaseCSS = this.generateColumnsCSS(range.columnsNames);
      range.columnsCSS = this.generateColumnsCSS(range);
      this.updateCSS();
   },
   generateColumns: function (prefix, columns)
   {
      var columnsNames = [];
      for (var i = 1; i <= columns; i++)
      {
         columnsNames.push('.' + prefix + i);
      }
      return columnsNames;
   },
   generateQueryCss: function (range)
   {
      var queryCSS = "@media (min-width: " + range.min + "px) {";
      return queryCSS;
   },
   generateBaseCSS: function (range)
   {
      var columnsBaseCSS = " { position:relative; float:left; min-height: 1px;";
      columnsBaseCSS += "padding-left: " + range.gutter + "; ";
      columnsBaseCSS += "padding-right: " + range.gutter + "; }\n";
      return range.columnsNames.join(',') + columnsBaseCSS;
   },
   generateColumnsCSS: function (range)
   {
      var columnsCSS = [this.generateBaseCSS(range)];
      columnsCSS.push("@media (min-width: " + range.min + "px) {");

      var baseSize = 100 / range.columns;
      for (var i = 0, len = range.columns; i < len; i++)
      {
         columnsCSS.push(range.columnsNames[i] + " { width: " + ((i + 1) * baseSize) + "%; }\n");
      }
      columnsCSS.push("}\n\n");
      return columnsCSS.join(' ');
   },
   generateCSS: function (columns)
   {

   },
   updateCSS: function ()
   {
      var styleElement = document.getElementById("grid_on_air");
      if (!styleElement)
      {
         styleElement = document.createElement("style");
         styleElement.id = "grid_on_air";
         document.getElementsByTagName('head')[0].appendChild(styleElement);
      }
      styleElement.innerHTML = "";

      for (var r in this.ranges)
      {
         console.log(this.ranges[r]);
         styleElement.innerHTML += this.ranges[r].columnsCSS;
      }
   }
});