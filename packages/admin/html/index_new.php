<!DOCTYPE html>
<html>
   <head>
      <meta charset="UTF-8">
      <title></title>
      <link rel="stylesheet" href="js/xtag/x-tag-components.min.css" />
      <link rel="stylesheet" href="css/grid.css" />
      <link rel="stylesheet" href="css/app.css" />
      <script src="js/xtag/x-tag-components.min.js"></script>
      <script src="js/xtag/x-tag-components.min.js"></script>
      <script src="http://cdnjs.cloudflare.com/ajax/libs/gsap/latest/plugins/CSSPlugin.min.js"></script>
      <script src="http://cdnjs.cloudflare.com/ajax/libs/gsap/latest/easing/EasePack.min.js"></script>
      <script src="http://cdnjs.cloudflare.com/ajax/libs/gsap/latest/TweenLite.min.js"></script>
      <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
      <script data-main="js/app.js" src="js/require.js"></script>

   </head>
   <body>



      <div class="app-pane container">

         <div class="system-nav-bar row" onclick="EW.createModal(this)"></div>

         <div  class="app-nav-bar extend row">
            <button> New </button>
            <button>Edit</button>
            <button onclick="EW.createModal()">Link to somewhere</button>
         </div>
         <div class="app-content row">

            <div class="col xs-3 sm-4 md-3 lg-3">
               <div class="card" >
                  <h1 class="card-title" onclick="EW.createModal(this.parentNode)" >
                     Card Title
                     <p>Card Subtitle</p>
                  </h1>
                  <p class="card-text" >
                     Lorem ipsum nibh eleifend augue tincidunt donec viverra vitae urna est, malesuada tortor suspendisse etiam mollis lorem feugiat enim lacus habitasse, consectetur id ultrices est nostra pretium pellentesque vitae volutpat.
                  </p>
                  <div class="action-row">
                     <button class="primary" onclick="EW.createModal()">Call Me</button>
                  </div>
               </div>
            </div>
            <div class="col xs-3 sm-4 md-4 lg-3">
               <div class="card">
                  <h1 class="card-header">
                     Card Header
                     <p>Card Subhead</p>
                  </h1>
                  <div class="action-bar">
                     <button class="text success" onclick="EW.createModal()">Ok</button>
                     <button class="text" onclick="EW.createModal()">Cancel</button>
                  </div>
                  <div class="action-bar">
                     <button class="danger" onclick="EW.createModal()">Delete</button>
                  </div>
                  <p class="card-text">
                     Lorem ipsum nibh eleifend augue tincidunt donec 
                     Lorem ipsum nibh eleifend augue tincidunt donec 
                  </p>
                  <div class="action-bar">
                     <button class="success" onclick="EW.createModal(this.parentNode)">Confirm</button>
                  </div>
               </div>
            </div>
            <div class="col xs-3 sm-4 md-5 lg-6">
               <div class="card">
                  <h1 class="card-title" onclick="EW.createModal(this)">
                     EW Administration Statistic
                     <p>Update yesterday</p>
                  </h1>
                  <p class="card-text" onclick="EW.createModal(this)">
                     Lorem ipsum nibh eleifend augue tincidunt donec viverra vitae urna est, malesuada tortor suspendisse etiam mollis lorem feugiat enim lacus habitasse, consectetur id ultrices est nostra pretium pellentesque vitae volutpat.
                  </p>
                  <div class="action-bar">
                     <button onclick="EW.createModal()">refresh</button>
                  </div>
               </div>
            </div>

         </div>
      </div>
   </body>




</html>
