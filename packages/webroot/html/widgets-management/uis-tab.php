<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="row">
   <div class="col-xs-12">
      <label>
         Page UIS
      </label>
      <button type="button" class="button mar-right" onclick="uisTab.uisListDialog(uisTab.setPageUIS)">
         Change
      </button>
      <input type="hidden" class="text-field" name="WidgetManagement_pageUisId" id="WidgetManagement_pageUisId" value="">
      <label id='WidgetManagement_name' class="mar-bot">
         Inherit/Default
      </label>
      <button type="button" id="remove-uis-btn" class="button orange mar-right" onclick="uisTab.removeUIS()">
         Remove UIS
      </button>
   </div>
</div>
<script  type="text/javascript">

   function UisTab()
   {


   }

   UisTab.prototype.uisListDialog = function (onSelect)
   {
      var dp = EW.createModal();
      this.table = EW.createTable({name: "uis-list", headers: {Name: {}, Template: {}}, columns: ["name", "template"], rowCount: true, url: "<?php echo EW_ROOT_URL; ?>webroot-api/WidgetsManagement/get_uis_list", pageSize: 30
         , buttons: {"Select": function (row) {
               if (onSelect)
                  onSelect.apply(null, new Array(row));
               dp.dispose();
            }}});
      dp.append("<div class='header-pane row'><h1 id='' class='col-xs-12'> UIS List: Select UIS</h1></div>");
      dp.append($("<div id='' class='form-content no-footer' ></div>").append(this.table.container));

      //$.post('/admin/WidgetsManagement/get_uis_list',function(data));
   };

   UisTab.prototype.setPageUIS = function (uisId)
   {
      $("#WidgetManagement_pageUisId").val(uisId.data("field-id"));
      $("#WidgetManagement_name").text(uisId.data("field-name"));
      if ($("#WidgetManagement_pageUisId").val())
      {
         $("#remove-uis-btn").show();
      }
      else
      {
         $("#remove-uis-btn").hide();
      }
   };

   UisTab.prototype.removeUIS = function ()
   {
      $("#WidgetManagement_pageUisId").val("");
      $("#WidgetManagement_name").text("Inherit/Default");
      $("#remove-uis-btn").hide();
   };

   var uisTab = new UisTab();

   $("#<?php echo $form_id ?>").on("refresh", function (e, formData)
   {
      if ($("#WidgetManagement_pageUisId").val())
      {
         $("#remove-uis-btn").show();
      }
      else
      {
         $("#remove-uis-btn").hide();
      }
   });
</script>