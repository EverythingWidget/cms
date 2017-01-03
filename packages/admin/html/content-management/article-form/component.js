/* global System, EW */

function Article() {
  var _this = this;
  var dialog = $.EW("getParentDialog", $("#article-form"));
  var loader;
  this.bAdd = EW.addActivity({
    verb: 'POST',
    activity: "admin/api/content-management/contents",
    title: "tr{Save}",
    defaultClass: "btn-success",
    parameters: function () {
      if (!$("#article-form").EW().validate()) {
        return false;
      }

      loader = System.ui.lock({
        element: dialog[0],
        akcent: 'loader center'
      });

      var data = ContentForm.getFormData();
      return data;
    },
    onDone: function (response) {
      System.ui.components.body.EW().notify(response).show();
      ContentForm.setData(response.data);
      $(document).trigger("article-list.refresh");
      loader.dispose();
      System.setHashParameters({
        article: response.data.id
      });
    },
    onFail: function (response) {
      loader.dispose();
    }
  }).hide();

  this.bEditAndClose = EW.addActivity({
    icon: 'icon-check',
    defaultClass: "btn-primary pull-right btn-circle",
    verb: 'PUT',
    activity: 'admin/api/content-management/contents',
    parameters: function () {
      if (!$("#article-form").EW().validate()) {
        return false;
      }

      loader = System.ui.lock({
        element: dialog[0],
        akcent: 'loader center'
      });

      var data = ContentForm.getFormData();
      return data;
    },
    onDone: function (data) {
      System.ui.components.body.EW().notify(data).show();
      dialog.trigger("close");
      $(document).trigger("article-list.refresh");
      loader.dispose(true);
    }}).hide();

  this.bUpdate = EW.addActivity({
    verb: 'PUT',
    activity: 'admin/api/content-management/contents',
    icon: 'icon-save',
    defaultClass: 'btn-success btn-circle',
    parameters: function () {
      if (!$("#article-form").EW().validate()) {
        return false;
      }

      loader = System.ui.lock({
        element: dialog[0],
        akcent: 'loader center'
      });

      var data = ContentForm.getFormData();
      return data;
    },
    onDone: function (response) {
      System.ui.components.body.EW().notify(response).show();
      ContentForm.setData(response.data);
      $(document).trigger("article-list.refresh");
      loader.dispose();
    }}).hide();

  this.bDelete = EW.addActivity({title: "<i class='icon-trash-empty'></i>",
    defaultClass: 'btn-danger btn-text btn-circle',
    verb: 'DELETE',
    activity: "admin/api/content-management/contents",
    parameters: function () {
      if (confirm("tr{Delete this article?}")) {
        return {id: $("#id").val()};
      } else {
        return null;
      }
    },
    onDone: function (response) {
      $.EW("getParentDialog", $("#article-form")).trigger("destroy");
      EW.setHashParameter("articleId", null, "document");
      System.ui.components.body.EW().notify(response).show();
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
