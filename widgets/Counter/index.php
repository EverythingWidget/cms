<?php
webroot\WidgetsManagement::add_html_script(["include" => "rm/public/js/countdown/jquery.countdown.js"]);
?>
<div id="{$widget_id}">
  <div class="days-wrapper">
    <span class="days"></span> <br>days
  </div>
  <div class="hours-wrapper">
    <span class="hours"></span> <br>hours
  </div>
  <div class="minutes-wrapper">
    <span class="minutes"></span> <br>minutes
  </div>
  <div class="seconds-wrapper">
    <span class="seconds"></span> <br>seconds
  </div>
</div>
<script>
  $(document).ready(function () {
    /*
     Countdown initializer
     */
    var now = new Date(<?= $widget_parameters["date"] ?>);
    var countTo = <?= $widget_parameters["days"] ?> * 24 * 60 * 60 * 1000 + now.valueOf();
    $('#{$widget_id}').countdown(countTo, function (event) {
      var $this = $(this);
      switch (event.type) {
        case "seconds":
        case "minutes":
        case "hours":
        case "days":
        case "weeks":
        case "daysLeft":
          $this.find('span.' + event.type).html(event.value);
          break;
        case "finished":
          $this.hide();
          break;
      }
    });
  });
</script>