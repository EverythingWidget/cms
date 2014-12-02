<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
admin\WidgetsManagement::set_widget_style_class("balloon balloon-poster");
?>
<form name="balloon-poster-form" id="balloon-poster-form">
   <div id="balloon-poster-modal">
      <div class="content">
         <input class="text-box" id="balloon_poster" name="balloon_poster"/>
      </div>
      <div class="options">

      </div>
      <div class="people">

      </div>
      <div class="footer">
         <button class="btn btn-text" type="button" id="balloon-poster-cancel">Cancel</button>
      </div>
   </div>
</form>

<script type="text/javascript">

   var balloonPosterModal = $("#balloon-poster-modal");
   // Calculate balloon poster left relative to the window width
   balloonPosterModal.css({top: $("#base-content-pane").height() - balloonPosterModal.height(), left: ($("#base-content-pane").outerWidth() - balloonPosterModal.outerWidth()) / 2});
   // Attach balloon poster to the base pane
   $("#base-content-pane").append($("#balloon-poster-form").detach());

   // Define cancel action
   $("#balloon-poster-cancel").on("click", function () {
      balloonPosterModal.animate({className: "", top: $("#base-content-pane").outerHeight() - 100, left: ($("#base-content-pane").outerWidth() - 400) / 2}, {duration: 500, queue: false, easing: "Power2.easeOut"});
      balloonPosterModal.animate({left: ($("#base-content-pane").outerWidth() - 400) / 2}, {duration: 500, queue: false, easing: "Power2.easeOut"});
      //console.log(balloonPosterModal.height());
   });

   // Define active action
   balloonPosterModal.find("#balloon_poster").on("click", function () {
      balloonPosterModal.animate({className: "active"}, {duration: 500, queue: false, easing: "Power4.easeOut"});
      balloonPosterModal.animate({top: $("#base-content-pane").outerHeight() / 3, left: ($("#base-content-pane").outerWidth() - 600) / 2}, {duration: 500, queue: false, easing: "Power4.easeOut"});
   });
</script>