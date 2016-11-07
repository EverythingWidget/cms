/* global EW, System, Scope */

function UIStructureList() {
  var self = this;
  this.currentTopPane;
  this.oldRow;

  System.entity('ui/primary-menu').actions = [{
      title: "tr{New Layout}",
      activity: 'webroot/html/widgets-management/layouts/layout-form/component.php',
//      parent: System.UI.components.mainFloatMenu,
      parameters: {
        uisId: null
      },
      modal: {
        class: "full"
      },
      onDone: function (hash) {
        hash.uisId = null;
      }
    }];
//  this.bNewUIS = EW.addActivity().hide();

  this.importUISActivity = EW.getActivity({
    activity: "webroot/api/widgets-management/import-uis"
  });
  this.exportUISActivity = EW.getActivity({
    activity: "webroot/api/widgets-management/export-uis"
  });

  if (this.importUISActivity) {
    /*var fileInput = $("<input type=file id=uis_file name=uis_file accept='.json'/>");
     var button = $("<div class='btn btn-file btn-primary' >tr{Import Layout}</div>").hide();
     parent: System.UI.components.mainFloatMenu.append(button.append(fileInput));
     button.comeIn();
     
     fileInput.change(function (e) {
     var form = new FormData();
     // HTML file input user's choice...
     form.append("uis_file", fileInput[0].files[0]);
     //EW.lock($("#main-content"));
     if (!fileInput[0].files[0])
     return;
     
     // Make the ajax call
     $.ajax({
     url: 'api/webroot/widgets-management/import-uis',
     type: 'POST',
     dataType: "json",
     success: function (res) {
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
     });*/
  }

  var exportAction = null;
  if (this.exportUISActivity) {
    exportAction = function (row) {
      window.open("api/webroot/widgets-management/export-uis?uis_id=" + row.data("field-id"));
    };
  }

  $(document).off("uis-list.refresh");
  $(document).on("uis-list.refresh", function () {
    self.table.refresh();
  });

  var editActivity;
  this.table = EW.createTable({
    name: "uis-list",
    rowLabel: "{name}",
    columns: [
      "name",
      "template"
    ],
    headers: {
      Name: {},
      Template: {}
    },
    rowCount: true,
    url: "api/webroot/widgets-management/layouts/",
    pageSize: 30,
    onDelete: function (id) {
      this.confirm("Are you sure of deleting this UIS?", function () {
        var _this = this;
        $.post('api/webroot/widgets-management/delete-uis', {
          uisId: id
        }, function (data) {
          EW.setHashParameter("categoryId", null);
          $("body").EW().notify(data).show();
          self.table.removeRow(id);
          _this._messageRow.remove();
          return true;
        }, "json");
      });
    },
    onEdit: ((editActivity = EW.getActivity({
      activity: "webroot/html/widgets-management/layouts/layout-form/component.php_see",
      modal: {
        class: "full"
      },
      onDone: function (hash) {
        hash.uisId = null;
      }
    })) ? function (id) {
      editActivity({
        uisId: id
      });
    } : null),
    buttons: {
      "tr{Clone}": function (row) {
        if (confirm("Are you sure you want to clone UIS:" + row.data("field-name") + "?")) {
          $.get('api/webroot/widgets-management/clone-uis', {
            uisId: row.data("field-id")
          }, function (data) {
            self.table.refresh();
            $("body").EW().notify(data).show();
          }, "json");
        }
      }
      ,
      "tr{Export}": exportAction
    }
  });
  $("#main-content").html(this.table.container);

}

UIStructureList.prototype.selectUIS = function (obj, uisId) {
  var self = this;
  $(self.oldRow).removeClass("selected");
  $(obj).addClass("selected");
  self.oldRow = obj;
};

UIStructureList.prototype.editUIS = function () {
  EW.setHashParameter('cmd', "edit-uis");
};

UIStructureList.prototype.loadNewUISForm = function () {
  var self = this;
  var tp = EW.createModal({
    class: "full",
    onClose: function () {
      EW.setHashParameter("cmd", null);
      self.currentTopPane = null;
    }
  });
  self.currentTopPane = tp;
  EW.lock(tp);

  $.post('html/webroot/widgets-management/ne-uis.php', function (data) {
    tp.html(data);
  });
};

UIStructureList.prototype.loadEditUISForm = function () {
  var self = this;
  // if modal is open do not proceed
  if (self.currentTopPane)
    return;
  //{
  tp = EW.createModal({
    class: "full",
    onClose: function () {
      EW.setHashParameter("cmd", null);
      self.currentTopPane = null;
    }
  });
  self.currentTopPane = tp;

  $.post('html/webroot/widgets-management/ne-uis.php', {
    uisId: EW.getHashParameter("uis-id")
  }, function (data) {
    tp.html(data);
  });
};

UIStructureList.prototype.deleteUIS = function (id) {
  var self = this;
  $.post('api/webroot/widgets-management/delete-uis', {
    uisId: id
  }, function (data) {
    EW.setHashParameter("categoryId", null);
    $("body").EW().notify(data).show();
    self.table.removeRow(id);

    return true;
  });
};


UIStructureList.prototype.listUIStructures = function () {
  if (this.table)
  {
    this.table.refresh();
    return;
  }
};

var uisList;


System.newStateHandler(Scope, function (state) {
  state.onInit = function () { };

  state.onStart = function () {
    uisList = new UIStructureList();
    uisList.table.read();
  };
});
