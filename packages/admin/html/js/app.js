requirejs.config({
   //By default load any module IDs from js/lib
   baseUrl: 'js/lib',
   //except, if the module ID starts with "app",
   //load it from the js/app directory. paths
   //config is relative to the baseUrl, and
   //never includes a ".js" extension since
   //the paths config could be for a directory.
   //paths: {
   //    app: '../app'
   //}
});

// Start the main app logic.
require(['grid-on-air', 'ew-tags'], function (goa)
{

   goa.outputTo = "result_css";
   /*goa.addRange(
    {
    base: 'col',
    prefix: 'xs-',
    min: 0,
    gutter: '5px',
    columns: 3
    
    });
    
    goa.addRange(
    {
    prefix: 'sm-',
    min: 600,
    gutter: '10px',
    columns: 12
    
    });
    goa.addRange(
    {
    prefix: 'md-',
    min: 960,
    gutter: '15px',
    columns: 12
    
    });
    
    goa.addRange(
    {
    prefix: 'lg-',
    min: 1340,
    gutter: '15px',
    columns: 12
    
    });
    goa.addRange(
    {
    base: 'col',
    baseStyle: "position:relative; float:left; min-height: 1px;",
    prefix: 'xs-',
    min: 0,
    gutter: '5px',
    columns: 3
    });
    
    goa.addRange(
    {
    //base: 'sm',
    //columnBaseStyle: "position:relative; float:left; min-height: 1px;",
    prefix: 'sm-',
    min: 600,
    gutter: '10px',
    columns: 12
    });
    goa.addRange(
    {
    //base: 'md',
    //columnBaseStyle: "position:relative; float:left; min-height: 1px;",
    prefix: 'md-',
    min: 960,
    gutter: '15px',
    columns: 12
    });
    
    goa.addRange(
    {
    //base: 'lg',
    //columnBaseStyle: "position:relative; float:left; min-height: 1px;",
    prefix: 'lg-',
    min: 1340,
    gutter: '15px',
    columns: 12
    });*/

   //goa.createGrid();
});


