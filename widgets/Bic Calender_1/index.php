<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
global $EW;

$feeder_id = "events-list";

$token = $_REQUEST["$feeder_id-token"];
;
if (!$token)
  $token = 0;

if ($feeder_id)
{
  $events = EWCore::get_widget_feeder("calender-events", $feeder_id, array(0, 999999));
  $events = json_decode($events, TRUE);
  //echo ($events["items"]);
}
//$items_list = $items["data"];
?>

<script src="<?php echo EW_ROOT_URL ?>/widgets/Bic Calender/bic_calender.js">
</script>
<script>
    $(document).ready(function() {
    var events = <?php echo json_encode($events["items"]) ?>;
    var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var dayNames = ["S", "M", "T", "W", "T", "F", "S"];

      $("[data-widget-id='<?php echo $widget_id ?>']").bic_calendar({
      //list of events in array
      events: events,
      //enable select
      enableSelect: true,
      //enable multi-select
      multiSelect: true,
      //set day names
      dayNames: dayNames,
      //set month names
      monthNames: monthNames,
      //show dayNames
      showDays: true,
      //show month controller
      displayMonthController: true,
      //show year controller
    displayYearController: true
  });
  });

</script>