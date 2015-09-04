<?php

//session_start();
$albumInfo = EWCore::process_command("admin", "ContentManagement", "get_album", array("albumId" => $_REQUEST["albumId"]));

function inputs()
{
   ob_start();
   ?>

   <?php

   return ob_get_clean();
}

function scripts()
{
   ob_start();
   ?>
   <script>
      function AlbumForm()
      {
         var self = this;
         this.bAdd = EW.addActivity({title: "tr{Save}", defaultClass: "btn-success", activity: "admin-api/ContentManagement/add_album",
            postData: function ()
            {
               if (!$("#album-form").EW().validate())
               {
                  return false;
               }
               var data = ContentForm.getFormData();
               //data.content = tinymce.activeEditor.getContent();
               return data;
            },
            onDone: function (data)
            {
               $.EW("getParentDialog", $("#album-form")).trigger("close");
               $("body").EW().notify(data).show();
               $(document).trigger("media-list.refresh", [data]);
            }}).hide();
         this.bEdit = EW.addAction("tr{Save Changes}", this.editAlbum).addClass("btn-success").hide();

         $("#album-form").on('refresh', function (e, data)
         {
            if (data["id"])
            {
               $("#form-title").html("<span>tr{Edit}</span>" + data.title);
               self.bEdit.comeIn();
               self.bAdd.comeOut();
               //bDelete.comeIn(300)
            }
            else
            {
               self.bEdit.comeOut();
               self.bAdd.comeIn();
            }
         });
      }
      var AlbumForm = new AlbumForm();
      //$("#title").focus();
   </script>
   <?php

   return ob_get_clean();
}

//EWCore::register_form("ew-content-form-proerties", "article-properties", ["content" => inputs()]);
echo admin\ContentManagement::create_content_form(["formId" => "album-form", "contentType" => "album", "script" => scripts(), "data" => $albumInfo]);
