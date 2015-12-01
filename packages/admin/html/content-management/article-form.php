<?php

session_start();
$articleInfo = array();
$articleInfo["parent_id"] = $_REQUEST["parent"];
if ($_REQUEST["articleId"])
   $articleInfo = EWCore::process_request_command("admin", "api", "content-management", "get-article", [
               "articleId" => $_REQUEST["articleId"]]);
else
   $articleInfo = json_encode($articleInfo);

function inputs()
{
   ob_start()
   ?>
   <input type="hidden" id="parent_id" name="parent_id" value="">
   <?php

   return ob_get_clean();
}

function script()
{
   ob_start()
   ?>
   <script>
      function Article()
      {
         var self = this;
         this.bAdd = EW.addActivity({title: "tr{Save}", defaultClass: "btn-success", activity: "admin/api/content-management/add-article",
            postData: function ()
            {
               if (!$("#article-form").EW().validate())
               {
                  //return false;
               }
               var data = ContentForm.getFormData();
               //data.content = tinymce.activeEditor.getContent();
               return data;
            },
            onDone: function (data)
            {

               $.EW("getParentDialog", $("#article-form")).trigger("close");
               $("body").EW().notify(data).show();
               $(document).trigger("article-list.refresh", [data]);
            }}).hide();

         this.bEditAndClose = EW.addActivity({title: "tr{Save and Close}",
            defaultClass: "btn-success pull-right",
            activity: "admin/api/content-management/update-article",
            postData: function ()
            {
               if (!$("#article-form").EW().validate())
               {
                  return false;
               }
               var data = ContentForm.getFormData();
               return data;
            },
            onDone: function (data)
            {
               $("body").EW().notify(data).show();
               ContentForm.setData(data.data);
               $.EW("getParentDialog", $("#article-form")).trigger("close");

               $(document).trigger("article-list.refresh");
            }}).hide();

         this.bEdit = EW.addActivity({title: "tr{Save}", defaultClass: "btn-success", activity: "admin/api/content-management/update-article",
            postData: function ()
            {
               if (!$("#article-form").EW().validate())
               {
                  return false;
               }

               var data = ContentForm.getFormData();
               //data.content = tinymce.activeEditor.getContent();
               //ContentForm.getFormData();
               return data;
            },
            onDone: function (data)
            {
               $("body").EW().notify(data).show();
               ContentForm.setData(data.data);
               $(document).trigger("article-list.refresh");
            }}).hide();

         this.bDelete = EW.addActivity({title: "tr{Delete}", defaultClass: "btn-danger", activity: "admin/api/content-management/delete-article",
            postData: function ()
            {
               if (confirm("tr{Delete this article?}"))
               {
                  return {articleId: $("#id").val()};
               } else
                  return null;

            },
            onDone: function (data)
            {
               $.EW("getParentDialog", $("#article-form")).trigger("destroy");
               EW.setHashParameter("articleId", null);
               $("body").EW().notify(data).show();
               $(document).trigger("article-list.refresh");
            }}).hide();

         $("#article-form").on("refresh", function (e, data) {
            //console.log(data.id);

            if ($("#article-form #id").val())
            {
               self.bAdd.comeOut(300);
               self.bEditAndClose.comeIn(300);
               self.bEdit.comeIn(300);
               self.bDelete.comeIn(300);

               $("#form-title").html("<span>tr{Edit}</span>" + data.title);
               $("#date_created").val(data.round_date_created);

            } else
            {
               self.bAdd.comeIn(300);
               self.bEditAndClose.comeOut(300);
               self.bEdit.comeOut(300);
               self.bDelete.comeOut(300);

            }
         });
      }

      Article.prototype.newArticleForm = function ()
      {
         //article.bAdd.comeIn(300);
      };

      Article.prototype.editArticleForm = function ()
      {
         //article.bEdit.comeIn(300);
         //   article.bDelete.comeIn(300);
      };

      Article.prototype.dispose = function ()
      {
         this.bAdd.remove();
         this.bEdit.remove();
         this.bDelete.remove();
      };

      function MadFileBrowser(field_name, url, type, win)
      {
         tinymce.activeEditor.windowManager.open({
            file: "Tools/tinymce/mfm/mfm.php?field=" + field_name + "&url=" + url + "",
            title: 'File Manager',
            width: 840,
            height: 450,
            resizable: "no",
            inline: "yes",
            close_previous: "no"
         }, {
            window: win,
            input: field_name
         });
      }
      var article = new Article();
      /*EW.addURLHandler(function ()
       {
       alert("inja");
       });*/
   </script>
   <?php

   return ob_get_clean();
}

EWCore::register_form("ew-content-form-proerties", "article-properties", ["content" => inputs()]);
echo admin\ContentManagement::create_content_form(["formId" => "article-form",
    "contentType" => "article",
    "script" => script(),
    "data" => $articleInfo]);

