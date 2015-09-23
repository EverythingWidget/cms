
xtag.register('ew-menu', {
   // extend existing elements
   //extends: 'div',
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
(function ()
{
   var app = angular.module('app', [])
           .config(function ()
           {

           });
   /*app.service("ewTests", function ($window)
   {
      return function ()
      {
         //console.log($window);
      }
   });*/

   app.service("ewModal", function ($window)
   {
      return function (origin, text)
      {
         EW.createModal(origin, text);
      }
   });

   app.controller('Main', function ($scope)
   {
      $scope.cm = function (o)
      {
         ewModal(o ? o[0] || document.activeElement : document.activeElement);
      };
   });

   app.directive("ewModal", function ()
   {
      return {
         restrict: 'A',
         transclude: false,
         controller: function ($scope, $element, $attrs, ewModal)
         {
            $element.on("click", function ()
            {
               //console.log($attrs);
               ewModal($element[0], $attrs.ewModal || "");
            });
         }
      };
   });
   /*app.directive("ewMenu", function ()
   {
      var c =
              {
                 restrict: 'E',
                 transclude: false,
                 scope: {
                    animSpeed: '='
                 },
                 controller: function ($scope, $element)
                 {
                    $scope.title = $element.find("h1")[0];
                    $element.one("mouseenter", function ()
                    {
                       $scope.open();
                    });

                    $scope.list = angular.element($element.find("ul")[0]);
                    $scope.list.css("position", "absolute");
                    $scope.list.addClass("menu-list");

                    $scope.open = function ()
                    {
                       $scope.updatePosition();
                       document.body.appendChild($scope.list[0]);
                       EW.animation.scaleTransform({
                          from: $element[0],
                          to: $scope.list[0],
                          time: .3});
                       $scope.list.one("mouseleave", function ()
                       {
                          $scope.close(true);
                       });
                       $scope.list.one("click", function ()
                       {
                          $scope.close();
                       });
                       $scope.opened = true;
                    };

                    $scope.close = function (fast)
                    {
                       var self = this;
                       if (!$scope.opened)
                          return;
                       EW.animation.scaleTransform({
                          from: $scope.list[0],
                          to: $element[0],
                          time: .2});
                       $scope.list.detach();
                       setTimeout(function ()
                       {
                          $element.one("mouseenter", function ()
                          {
                             $scope.open();
                          });
                       }, fast ? 0 : 800);
                       this.opened = false;
                    };

                    $scope.updatePosition = function ()
                    {
                       var dim = $element[0].getBoundingClientRect();
                       $scope.list.css(
                               {
                                  minWidth: dim.width + 'px',
                                  left: dim.left + 'px',
                                  top: dim.top + 'px'
                               });
                    };

                 },
                 //template: "<div></div>",
                 replace: true
              };
      return c;
   });

   app.directive("ewCard", function ()
   {
      var c =
              {
                 restrict: 'E',
                 transclude: true,
                 scope:
                         {
                            title: '@',
                            secTitle: '@'
                         },
                 controller: function ($scope, $element, $attrs)
                 {
                 },
                 templateUrl: "js/lib/EWCard.html"
              };
      return c;
   });*/
})();