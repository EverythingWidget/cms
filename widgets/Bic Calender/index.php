<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
global $EW;

$feeder_id = "events-list";

$token = $_REQUEST["$feeder_id-token"];
$date = $_REQUEST["currentDate"];
$url = json_decode($widget_parameters["url"], TRUE);

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

<script src="<?php echo (EW_ROOT_URL . "widgets/Bic Calender/bic_calender.js") ?>">
</script>
<script>
   $(document).ready(function () {
      var events = <?php echo (!$url) ? json_encode($events["items"]) : "[]"; ?>;
      var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
      var dayNames = ["Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"];

      $("[data-widget-id='<?php echo $widget_id ?>']").bic_calendar({
         //list of events in array
         events: events,
         currentDate: "<?php echo $date ?>",
         //enable select
         enableSelect: true,
         //enable multi-select
         multiSelect: true,
         //set day names
         dayNames: dayNames,
         //set month names
         monthNames: monthNames,
         //show dayNames
         showDays: true

<?php
echo ($url) ? ",reqAjax: {url:'" . EW_DIR . "{$url["url"]}' , type: 'post'}" : "";
echo ($widget_parameters["monthControl"]) ? ",displayMonthController: true" : ",displayMonthController: false";
?>

      });

   });
   //alert("asdasd");
   console.log($(document));
   console.log($("[data-widget-id='<?php echo $widget_id ?>']"));
</script>