/* globals $, EW, System, ContentForm */

(function (System) {
  var folderData;
  var bSave = EW.addActivity({
    title: 'tr{Save}',
    defaultClass: 'btn-success',
    verb: 'POST',
    activity: 'admin/api/content-management/contents',
    parameters: function () {
      if (!$('#category-form').EW().validate()) {
        return false;
      }
      var data = ContentForm.getFormData();
      return data;
    },
    onDone: function (response) {
      $('body').EW().notify(response).show();
      $(document).trigger('article-list.refresh');
      $.EW('getParentDialog', $('#category-form')).trigger('close');
    }
  }).hide();

  var bEdit = EW.addActivity({
    title: 'tr{Save Changes}',
    defaultClass: 'btn-success',
    verb: 'PUT',
    activity: 'admin/api/content-management/contents',
    parameters: function () {
      if (!$('#category-form').EW().validate())
      {
        return false;
      }
      var data = ContentForm.getFormData();
      return data;
    },
    onDone: function (data) {
      $('body').EW().notify(data).show();
      $(document).trigger('article-list.refresh');
    }
  }).hide();

  var bDelete = EW.addActivity({
    title: '<i class=\'icon-trash-empty\'></i>',
    defaultClass: 'btn-danger btn-text',
    verb: 'DELETE',
    activity: 'admin/api/content-management/folder',
    parameters: function () {
      if (!confirm('tr{Are you sure of deleting this folder?}')) {
        return false;
      }
      var data = ContentForm.getFormData();
      return {
        id: data.id
      };
    },
    onDone: function (response) {
      $('body').EW().notify(response).show();
      //$(document).trigger("article-list.refresh");
      System.setHashParameters({
        dir: folderData.parent_id + '/list'
      });
      $.EW('getParentDialog', $('#category-form')).trigger('destroy');
    }
  }).hide();

  $('#category-form').on('refresh', function (e, folder) {
    folderData = folder;
    if (folder && folder.id)
    {
      bSave.comeOut(300);
      bEdit.comeIn(300);
      bDelete.comeIn(300);
      $('#category-form #form-title').html('<span>tr{Edit}</span>' + folder.title);
    } else {
      bSave.comeIn(300);
      bEdit.comeOut(300);
      bDelete.comeOut(300);
    }
  });
})(System);
