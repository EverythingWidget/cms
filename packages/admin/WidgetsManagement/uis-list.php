<script  type="text/javascript">
   function UIStructureList()
   {
      var self = this;
      this.currentTopPane;
      this.oldRow;
      this.bNewUIS = EW.addAction("tr{New Layout}", function ()
      {
         EW.setHashParameter('cmd', "new-uis");
      }, {display: "none"});
      if (EW.getActivity({activity: "app-admin/WidgetsManagement/import_uis"}))
      {
         var fi = $("<input type=file id=uis_file name=uis_file accept='.json'/>");
         $(".action-bar-items").append($("<div class='btn btn-file btn-primary' >tr{Import Layout}</div>").append(fi));

         fi.change(function (e) {
            var form = new FormData();
            // HTML file input user's choice...
            form.append("uis_file", fi[0].files[0]);
            //EW.lock($("#main-content"));
            if (!fi[0].files[0])
               return;

            // Make the ajax call
            $.ajax({
               url: '<?php echo EW_ROOT_URL ?>app-admin/WidgetsManagement/import_uis',
               type: 'POST',
               dataType: "json",
               /*xhr: function () {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                myXhr.upload.addEventListener('progress', uploadForm.progress, false);
                }
                return myXhr;
                },*/
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
      var exportAction = null;
      if (EW.getActivity({activity: "app-admin/WidgetsManagement/export_uis"}))
      {
         exportAction = function (row)
         {
            window.open("app-admin/WidgetsManagement/export_uis?uis_id=" + row.data("field-id"));
         }
      }
      $(document).off("uis-list.refresh");
      $(document).on("uis-list.refresh", function () {
         self.table.refresh();
      });
      this.bNewUIS.comeIn(300);

      this.table = EW.createTable({name: "uis-list", rowLabel: "{name}", columns: ["name", "template"], headers: {Name: {}, Template: {}}, rowCount: true, url: "<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/get_uis_list", pageSize: 30
         , onDelete: function (id)
         {

            this.confirm("Are you sure of deleting this UIS?", function () {
               $.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/delete_uis', {
                  uisId: id}, function (data) {
                  EW.setHashParameter("categoryId", null);
                  $("body").EW().notify(data).show();
                  self.table.removeRow(id);
                  return true;

               }, "json");
            });
            //uisList.deleteUIS(id);
         }
         , onEdit: function (id)
         {
            EW.setHashParameters({"uis-id": id, "cmd": "edit-uis"});
         }
         , buttons: {"tr{Clone}": function (row)
            {
               if (confirm("Are you sure you want to clone UIS:" + row.data("field-name") + "?"))
               {
                  $.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/clone_uis', {uisId: row.data("field-id")}, function (data) {
                     self.table.refresh();
                     $("body").EW().notify(data).show();
                  }, "json");
               }
            }
            , "tr{Export}": exportAction}});
      $("#main-content").html(this.table.container);
   }

   UIStructureList.prototype.selectUIS = function (obj, uisId)
   {
      var self = this;
      $(self.oldRow).removeClass("selected");
      $(obj).addClass("selected");
      self.oldRow = obj;
   };

   UIStructureList.prototype.editUIS = function ()
   {
      EW.setHashParameter('cmd', "edit-uis");
   };

   UIStructureList.prototype.loadNewUISForm = function ()
   {
      var self = this;
      var tp = EW.createModal({class: "full", onClose: function ()
         {
            EW.setHashParameter("cmd", null);
            self.currentTopPane = null;
         }});
      self.currentTopPane = tp;
      EW.lock(tp);

      $.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/ne-uis_1.php', function (data) {
         tp.html(data);
         //neuis.newUISForm();
      });
   };

   UIStructureList.prototype.loadEditUISForm = function ()
   {
      var self = this;
      // if modal is open do not proceed
      if (self.currentTopPane)
         return;
      //{
      tp = EW.createModal({class: "full", onClose: function ()
         {
            //neuis.dispose();
            EW.setHashParameter("cmd", null);
            self.currentTopPane = null;
            //uisList.bEditUIS.comeIn(300);
            //contentManagement.showActions();
         }});
      self.currentTopPane = tp;
      tp.addClass("full");
      EW.lock(tp);
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/ne-uis.php', {uisId: EW.getHashParameter("uis-id")}, function (data) {
         tp.html(data);
         //neuis.editUISForm();

      });
      //}

   };

   UIStructureList.prototype.deleteUIS = function (id)
   {
      var self = this;
      //EW.lock(uisList.currentTopPane, "Saving...");
      $.post('<?php echo EW_ROOT_URL; ?>app-admin/WidgetsManagement/delete_uis', {
         uisId: id}, function (data) {
         EW.setHashParameter("categoryId", null);
         $("body").EW().notify(data).show();
         self.table.removeRow(id);
         return true;

      }, "json");
   };

   /*widgetsManagement.onBackToWM = function ()
   {
      var self = this;
      self.bNewUIS.remove();
      EW.removeURLHandler(self.handler);
      //uisList.bEditUIS.remove();
      EW.setHashParameter('ui_structure_id', null);
   };*/

   UIStructureList.prototype.listUIStructures = function ()
   {
      if (this.table)
      {
         this.table.refresh();
         return;
      }
   };

   //showMoreOptions(false);
   // listUIStructures();
   var uisList;
   $(document).ready(function () {
      uisList = new UIStructureList();
      //uisList.listUIStructures();

      uisList.handler = EW.addURLHandler(function ()
      {
         var uisId = EW.getHashParameter("uis-id");
         var cmd = EW.getHashParameter("cmd");
         if (cmd)
         {
            if (cmd === "edit-uis")
            {
               uisList.loadEditUISForm();
            }
            if (cmd === "new-uis")
            {
               uisList.loadNewUISForm();
            }
            //uisList.bEditUIS.comeOut(200);
         }
         else
         {
            if (uisId)
            {
               //uisList.bEditUIS.comeIn(300);
               //uisList.selectUIS($("#uis-list tr[data-id=" + uisId + "]"));
            }
            else
            {
               //uisList.bEditUIS.comeOut(200);
               //uisList.selectUIS(null);
            }
            uisList.bNewUIS.comeIn(300);
            if (uisList.currentTopPane)
               uisList.currentTopPane.dispose();
            uisList.currentTopPane = null;
         }

         return "UISHandler";
      });
   });
</script>




