<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<form id="json-editor-form"   action="#" method="POST">
   <div class="header-pane row">
      <h1 id="form-title" class="col-xs-12">
         <span>tr{Edit}</span>tr{Language}
      </h1>  
   </div>
   <div class="form-content  row">
      <input type="hidden" id="app" name="app" value="<?php echo $_REQUEST["app"] ?>"/>
      <input type="hidden" id="lang" name="language" value="<?php echo $_REQUEST["lang"] ?>"/>
      <div class="col-xs-12">
         <div class="row">
            <div class="col-xs-12">
               <ul id="menu" class="list box">
                  <li>
                     <div class="wrapper">
                        <div class="col-xs-6">
                           <input class="text-field" data-label="id" name="id" />
                        </div>
                        <div class="col-xs-6">
                           <input class="text-field" data-label="text" name="text" />
                        </div>
                     </div>
                  </li>
               </ul>

            </div>
         </div>      
      </div>
   </div>
   <div class="footer-pane row actions-bar action-bar-items" >
   </div>
</form>
<script>
   var JSONEditorForm = (function ()
   {
      function JSONEditorForm()
      {
         this.bSave = EW.addAction("tr{Save Changes}", $.proxy(this.update, this)).addClass("btn-success");
         $("#menu").EW().dynamicList({
            value: <?php echo admin\Settings::get_language_strings($_REQUEST["app"], $_REQUEST["lang"]); ?>
         });
      }

      JSONEditorForm.prototype.addCity = function ()
      {
         /*if ($("#name").val())
          {
          //alert(media.itemId);
          var formParams = $.parseJSON($("#city-form").serializeJSON());
          EW.lock($("#city-form"), "Saving...");
          $.post('<?php echo EW_ROOT_URL; ?>app-culturenight/Cities/add_city', formParams, function(data) {
          if (data.status === "success")
          {
          $.EW("getParentDialog", $("#city-form")).trigger("close");
          $(document).trigger("cities-list.refresh");
          $("body").EW().notify(data);
          }
          else
          {
          $("body").EW().notify(data);
          }
          EW.unlock($("#city-form"));
          }, "json");
          }*/
      };

      JSONEditorForm.prototype.update = function ()
      {

         //alert(media.itemId);
         var formParams = $.parseJSON($("#json-editor-form").serializeJSON());
         EW.lock($("#city-form"), "Saving...");
         $.post('<?php echo EW_ROOT_URL; ?>app-admin/Settings/update_language', formParams, function (data) {

            $("body").EW().notify(data);

         }, "json");

      };

      return new JSONEditorForm();
   })();

</script>
