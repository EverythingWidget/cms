/* global System, EW */

'use strict';

System.entity('state-handlers/widgets-management/pages-uis/all', AllLayoutsStateHandler);

function AllLayoutsStateHandler(state) {
  var handler = this;
  handler.state = state;
  handler.states = {};

  handler.state.onInit = function () {
    handler.init();
  };

  handler.state.onStart = function () {
    handler.start();
  };
}

AllLayoutsStateHandler.prototype.init = function () {
  var handler = this;
  this.allLayoutsList = EW.createTable({
    name: "pages-and-uis-list",
    columns: [
      "path",
      "name"
    ],
    headers: {
      Path: {},
      "Layout Name": {}
    },
    rowCount: true,
    url: "api/webroot/widgets-management/get-all-pages-uis-list/",
    pageSize: 30,
    onDelete: function (id) {
      this.confirm("Are you sure?", function () {
        var row = this;
        $.post("api/webroot/widgets-management/set-uis", {
          path: row.data("field-path")
        }, function (data) {
          $("input[name='" + row.data("field-path") + "']").val("").change();
          $("body").EW().notify(data).show();
          handler.allUISList.removeRow(id);
          row._messageRow.remove();
        });
      });
    }
  });

  Scope.uiViews.main_view.appendChild(this.allLayoutsList.container[0]);
  this.allLayoutsList.refresh();
};

AllLayoutsStateHandler.prototype.start = function () {

};

// ------ Registring the state handler ------ //

System.state('widgets-management/pages-uis/all', function (state) {
  new AllLayoutsStateHandler(state);
});