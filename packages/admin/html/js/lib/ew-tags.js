'use strict';

define(function ()
{
   xtag.register('ew-menu', {
      // extend existing elements
      extends: 'div',
      lifecycle: {
         created: function ()
         {
            var self = this;
            // fired once at the time a component
            // is initially created or parsed
            this.listEl = this.getElementsByTagName("ul")[0];
            this.listEl.style.position = "absolute";
            this.listEl.classList.add("menu-list");

            this.updatePosition();

            this.addEventListener("mouseenter", self.open);
            this.addEventListener("click", self.open);
            this.listEl.addEventListener("mouseleave", function (e)
            {
               self.close(e, true);
            });
            this.listEl.addEventListener("click", function (e)
            {
               self.close(e);
            });
            ;
         },
         inserted: function ()
         {
            //console.log(this.events)
            // fired each time a component
            // is inserted into the DOM
         },
         removed: function ()
         {
            // fired each time an element
            // is removed from DOM
         },
         attributeChanged: function ()
         {
            // fired when attributes are set
         }
      },
      accessors:
              {
                 class:
                         {
                            attribute: {string: "class"} // creates a boolean attribute.  enabled=""
                         }
              },
      methods:
              {
                 open: function ()
                 {
                    this.removeEventListener("mouseenter", this.open);
                    this.removeEventListener("click", this.open);
                    this.updatePosition();
                    document.body.appendChild(this.listEl);
                    EW.animation.scaleTransform({
                       from: this,
                       to: this.listEl,
                       origin: this,
                       time: .3});
                    this.opened = true;
                 },
                 close: function (e, fast)
                 {
                    var self = this;
                    if (!this.opened)
                       return;
                    EW.animation.scaleTransform({
                       from: this.listEl,
                       to: this,
                       origin: this,
                       time: .2});
                    this.appendChild(this.listEl);
                    setTimeout(function ()
                    {
                       self.addEventListener("mouseenter", self.open);
                       self.addEventListener("click", self.open);
                    }, fast ? 100 : 800);
                    this.opened = false;
                 },
                 updatePosition: function ()
                 {
                    var dim = this.getBoundingClientRect();
                    this.listEl.style.minWidth = dim.width + "px";
                    this.listEl.style.top = dim.top + "px";
                    this.listEl.style.left = dim.left + "px";
                 },
                 previousToggler: function ()
                 {
                    // activate the previous toggler
                 }
              }
   });
   return xtag;
});
