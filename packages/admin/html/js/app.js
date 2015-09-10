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
require(['grid-on-air'], function (goa)
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
    
    });*/
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
           });

   //goa.createGrid();
});


var EW = function ()
{
   var ew =
           {
              body: document.getElementsByTagName("body")[0],
              getCenterPoint: function (rect)
              {
                 var pos = document.activeElement.getBoundingClientRect();
                 return         {
                    left: rect.left + (rect.width / 2),
                    top: rect.top + (rect.height / 2)
                 };
              },
              createModal: function (parameters)
              {
                 var lockPane = ew.lock(document.getElementsByClassName("app-pane")[0]);

                 var modal = document.createElement("div"); //or use jQuery's $("#photo")
                 modal.classList.add("dialog", "center", "open");
                 modal.innerHTML = "<h1 class='dialog-header-bar'>This is the Dialog Title</h1><div class='dialog-content-pane'></div><div class='dialog-action-bar'></div>";
                 document.getElementsByTagName("body")[0].appendChild(modal);

                 var origin = document.activeElement;
                 EW.animation.transform(origin, modal, .5, function ()
                 {
                    origin.display = "none";
                    modal.style.display = "";
                 });
                 modal.style.display = "none";
                 origin.style.opacity = "0";
                 modal.addEventListener("click", function ()
                 {
                    lockPane.dispose();
                    EW.animation.transform(modal, origin, .3, function ()
                    {
                       origin.style.opacity = "";
                    });
                    modal.parentNode.removeChild(modal);
                 });
              },
              lock: function (e)
              {
                 var sourceRect = e.getBoundingClientRect();
                 var ss = window.getComputedStyle(e);
                 var lockPane = document.createElement("div");
                 lockPane.classList.add("lock-pane");
                 lockPane.style.position = "absolute";
                 lockPane.style.left = sourceRect.left;
                 lockPane.style.top = sourceRect.top;
                 lockPane.style.width = sourceRect.width + "px";
                 lockPane.style.height = sourceRect.height + "px";
                 lockPane.style.zIndex = (ss.zIndex === "0" || ss.zIndex === "auto") ? 1 : ss.zIndex;

                 e.parentNode.insertBefore(lockPane, e.nextSibling);
                 setTimeout(function ()
                 {
                    lockPane.classList.add("show");
                 }, 1);
                 //console.log(sourceRect)
                 /*TweenLite.fromTo(lockPane, .5,
                  {
                  opacity: 0
                  },
                  {
                  opacity: 1
                  });*/
                 lockPane.dispose = function ()
                 {
                    lockPane.parentNode.removeChild(lockPane);
                 };
                 return lockPane;
              },
              animation:
                      {
                         transform: function (fromE, toE, t, onComplete, ease)
                         {
                            t = t || 1;
                            var sourceRect = fromE.getBoundingClientRect();
                            var distRect = toE.getBoundingClientRect();
                            //toE.style.opacity = '0';
                            var transformBox = document.createElement("div");
                            var ss = window.getComputedStyle(fromE);
                            var ds = window.getComputedStyle(toE);
                            transformBox.style.position = "absolute";
                            transformBox.style.backgroundColor = ss.backgroundColor;
                            transformBox.style.boxShadow = ds.boxShadow;
                            transformBox.style.borderRadius = ds.borderRadius;
                            transformBox.style.zIndex = (ds.zIndex === "0" || ds.zIndex === "auto") ? 1 : ds.zIndex;
                            ew.body.appendChild(transformBox);
                            //console.log(ds.zIndex);
                            //var c = ew.getCenterPoint(sourceRect);
                            //console.log(sourceRect);
                            TweenLite.fromTo(transformBox, t,
                                    {
                                       width: distRect.width,
                                       height: distRect.height,
                                       left: sourceRect.left,
                                       top: sourceRect.top,
                                       /*width: sourceRect.width,
                                        height: sourceRect.height*/
                                       //transform: "scale(.5,1)",
                                       transform: "scale(" + sourceRect.width / distRect.width + "," + sourceRect.height / distRect.height + ")",
                                       transformOrigin: "0 0"
                                    },
                            {
                               left: distRect.left,
                               top: distRect.top,
                               //width: distRect.width,
                               //height: distRect.height,
                               //boxShadow: ds.boxShadow,
                               backgroundColor: ds.backgroundColor,
                               transform: "scale(1,1)",
                               //borderRadius: ds.borderRadius,
                               ease: ease || "Power2.easeInOut",
                               onComplete: function ()
                               {
                                  //toE.style.opacity = '';
                                  transformBox.parentNode.removeChild(transformBox);
                                  if (onComplete)
                                     onComplete();
                               }
                            });
                         }
                      }

           }
   return ew;
}();