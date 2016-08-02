/* global System, EW */

'use restrict';

System.entity('state-handlers/users-management/groups', GroupsStateHandler);

function GroupsStateHandler(state) {
  var handler = this;
  handler.state = state;

  handler.state.onInit = function () {
    handler.init();
  };

  handler.state.onStart = function () {
    handler.start();
  };
}

GroupsStateHandler.prototype.init = function () {
  var handler = this;

  $(document).off('users-groups-list.refresh').on('users-groups-list.refresh', function () {
    handler.table.refresh();
  });

  handler.table = EW.createTable({
    name: "users-groups-list",
    rowLabel: "{title}",
    columns: [
      'title',
      'description',
      'date_created'
    ],
    headers: {
      "tr{Title}": {
      },
      "tr{Description}": {
      },
      "tr{Date Created}": {
      }
    },
    rowCount: true,
    url: "api/admin/users-management/groups/",
    pageSize: 30,
    onDelete: function (id) {
      this.confirm("tr{Are you sure of deleting of this group?}", function () {
        $.ajax({
          type: 'DELETE',
          url: 'api/admin/users-management/groups/',
          data: {
            id: id
          },
          success: function (data) {
            handler.usersGroupsList();
            $("body").EW().notify(data).show();
          }
        });

        return true;
      });
    },
    onEdit: function (id) {
      if (handler.editGroupActivity) {
        handler.editGroupActivity({
          groupId: id
        });
      }
    }
  });

  Scope.uiViews.users_groups_list.appendChild(handler.table.container[0]);

};

GroupsStateHandler.prototype.start = function () {
  var handler = this;

  System.entity('ui/primary-actions').actions = [
    {
      title: "tr{New Group}",
      parameters: function () {
        return {
          groupId: null
        };
      },
      activity: 'admin/html/users-management/groups/group-form/component.php'
    }
  ];
//  handler.bNewGroup = EW.addActivity({
//    title: "tr{New Group}",
//    parent: System.UI.components.mainFloatMenu,
//    parameters: function () {
//      return {
//        groupId: null
//      };
//    },
//    activity: 'admin/html/users-management/groups/group-form/component.php'
//  });
//
//  handler.editGroupActivity = EW.getActivity({
//    activity: 'admin/html/users-management/groups/group-form/component.php_edit',
//    onDone: function () {
//      System.setHashParameters({
//        groupId: null
//      });
//    }
//  });

  if (this.table) {
    this.table.refresh();
    return;
  }

  this.table.read();
};

// ------ Registring the state handler ------ //

System.state('users-management/users-groups', function (state) {
  new GroupsStateHandler(state);
});
