/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function AlbumForm() {
  var self = this;
  this.bAdd = EW.addActivity({title: "tr{Save}", defaultClass: "btn-success", activity: "admin/api/content-management/add-album",
    parameters: function () {
      if (!$("#album-form").EW().validate()) {
        return false;
      }
      var data = ContentForm.getFormData();
      //data.content = tinymce.activeEditor.getContent();
      return data;
    },
    onDone: function (data) {
      $.EW("getParentDialog", $("#album-form")).trigger("close");
      $("body").EW().notify(data).show();
      $(document).trigger("media-list.refresh", [data]);
    }
  }).hide();

  this.bEdit = EW.addAction("tr{Save Changes}", this.editAlbum).addClass("btn-success").hide();

  $("#album-form").on('refresh', function (e, album) {
    if (album && album.id) {
      $("#form-title").html("<span>tr{Edit}</span>" + album.title);
      self.bEdit.comeIn();
      self.bAdd.comeOut();
      //bDelete.comeIn(300)
    } else {
      self.bEdit.comeOut();
      self.bAdd.comeIn();
    }
  });
}
var AlbumForm = new AlbumForm();