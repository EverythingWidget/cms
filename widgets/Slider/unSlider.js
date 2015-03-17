/**
 *   Unslider by @idiot
 */

(function ($, f) {
   //  If there's no jQuery, Unslider can't work, so kill the operation.
   if (!$)
      return f;

   var Unslider = function () {
      //  Set up our elements
      this.el = f;
      this.items = f;

      //  Dimensions
      this.sizes = [];
      this.max = [0, 0];

      

      //  Start/stop timer
      this.interval = f;

      //  Set some options
      this.opts = {
         index: 0,
         speed: 500,
         delay: 3000, // f for no autoplay
         complete: f, // when a slide's finished
         keys: !f, // keyboard shortcuts - disable if it breaks things
         dots: f, // display â€¢â€¢â€¢â€¢oâ€¢ pagination
         fluid: f // is it a percentage width?,
      };
      
      //  Current inded
      this.index = 0;

      //  Create a deep clone for methods where context changes
      var self = this;

      this.init = function (el, opts) {
         this.el = el;
         this.ul = el.children('ul');
         this.max = [el.outerWidth(), el.outerHeight()];
         this.items = this.ul.children('li').each(this.calculate);

         //  Check whether we're passing any options in to Unslider
         this.opts = $.extend(this.opts, opts);

         //  Set up the Unslider
         this.setup();

         return this;
      };

      //  Get the width for an element
      //  Pass a jQuery element as the context with .call(), and the index as a parameter: Unslider.calculate.call($('li:first'), 0)
      this.calculate = function (index) {
         var me = $(this),
                 width = me.outerWidth(), height = me.outerHeight();

         //  Add it to the sizes list
         self.sizes[index] = [width, height];

         //  Set the max values
         if (width > self.max[0])
            self.max[0] = width;
         if (height > self.max[1])
            self.max[1] = height;
      };

      //  Work out what methods need calling
      this.setup = function () {
         //  Set the main element
         this.el.css({
            overflow: 'hidden',
            width: self.max[0],
            height: this.items.first().outerHeight()
         });

         //  Set the relative widths
         this.ul.css({width: (this.items.length * 100) + '%', position: 'relative'});
         this.items.css('width', (100 / this.items.length) + '%');

         if (this.opts.delay !== f) {
            this.start();
            this.el.hover(this.stop, this.start);
         }

         //  Custom keyboard support
         this.opts.keys && $(document).keydown(this.keys);

         //  Dot pagination
         this.opts.dots && this.dots();

         //  Little patch for fluid-width sliders. Screw those guys.
         if (this.opts.fluid) {
            var resize = function () {
               self.el.css('width', Math.min(Math.round((self.el.outerWidth() / self.el.parent().outerWidth()) * 100), 100) + '%');
            };

            resize();
            $(window).resize(resize);
         }

         if (this.opts.arrows) {
            this.el.parent().append('<p class="arrows"><span class="prev">â†</span><span class="next">â†’</span></p>')
                    .find('.arrows span').click(function () {
               $.isFunction(self[this.className]) && self[this.className]();
            });
         }
         

         //  Swipe support
         if ($.event.swipe) {
            this.el.on('swipeleft', self.prev).on('swiperight', self.next);
         }
      };

      //  Move Unslider to a slide index
      this.move = function (index, cb) {
         //  If it's out of bounds, go to the first slide
         if (!this.items.eq(index).length)
            self.opt.index = 0;
         if (index < 0)
            self.opt.index = (this.items.length - 1);

         var target = this.items.eq(index);
         var obj = {height: target.outerHeight()};
         var speed = cb ? 5 : this.opts.speed;

         if (!this.ul.is(':animated')) {
            //  Handle those pesky dots
            self.el.find('.dot:eq(' + index + ')').addClass('active').siblings().removeClass('active');

            this.el.animate(obj, speed) && this.ul.animate($.extend({left: '-' + index + '00%'}, obj), speed, function (data) {
               self.opt.index = index;
               $.isFunction(self.opts.complete) && !cb && self.opts.complete(self.el);
            });
         }
      };

      //  Autoplay functionality
      this.start = function () {
         self.interval = setInterval(function () {
            self.move(self.opt.index + 1);
         }, self.opts.delay);
      };

      //  Stop autoplay
      this.stop = function () {
         self.interval = clearInterval(self.interval);
         return self;
      };

      //  Keypresses
      this.keys = function (e) {
         var key = e.which;
         var map = {
            //  Prev/next
            37: self.prev,
            39: self.next,
            //  Esc
            27: self.stop
         };

         if ($.isFunction(map[key])) {
            map[key]();
         }
      };

      //  Arrow navigation
      this.next = function () {
         return self.stop().move(self.opt.index + 1)
      };
      this.prev = function () {
         return self.stop().move(self.opt.index - 1)
      };

      this.dots = function () {
         //  Create the HTML
         var html = '<ol class="dots">';
         $.each(this.items, function (index) {
            html += '<li class="dot' + (index < 1 ? ' active' : '') + '">' + (index + 1) + '</li>';
         });
         html += '</ol>';

         //  Add it to the Unslider
         this.el.addClass('has-dots').append(html).find('.dot').click(function () {
            self.move($(this).index());
         });
      };
   };

   //  Create a jQuery plugin
   $.fn.unslider = function (o) {
      var len = this.length;

      //  Enable multiple-slider support
      return this.each(function (index) {
         //  Cache a copy of $(this), so it 
         var me = $(this);
         var instance = (new Unslider).init(me, o);

         //  Invoke an Unslider instance
         me.data('unslider' + (len > 1 ? '-' + (index + 1) : ''), instance);
      });
   };
})(window.jQuery, false);