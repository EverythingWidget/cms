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
   
   goa.addRange(
           {
              prefix: 'col-xs-',
              min: 0,
              gutter: '5px',
              columns: 3

           });

   goa.addRange(
           {
              prefix: 'col-sm-',
              min: 600,
              gutter: '10px',
              columns: 12

           });
   goa.addRange(
           {
              prefix: 'col-md-',
              min: 960,
              gutter: '15px',
              columns: 12

           });
           
           goa.addRange(
           {
              prefix: 'col-lg-',
              min: 1340,
              gutter: '15px',
              columns: 12

           });
});