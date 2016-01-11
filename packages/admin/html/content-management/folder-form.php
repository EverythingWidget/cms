<?php

function get_folder_data($id)
{
   $categoryInfo = ["data" => []];
   $categoryInfo["data"]["parent_id"] = $_REQUEST["parent"];
   if ($id)
   {
      return EWCore::call("admin/api/content-management/contents", ["id" => $id]);
   }
   else
   {
      return json_encode($categoryInfo);
   }
}

function inputs()
{
   ob_start();
   ?>
   <input type="hidden" id="parent_id" name="parent_id" value="">
   <?php

   return ob_get_clean();
}

function script()
{
   ob_start();
   ?>
   <script >
      var bSave = EW.addActivity({title: "tr{Save}", defaultClass: "btn-success", activity: "admin/api/content-management/add-folder",
         postData: function () {
            if (!$("#category-form").EW().validate()) {
               return false;
            }
            var data = ContentForm.getFormData();
            return data;
         },
         onDone: function (data) {
            $("body").EW().notify(data).show();
            $(document).trigger("article-list.refresh");
            $.EW("getParentDialog", $("#category-form")).trigger("close");
         }}).hide();

      var bEdit = EW.addActivity({title: "tr{Save Changes}", defaultClass: "btn-success", activity: "admin/api/content-management/update-folder",
         postData: function ()
         {
            if (!$("#category-form").EW().validate())
            {
               return false;
            }
            var data = ContentForm.getFormData();
            return data;
         },
         onDone: function (data)
         {
            $("body").EW().notify(data).show();
            $(document).trigger("article-list.refresh");
         }}).hide();

      var bDelete = EW.addActivity({title: "tr{Delete}", defaultClass: "btn-danger", activity: "admin/api/content-management/delete-folder",
         postData: function ()
         {
            if (!confirm("tr{Are you sure of deleting this folder?}")) {
               return false;
            }
            var data = ContentForm.getFormData();
            return {id: data.id};
         },
         onDone: function (response)
         {
            $("body").EW().notify(response.data).show();
            $(document).trigger("article-list.refresh");
            $.EW("getParentDialog", $("#category-form")).trigger("close");
         }}).hide();

      $("#category-form").on("refresh", function (e, folder) {
         if (folder.data && folder.data.id)
         {
            bSave.comeOut(300);
            bEdit.comeIn(300);
            bDelete.comeIn(300);
            $("#category-form #form-title").html("<span>tr{Edit}</span>" + folder.data.title);
         } else {
            bSave.comeIn(300);
            bEdit.comeOut(300);
            bDelete.comeOut(300);
         }
      });
   </script>
   <?php

   return ob_get_clean();
}

EWCore::register_form("ew-content-form-proerties", "category-properties", ["content" => inputs()]);
echo admin\ContentManagement::create_content_form(["formTitle" => "Folder",
    "formId" => "category-form",
    "contentType" => "folder",
    "script" => script(),
    "data" => get_folder_data($_REQUEST["folderId"])]);

