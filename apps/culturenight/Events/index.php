<?php
session_start();
?>
<script  type="text/javascript">
   function Events()
   {
      var self = this;
      this.bAddActivity = EW.addActivity({title: "tr:culturenight{Add Event}", activity: "app-culturenight/Events/event-form.php", parent: "action-bar-items", modal: {class: "center"}, hash: {eventId: null}}).hide().comeIn(300);
      //this.editActivity = EW.getActivity({activity: "app-culturenight/Events/event-form.php_see"});
      this.table = EW.createTable({
         name: "events-list",
         columns: ["name", "web", "sdate", "edate", "published"],
         headers: {
            Name: {
            },
            Web: {
            },
            "Start Date": {
               width: "120px"
            },
            "End Date": {
               width: "120px"
            },
            Published: {
            }
         },
         rowCount: true,
         url: "<?php echo EW_ROOT_URL; ?>app-culturenight/Events/get_events_list",
         pageSize: 30,
         onEdit: ((editActivity = EW.getActivity({activity: "app-culturenight/Events/event-form.php_see", onDone: function (hash) {
               hash["eventId"] = null;
            }})) ? function (id) {
            editActivity({eventId: id});
         } : null)
      });
      /*$(document).off("culturenight/Events/event-form.php_see");
       $(document).on("culturenight/Events/event-form.php_see.call", function (e, data) {
       data.closeHash = {eventId: null};
       });
       
       $(document).on("culturenight/Events/event-form.php_see.done", function (e, data) {
       });*/
      $("#Events").html(this.table.container);
      $(document).off("events-list.refresh");
      $(document).on("events-list.refresh", function () {
         self.table.refresh();
      });
      var self = this;

      self.eventFromDialog = EW.createModal({hash: {key: "form", value: "event"}, onOpen: function () {
            EW.lock(this);
            var eventId = EW.getHashParameter("eventId");
            var data = {
               eventId: eventId,
               ew_actionBase: {form: "event"}
            };

            $.post("<?php echo EW_ROOT_URL; ?>app-culturenight/Events/event-form.php", data, function (data) {
               self.eventFromDialog.html(data);
            });
         },
         onClose: function () {
            EW.setHashParameters({form: null, eventId: null});
         }});
   }
   var events = new Events();

</script>