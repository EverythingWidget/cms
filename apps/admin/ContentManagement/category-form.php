<?php

session_start();
$categoryInfo = array();
if ($_REQUEST["categoryId"])
{
   $categoryInfo = EWCore::process_command("admin", "ContentManagement", "get_category", array("id" => $_REQUEST["categoryId"]));
}
else
{
   $categoryInfo["parent_id"] = $_REQUEST["parent"];
   $categoryInfo = json_encode($categoryInfo);
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
      var bSave = EW.addActivity({title: "tr{Save}", defaultClass: "btn-success", activity: "app-admin/ContentManagement/add_category",
         postData: function ()
         {
            if (!$("#category-form").EW().validate())
            {
               return false;
            }
            var data = ContentForm.getFormData();
            data.content = tinymce.activeEditor.getContent();
            return data;
         },
         onDone: function (data)
         {
            $("body").EW().notify(data).show();
            $(document).trigger("article-list.refresh");
            $.EW("getParentDialog", $("#category-form")).trigger("close");
         }}).hide();

      var bEdit = EW.addActivity({title: "tr{Save Changes}", defaultClass: "btn-success", activity: "app-admin/ContentManagement/update_category",
         postData: function ()
         {
            if (!$("#category-form").EW().validate())
            {
               return false;
            }
            var data = ContentForm.getFormData();
            data.content = tinymce.activeEditor.getContent();
            return data;
         },
         onDone: function (data)
         {
            $("body").EW().notify(data).show();
            $(document).trigger("article-list.refresh");
         }}).hide();

      var bDelete = EW.addActivity({title: "tr{Delete}", defaultClass: "btn-danger", activity: "app-admin/ContentManagement/delete_category",
         postData: function ()
         {
            if (!confirm("tr{Are you sure of deleting this folder?}"))
            {
               return false;
            }
            var data = ContentForm.getFormData();
            data.content = tinymce.activeEditor.getContent();
            return data;
         },
         onDone: function (data)
         {
            $("body").EW().notify(data).show();
            $(document).trigger("article-list.refresh");
            $.EW("getParentDialog", $("#category-form")).trigger("close");
         }}).hide();

      $("#category-form").on("refresh", function (e, data) {
         //console.log(data.id);
         if ($("#category-form #id").val())
         {
            bSave.comeOut(300);
            bEdit.comeIn(300);
            bDelete.comeIn(300);
            $("#category-form #form-title").html("<span>tr{Edit}</span>" + data.title);
         }
         else
         {
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
echo admin\ContentManagement::create_content_form(["formTitle" => "Folder", "formId" => "category-form", "contentType" => "folder", "script" => script(), "data" => $categoryInfo]);

