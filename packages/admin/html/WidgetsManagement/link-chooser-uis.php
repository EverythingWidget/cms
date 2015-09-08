<?php
session_start();
?>

<script  type="text/javascript">
   var LinkChooserUIS = (function ()
   {
      function LinkChooserUIS()
      {
         var self = this;
         this.currentTopPane;
         this.oldRow;
         /*this.bNewUIS = EW.addAction("tr{New Layout}", function ()
          {
          EW.setHashParameter('cmd', "new-uis");
          }, {display: "none"});
          if (EW.getActivity({activity: "admin-api/WidgetsManagement/import_uis"}))
          {
          var fi = $("<input type=file id=uis_file name=uis_file accept='.json'/>");
          $(".action-bar-items").append($("<button type=button class='btn btn-file btn-primary' >tr{Import Layout}</button>").append(fi));
          
          fi.change(function (e) {
          var form = new FormData();
          // HTML file input user's choice...
          form.append("uis_file", fi[0].files[0]);
          //EW.lock($("#main-content"));
          if (!fi[0].files[0])
          return;
          
          // Make the ajax call
          $.ajax({
          url: '<?php echo EW_ROOT_URL ?>admin-api/WidgetsManagement/import_uis',
          type: 'POST',
          dataType: "json",
          //xhr: function () {
          //var myXhr = $.ajaxSettings.xhr();
          //if (myXhr.upload) {
          //myXhr.upload.addEventListener('progress', uploadForm.progress, false);
          //}
          //return myXhr;
          //},
          //add beforesend handler to validate or something
          //beforeSend: functionname,
          success: function (res) {
          //EW.unlock($("#main-content"));
          $("body").EW().notify(res).show();
          self.table.refresh();
          },
          //add error handler for when a error occurs if you want!
          //error: errorfunction,
          data: form,
          // this is the important stuf you need to overide the usual post behavior
          cache: false,
          contentType: false,
          processData: false
          });
          });
          }
          $(document).off("uis-list.refresh");
          $(document).on("uis-list.refresh", function () {
          self.table.refresh();
          });
          this.bNewUIS.comeIn(300);*/
         this.uis = {};
         this.table = EW.createTable({name: "uis-list", rowLabel: "{name}", columns: ["name", "template"], headers: {Name: {}, Template: {}}, rowCount: true, url: "<?php echo EW_ROOT_URL; ?>admin-api/WidgetsManagement/get_uis_list", pageSize: 30
            , buttons: {"Select": function (row)
               {
                  self.uis = {type: "uis", id: row.data("field-id")};
                  self.selectUIS();
               }}});
         $("#link-chooser #link-chooser-uis-list").html(this.table.container);
      }
      LinkChooserUIS.prototype.selectUIS = function ()
      {

<?php
//Call the function which has been attached to the function reference element
if ($_REQUEST["callback"] == "function-reference")
{
   echo 'var func = $("#link-chooser #function-reference").data("callback")(JSON.stringify(this.uis));';
}
else
   echo $_REQUEST["callback"] . '(this.uis);';
?>
      }
      return new LinkChooserUIS();
   })();

</script>




