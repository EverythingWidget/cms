/* global System, EW, Scope */

'use restrict';

System.entity('state-handlers/users-management/groups', GroupsComponent);

function GroupsComponent(scope, state) {
  var component = this;

  component.scope = scope;
  component.state = state;

  component.state.onInit = function () {
    component.init();
  };

  component.state.onStart = function () {
    component.start();
  };
}

GroupsComponent.prototype.init = function () {
  var handler = this;

  handler.editGroupActivity = EW.getActivity({
    activity: 'admin/html/users-management/groups/group-form/component.php_edit',
    onDone: function () {
      handler.state.setParam('groupId', null);
    }
  });

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
    url: 'api/admin/users-management/groups/',
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

  Scope.views.users_groups_list.appendChild(handler.table.container[0]);
};

GroupsComponent.prototype.start = function () {
  var component = this;

  component.scope.primaryMenu.actions = [
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

  if (this.table) {
    this.table.refresh();
    return;
  }

  this.table.read();
};

// ------ Registring the state handler ------ //
var stateId = 'users-management/users-groups';

if (Scope._stateId === stateId) {
  System.state(stateId, function (state) {
    Scope.primaryMenu = System.entity('ui/primary-menu');
    new GroupsComponent(Scope, state);
  });
}