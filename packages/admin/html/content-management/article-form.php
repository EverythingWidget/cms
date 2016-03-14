<?php

session_start();

function get_article_data($id) {
  $articleInfo = [];
  $articleInfo["parent_id"] = $_REQUEST["parent"];
  if ($_REQUEST["articleId"]) {
    $articleInfo = EWCore::call_api("admin/api/content-management/get-article", [
                "articleId" => $id
            ])["data"];
  }

  return json_encode($articleInfo);
}

function inputs() {
  ob_start()
  ?>
  <input type="hidden" id="parent_id" name="parent_id" value="">
  <?php

  return ob_get_clean();
}

function script() {
  ob_start()
  ?>
  <script>
    (function (System) {
      function Article() {
        var _this = this;
        var dialog = $.EW("getParentDialog", $("#article-form"));
        var loader;
        this.bAdd = EW.addActivity({title: "tr{Save}", defaultClass: "btn-success", activity: "admin/api/content-management/add-article",
          parameters: function () {
            if (!$("#article-form").EW().validate()) {
              return false;
            }

            loader = System.UI.lock({
              element: dialog[0],
              akcent: 'loader center'
            });

            var data = ContentForm.getFormData();
            return data;
          },
          onDone: function (response) {
            System.UI.components.body.EW().notify(response).show();
            System.setHashParameters({articleId: response.id});
            ContentForm.setData(response.data);
            $(document).trigger("article-list.refresh");
            loader.dispose();
          }}).hide();

        this.bEditAndClose = EW.addActivity({title: "tr{Save and Close}",
          defaultClass: "btn-success pull-right",
          activity: "admin/api/content-management/update-article",
          parameters: function () {
            if (!$("#article-form").EW().validate()) {
              return false;
            }

            loader = System.UI.lock({
              element: dialog[0],
              akcent: 'loader center'
            });

            var data = ContentForm.getFormData();
            return data;
          },
          onDone: function (data) {
            System.UI.components.body.EW().notify(data).show();
            dialog.trigger("close");
            $(document).trigger("article-list.refresh");
            loader.dispose(true);
          }}).hide();

        this.bUpdate = EW.addActivity({title: "tr{Update}", defaultClass: "btn-success", activity: "admin/api/content-management/update-article",
          parameters: function () {
            if (!$("#article-form").EW().validate()) {
              return false;
            }

            loader = System.UI.lock({
              element: dialog[0],
              akcent: 'loader center'
            });

            var data = ContentForm.getFormData();
            return data;
          },
          onDone: function (response) {
            System.UI.components.body.EW().notify(response).show();
            ContentForm.setData(response.data);
            $(document).trigger("article-list.refresh");
            loader.dispose();
          }}).hide();

        this.bDelete = EW.addActivity({title: "tr{Delete}", defaultClass: "btn-danger", activity: "admin/api/content-management/delete-article",
          parameters: function () {
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
            _this.bAdd.comeOut(300);
            _this.bEditAndClose.comeIn(300);
            _this.bUpdate.comeIn(300);
            _this.bDelete.comeIn(300);

            ContentForm.uiTitle.html("<span>tr{Edit}</span>" + article.title);
            $("#date_created").val(article.round_date_created);

          } else {
            _this.bAdd.comeIn(300);
            _this.bEditAndClose.comeOut(300);
            _this.bUpdate.comeOut(300);
            _this.bDelete.comeOut(300);
          }
        });
      }

      var article = new Article();
    })(System);
  </script>
  <?php

  return ob_get_clean();
}

EWCore::register_form("ew/ui/form/content/properties", "article-properties", ["content" => inputs()]);
echo admin\ContentManagement::create_content_form(["formId"      => "article-form",
    "contentType" => "article",
    "script"      => script(),
    "data"        => get_article_data($_REQUEST["articleId"])]);

