<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div class="row">
   <div class="col-xs-12">
      <h2>
         tr{Shortcut}
      </h2>
   </div>
</div>
<div class="row">
   <div class="col-xs-12">
      <?php
      $activities = EWCore::read_activities();
      foreach ($activities as $act => $par)
      {
         if (!$par["form"])
            continue;
         $href = EW_ROOT_URL . "admin/api/index.php?compId={$par["compId"]}#";
         foreach ($par as $key => $val)
         {
            if ($key == "compId")
               continue;
            $href.="$key=$val&";
         }
         if (strpos($act, '.php_'))
            continue;
         //echo "<button type=button class='btn btn-primary link' data-link='{$par["app"]}/{$par["section"]}/{$par["url"]}' >{$par["appTitle"]}: {$par["title"]}</button>"
         echo "<button type=button class='btn btn-primary link' data-link='$act' >{$par["appTitle"]}: {$par["activityTitle"]}</button>"
         ?>

         <?php
      }
      ?>
   </div>
</div>

<script  type="text/javascript">
   //console.log(<?php echo json_encode(EWCore::read_registry("ew-activity")); ?>);
   var appContent = $("#main-content").html();
   //$("#main-content").empty();
   function Dashboard()
   {
      var self = this;
      this.oldApp = null;
      //EW.setHashParameter("app", "webroot");
      $.each($("#main-content").find("button.link"), function (i, e) {
         $(e).on("click", function () {
            self.loadActivity($(e).data("link"));
         });
      });
   }

   Dashboard.prototype.loadActivity = function (link)
   {
      /*$.EW("createModal", {onOpen: function () {
       EW.lock(this);
       var d = this;
       var eventId = EW.getHashParameter("eventId");
       var data = {
       eventId: eventId,
       ew_actionBase: {form: "event"}
       };
       
       $.post("<?php echo EW_ROOT_URL; ?>app-" + link, function (data) {
       d.html(data);
       });
       }});*/
      EW.setHashParameter("ew_activity", link);
   };
   new Dashboard();

</script>