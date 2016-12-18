
<script  type="text/javascript">
   var LinkChooserUIS = (function () {
      function LinkChooserUIS() {
         var self = this;
         this.currentTopPane;
         this.oldRow;
         this.uis = {};
         this.table = EW.createTable({name: "uis-list", rowLabel: "{name}",
            columns: ["name", "template"],
            headers: {Name: {}, Template: {}},
            rowCount: true, url: 'api/webroot/widgets-management/get-uis-list',
            pageSize: 30,
            buttons: {
               "Select": function (row) {
                  self.uis = {type: "webroot/widgets-management/uis", id: row.data("field-id")};
                  self.selectUIS();
               }
            }
         });
         Scope.html.find('#uis-chooser').html(this.table.container);
         this.table.read();
      }

      LinkChooserUIS.prototype.selectUIS = function () {
<?php
//Call the function which has been attached to the function reference element
if ($_REQUEST["callback"] == "function-reference")
{
   echo 'var func = $("#link-chooser #function-reference").data("callback")(JSON.stringify(this.uis));';
}
else
{
   echo $_REQUEST["callback"] . '(this.uis);';
}
?>
      }
      return new LinkChooserUIS();
   })();
</script>

