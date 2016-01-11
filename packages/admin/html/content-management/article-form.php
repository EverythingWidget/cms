<?php

session_start();

function get_article_data($id)
{
   $articleInfo = [];
   $articleInfo["parent_id"] = $_REQUEST["parent"];
   if ($_REQUEST["articleId"])
   {
      $articleInfo = EWCore::call_api("admin/api/content-management/get-article", [
                  "articleId" => $id
              ])["data"];
   }

   return json_encode($articleInfo);
}

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
      function Article() {
         var self = this;
         this.bAdd = EW.addActivity({title: "tr{Save}", defaultClass: "btn-success", activity: "admin/api/content-management/add-article",
            postData: function () {
               if (!$("#article-form").EW().validate()) {
                  return false;
               }

               var data = ContentForm.getFormData();
               return data;
            },
            onDone: function (response) {
               System.UI.components.body.EW().notify(response).show();
               EW.setHashParameter("articleId", response.id, "document");
               EW.setHashParameter("articleId", response.id);
               ContentForm.setData(response.data);
               $(document).trigger("article-list.refresh");
            }}).hide();

         this.bEditAndClose = EW.addActivity({title: "tr{Save and Close}",
            defaultClass: "btn-success pull-right",
            activity: "admin/api/content-management/update-article",
            postData: function () {
               if (!$("#article-form").EW().validate()) {
                  return false;
               }

               var data = ContentForm.getFormData();
               return data;
            },
            onDone: function (data) {
               System.UI.components.body.EW().notify(data).show();
               //ContentForm.setData(data.data);
               $.EW("getParentDialog", $("#article-form")).trigger("close");

               $(document).trigger("article-list.refresh");
            }}).hide();

         this.bEdit = EW.addActivity({title: "tr{Save}", defaultClass: "btn-success", activity: "admin/api/content-management/update-article",
            postData: function () {
               if (!$("#article-form").EW().validate()) {
                  return false;
               }

               var data = ContentForm.getFormData();
               return data;
            },
            onDone: function (response) {
               System.UI.components.body.EW().notify(response).show();
               ContentForm.setData(response.data);
               $(document).trigger("article-list.refresh");
            }}).hide();

         this.bDelete = EW.addActivity({title: "tr{Delete}", defaultClass: "btn-danger", activity: "admin/api/content-management/delete-article",
            postData: function () {
               if (confirm("tr{Delete this article?}")) {
                  return {id: $("#id").val()};
               } else {
                  return null;
               }
            },
            onDone: function (data) {
               $.EW("getParentDialog", $("#article-form")).trigger("destroy");
               EW.setHashParameter("articleId", null, "document");
               System.UI.components.body.EW().notify(data).show();
               $(document).trigger("article-list.refresh");
            }}).hide();

         ContentForm.uiForm.on("refresh", function (e, article) {
            if (article && article.id) {
               self.bAdd.comeOut(300);
               self.bEditAndClose.comeIn(300);
               self.bEdit.comeIn(300);
               self.bDelete.comeIn(300);

               ContentForm.uiTitle.html("<span>tr{Edit}</span>" + article.title);
               $("#date_created").val(article.round_date_created);

            } else {
               self.bAdd.comeIn(300);
               self.bEditAndClose.comeOut(300);
               self.bEdit.comeOut(300);
               self.bDelete.comeOut(300);

            }
         });
      }

      var article = new Article();

   </script>
   <?php

   return ob_get_clean();
}

EWCore::register_form("ew-content-form-proerties", "article-properties", ["content" => inputs()]);
echo admin\ContentManagement::create_content_form(["formId" => "article-form",
    "contentType" => "article",
    "script" => script(),
    "data" => get_article_data($_REQUEST["articleId"])]);