var EW = function ()
{
   var EW =
           {
              DEFAULTS:
                      {
                         animationDuration: 1
                      },
              COMPONENT_STRUCTURE:
                      {
                         el: null,
                         events: {},
                         on: function (event, handler)
                         {
                            this.events[event] = handler;
                         },
                         trigger: function (event)
                         {
                            if (this.events[event])
                               this.events[event].apply(this, Array.prototype.slice.call(arguments, 1));
                         }
                      },
              clone: function (obj)
              {
                 var target = {};
                 for (var i in obj) {
                    if (obj.hasOwnProperty(i))
                    {
                       target[i] = obj[i];
                    }
                 }
                 return target;
              },
              body: document.getElementsByTagName("body")[0],
              getCenterPoint: function (rect)
              {
                 var pos = document.activeElement.getBoundingClientRect();
                 return         {
                    left: rect.left + (rect.width / 2),
                    top: rect.top + (rect.height / 2)
                 };

              },
              createModal: function (ori)
              {
                 var lockPane;
                 var modal = EW.clone(EW.COMPONENT_STRUCTURE);
                 modal.close = function ()
                 {

                 };

                 modal.dispose = function ()
                 {

                 };

                 modal.remove = function ()
                 {
                    this.el.parentNode.removeChild(this.el);
                 }

                 modal.el = document.createElement("div"); //or use jQuery's $("#photo")
                 modal.el.className = "dialog center open";
                 modal.el.innerHTML = "<h1 class='dialog-header-bar'>This is the Dialog Title</h1><div class='dialog-content-pane'></div><div class='dialog-action-bar'></div>";
                 var origin = ori || document.activeElement;

                 var loadModal = setTimeout(function ()
                 {
                    lockPane = EW.lock(document.getElementsByClassName("app-pane")[0]);
                    document.getElementsByTagName("body")[0].appendChild(modal.el);
                    EW.animation.transform({
                       from: loader.el,
                       to: modal.el,
                       el: modal.el,
                       time: .4,
                       flow: true,
                       onComplete: function ()
                       {
                          loader.dispose();
                          origin.style.visibility = "hidden";

                       }
                    });

                 }, 1000);

                 var loader = EW.animation.toLoader(origin, "btn-loader");
                 loader.on("cancel", function ()
                 {
                    clearTimeout(loadModal);
                 });

                 //origin.style.opacity = "0";
                 modal.el.addEventListener("click", function ()
                 {
                    lockPane.dispose();
                    EW.animation.scaleTransform({
                       from: modal.el,
                       to: origin,
                       time: .3,
                       onComplete: function ()
                       {
                          origin.style.visibility = "";
                       }
                    });
                    modal.remove();
                 });
              },
              lock: function (e, t)
              {
                 t = t || EW.DEFAULTS.animationDuration;
                 var sourceRect = e.getBoundingClientRect();
                 var ss = window.getComputedStyle(e);
                 var lockPane = document.createElement("div");
                 lockPane.className = "lock-pane";
                 lockPane.style.position = "absolute";
                 lockPane.style.left = sourceRect.left;
                 lockPane.style.top = sourceRect.top;
                 lockPane.style.width = sourceRect.width + "px";
                 lockPane.style.height = sourceRect.height + "px";
                 lockPane.style.zIndex = (ss.zIndex === "0" || ss.zIndex === "auto") ? 1 : ss.zIndex;
                 lockPane.style.transition = "opacity " + t + "s";

                 e.parentNode.insertBefore(lockPane, e.nextSibling);
                 setTimeout(function ()
                 {
                    lockPane.classList.add("show");
                 }, 0);
                 lockPane.dispose = function ()
                 {
                    lockPane.parentNode.removeChild(lockPane);
                 };
                 return lockPane;
              },
              rgbToHex: function (rgb)
              {
                 rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
                 return (rgb && rgb.length === 4) ? "#" +
                         ("0" + parseInt(rgb[1], 10).toString(16)).slice(-2) +
                         ("0" + parseInt(rgb[2], 10).toString(16)).slice(-2) +
                         ("0" + parseInt(rgb[3], 10).toString(16)).slice(-2) : null;
              },
              animation:
                      {
                         transformBetween: function (conf)
                         {
                            var t = conf.time || EW.DEFAULTS.animationDuration;
                            var sourceRect = conf.from.getBoundingClientRect();
                            var distRect = conf.to.getBoundingClientRect();
                            var ss = window.getComputedStyle(conf.from);
                            var ds = window.getComputedStyle(conf.to);

                            TweenLite.fromTo(conf.el, t,
                                    {
                                       opacity: .1,
                                       left: sourceRect.left,
                                       top: sourceRect.top,
                                       borderRadius: distRect.width * parseInt(ss.borderRadius, 10) / sourceRect.width,
                                       margin: 0,
                                       transform: "scale(" + sourceRect.width / distRect.width + "," + sourceRect.height / distRect.height + ")",
                                       boxShadow: ss.boxShadow,
                                       transformOrigin: "0 0"
                                    },
                            {
                               opacity: 1,
                               left: ds.left,
                               top: ds.top,
                               margin: ds.margin,
                               transform: "scale(1,1)",
                               borderRadius: ds.borderRadius,
                               boxShadow: ds.boxShadow,
                               ease: conf.ease || "Power2.easeInOut",
                               onComplete: function ()
                               {
                                  if (conf.onComplete)
                                     conf.onComplete();
                               }
                            });
                         },
                         transform: function (conf)
                         {
                            var t = conf.time || EW.DEFAULTS.animationDuration;
                            var sourceRect = conf.from.getBoundingClientRect();
                            var distRect = conf.to.getBoundingClientRect();
                            //toE.style.opacity = '0';
                            var transformBox = document.createElement("div");
                            var ss = window.getComputedStyle(conf.from, null);
                            //console.log(ss);
                            var ds = window.getComputedStyle(conf.to, null);
                            transformBox.style.position = "absolute";
                            transformBox.style.backgroundColor = (ss.backgroundColor.indexOf("rgba") !== -1 ||
                                    ss.backgroundColor === "transparent") ? "rgb(190,190,190)" : ss.backgroundColor;
                            transformBox.style.boxShadow = ss.boxShadow;
                            transformBox.style.borderRadius = conf.from.style.borderRadius;
                            transformBox.style.padding = ss.padding;
                            transformBox.style.color = ss.color;
                            transformBox.style.fontSize = ss.fontSize;
                            transformBox.style.zIndex = (ds.zIndex === "0" || ds.zIndex === "auto") ? 1 : ds.zIndex;
                            transformBox.style.overflow = "hidden";
                            EW.body.appendChild(transformBox);
                            conf.to.style.visibility = "hidden";
                            if (conf.flow)
                               conf.from.style.visibility = "hidden";

                            TweenLite.fromTo(transformBox, t,
                                    {
                                       width: sourceRect.width,
                                       height: sourceRect.height,
                                       left: sourceRect.left,
                                       top: sourceRect.top,
                                       //opacity: 1
                                    },
                                    {
                                       width: distRect.width,
                                       height: distRect.height,
                                       left: distRect.left,
                                       top: distRect.top,
                                       backgroundColor: (ds.backgroundColor.indexOf("rgba") !== -1 ||
                                               ds.backgroundColor === "transparent") ? "rgb(190,190,190)" : ds.backgroundColor,
                                       boxShadow: ds.boxShadow,
                                       borderRadius: ds.borderRadius,
                                       ease: conf.ease || "Power2.easeInOut",
                                       onComplete: function ()
                                       {
                                          conf.to.style.visibility = "";
                                          transformBox.parentNode.removeChild(transformBox);
                                          if (conf.onComplete)
                                             conf.onComplete();
                                       }
                                    });
                         },
                         scaleTransform: function (conf)
                         {
                            var t = conf.time || EW.DEFAULTS.animationDuration;
                            var sourceRect = conf.from.getBoundingClientRect();
                            var distRect = conf.to.getBoundingClientRect();
                            //toE.style.opacity = '0';
                            var distBox = document.createElement("div");
                            var ss = window.getComputedStyle(conf.from, null);
                            //console.log(ss);
                            var ds = window.getComputedStyle(conf.to, null);
                            //distBox.style.cssText = document.defaultView.getComputedStyle(conf.to, "").cssText;
                            distBox.style.position = "absolute";
                            distBox.style.backgroundColor = (ds.backgroundColor.indexOf("rgba") !== -1 ||
                                    ds.backgroundColor === "transparent") ? "rgb(255,255,255)" : ds.backgroundColor;
                            distBox.style.boxShadow = ds.boxShadow;
                            distBox.style.borderRadius = conf.to.style.borderRadius;
                            distBox.style.padding = ds.padding;
                            distBox.style.color = ds.color;
                            distBox.style.fontSize = ds.fontSize;
                            distBox.style.fontWeight = ds.fontWeight;
                            distBox.style.textAlign = ds.textAlign;
                            distBox.style.textTransform = ds.textTransform;
                            distBox.style.zIndex = (ds.zIndex === "0" || ds.zIndex === "auto") ? 1 : ds.zIndex;
                            distBox.style.width = distRect.width + "px";
                            distBox.style.height = distRect.height + "px";
                            distBox.style.lineHeight = ds.lineHeight;
                            distBox.style.border = ds.border;
                            distBox.style.borderRadius = ds.borderRadius;
                            distBox.style.margin = "0px";
                            distBox.innerHTML = conf.to.innerHTML;
                            distBox.className = conf.to.className;
                            EW.body.appendChild(distBox);

                            var originBox = document.createElement("div");
                            originBox.style.position = "absolute";
                            originBox.style.backgroundColor = (ss.backgroundColor.indexOf("rgba") !== -1 ||
                                    ss.backgroundColor === "transparent") ? "rgb(255,255,255)" : ss.backgroundColor;
                            originBox.style.boxShadow = 'none';
                            //origin.style.borderRadius = conf.from.style.borderRadius;
                            originBox.style.padding = ss.padding;
                            originBox.style.color = ss.color;
                            originBox.style.fontSize = ss.fontSize;
                            originBox.style.fontWeight = ss.fontWeight;
                            originBox.style.textAlign = ss.textAlign;
                            originBox.style.textDecoration = ss.textDecoration;
                            originBox.style.zIndex = (ds.zIndex === "0" || ds.zIndex === "auto") ? 1 : ds.zIndex;
                            originBox.style.margin = "0px";
                            originBox.style.width = sourceRect.width + "px";
                            originBox.style.height = sourceRect.height + "px";
                            originBox.style.lineHeight = ss.lineHeight;
                            originBox.style.border = ss.border;
                            originBox.innerHTML = conf.from.innerHTML;
                            originBox.className = conf.from.className;
                            EW.body.appendChild(originBox);
                            var ease = conf.ease || "Power2.easeInOut";

                            conf.to.style.visibility = "hidden";
                            if (conf.flow)
                               conf.from.style.visibility = "hidden";

                            TweenLite.fromTo(originBox, t,
                                    {
                                       //boxShadow: 'none',
                                       left: sourceRect.left,
                                       top: sourceRect.top,
                                       //transform: "scale(1,1)",
                                       transformOrigin: "0 0"
                                    },
                            {
                               left: distRect.left,
                               top: distRect.top,
                               borderRadius: ds.borderRadius,
                               opacity: 0,
                               //boxShadow: ss.boxShadow,
                               //margin:0,
                               transform: "scale(" + distRect.width / sourceRect.width + "," + distRect.height / sourceRect.height + ")",
                               ease: ease,
                               onComplete: function ()
                               {
                                  originBox.parentNode.removeChild(originBox);
                               }
                            });


                            TweenLite.fromTo(distBox, t,
                                    {
                                       //boxShadow:'none',
                                       left: sourceRect.left,
                                       top: sourceRect.top,
                                       margin: 0,
                                       //opacity: .5,
                                       transform: "scale(" + sourceRect.width / distRect.width + "," + sourceRect.height / distRect.height + ")",
                                       //transform: "scale(" + sourceRect.width / distRect.width + "," + sourceRect.height / distRect.height + ")",
                                       transformOrigin: "0 0"
                                    },
                            {
                               //opacity: 1,
                               left: distRect.left,
                               top: distRect.top,
                               transform: "scale(1,1)",
                               ease: ease,
                               onComplete: function ()
                               {
                                  conf.to.style.visibility = "";
                                  distBox.parentNode.removeChild(distBox);
                                  if (conf.onComplete)
                                     conf.onComplete();
                               }
                            });
                         },
                         toLoader: function (el, loaderClass)
                         {
                            var loader = EW.clone(EW.COMPONENT_STRUCTURE);

                            loader.el = document.createElement("div");
                            loader.cancel = function ()
                            {
                               this.trigger("cancel");
                               this.dispose();
                            };
                            loader.dispose = function ()
                            {
                               TweenLite.fromTo(el, .15, {opacity: 0}, {opacity: 1});
                               el.style.visibility = "";
                               this.disposed = true;
                               loader.el.parentNode.removeChild(loader.el);
                               this.trigger('dispose');
                            };


                            var elemStyle = window.getComputedStyle(el);
                            var elemRect = el.getBoundingClientRect();
                            var elemCent = EW.getCenterPoint(elemRect);
                            loader.el.className = loaderClass;
                            EW.body.appendChild(loader.el);

                            var loaderStyle = window.getComputedStyle(loader.el);
                            var loaderRect = loader.el.getBoundingClientRect();

                            loader.el.style.position = "absolute";
                            loader.el.style.width = elemRect.width + "px";
                            loader.el.style.height = elemRect.height + "px";
                            loader.el.style.top = elemRect.top + 'px';
                            loader.el.style.left = elemRect.left + 'px';
                            loader.el.style.zIndex = (elemStyle.zIndex === "0" || elemStyle.zIndex === "auto") ? 1 : elemStyle.zIndex;

                            var animProperties = (loaderClass) ?
                                    {
                                       top: elemCent.top - loaderRect.width / 2,
                                       left: elemCent.left - loaderRect.height / 2,
                                       width: loaderRect.width,
                                       height: loaderRect.height,
                                       borderRadius: loaderStyle.borderRadius,
                                       //backgroundColor: loaderStyle.backgroundColor,
                                       boxShadow: loaderStyle.boxShadow,
                                       ease: "Power3.easeOut",
                                    } :
                                    {
                                       top: elemCent.top - 30,
                                       left: elemCent.left - 30,
                                       width: 60,
                                       height: 60,
                                       borderRadius: 30,
                                       ease: "Power2.easeOut",
                                    };
                            /*animProperties.onComplete = function ()
                             {
                             
                             }*/
                            loader.el.style.visibility = "hidden";
                            setTimeout(function ()
                            {
                               loader.el.className = "";
                               loader.el.style.visibility = "";
                               loader.el.style.borderRadius = elemStyle.borderRadius;
                               loader.el.style.backgroundColor = (elemStyle.backgroundColor.indexOf("rgba") !== -1 ||
                                       elemStyle.backgroundColor === "transparent" || elemStyle.backgroundColor === "rgb(255, 255, 255)") ? elemStyle.color : elemStyle.backgroundColor;
                               el.style.visibility = "hidden";
                               TweenLite.to(loader.el, .15,
                                       {
                                          top: elemCent.top - 14,
                                          left: elemCent.left - 14,
                                          width: 28,
                                          height: 28,
                                          borderRadius: 28,
                                          ease: "Power4.easeOut",
                                          onComplete: function ()
                                          {
                                             loader.el.className = loaderClass;
                                          }
                                       });

                               animProperties.delay = .15;
                               TweenLite.to(loader.el, .3, animProperties);
                               loader.el.addEventListener("click", function ()
                               {
                                  loader.cancel();
                               });
                            }, 0);
                            return loader;
                         }
                      }

           };
   return EW;
}();