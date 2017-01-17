<script>
  function UsersComponent(scope, state) {
    var component = this;

    component.scope = scope;
    component.state = state;

    component.handlers = {};

    component.state.onInit = function () {
      component.init();
    };

    component.state.onStart = function () {
      component.start();
    };
  }

  UsersComponent.prototype.defineStates = function (handlers) {
    var component = this;

//    handlers.app = function (full, value) {
//      if (parseInt(value)) {
//        component.editActivity(false, {id: value});
//      }
//    };
  };

  UsersComponent.prototype.init = function () {
    var component = this;
    this.editActivity = EW.getActivity({
      activity: 'admin/html/users-management/user-form/component.php_edit',
      modal: {
        class: 'center'
      },
      onDone: function () {
        component.state.setParam('id', null);
      }
    });

    this.defineStates(this.handlers);
    System.utility.installModuleStateHandlers(this.state, this.handlers);
  };

  UsersComponent.prototype.start = function () {
    var component = this;

    this.table = null;
    //this.bAddUser = EW.addActivity().hide().comeIn(300);

    component.scope.primaryMenu.actions = [
      {
        title: 'tr{New User}',
        parent: System.ui.components.mainFloatMenu,
        modal: {
          class: "center"
        },
        activity: 'admin/html/users-management/user-form/component.php',
        paramters: {
          userId: null
        }
      }
    ];

    component.usersList();
  };

  UsersComponent.prototype.usersList = function () {
    var component = this;

    if (component.table) {
      component.table.refresh();
      return;
    }

    component.table = EW.createTable({
      name: "users-list",
      rowLabel: "{first_name} {last_name}",
      columns: ["email", "first_name", "last_name", "group.title"],
      headers: {
        "tr{Username}": {},
        "tr{First Name}": {},
        "tr{Last Name}": {},
        "tr{Group}": {}
      },
      rowCount: true,
      url: "api/admin/users-management/users/",
      pageSize: 30,
      onDelete: function (id) {
        this.confirm("tr{Are you sure of deleting of this user?}", function () {
          $.ajax({
            type: 'DELETE',
            url: 'api/admin/users-management/users/',
            data: {id: id},
            success: function (response) {
              System.ui.components.document.trigger("users-list.refresh");
              System.ui.components.body.EW().notify(response).show();
            }
          });

          return true;
        });
      },
      onEdit: function (id) {
        component.editActivity({id: id});
      }
    });

    System.ui.components.mainContent.html(this.table.container);
    this.table.read();

    System.ui.components.document.off("users-list.refresh");
    System.ui.components.document.on("users-list.refresh", function () {
      component.table.refresh();
    });
  };

  // ------ Registring the state handler ------ //

  var stateId = 'users-management/users';

  if (Scope._stateId === stateId) {
    System.state(stateId, function (state) {
      Scope.primaryMenu = System.entity('ui/primary-menu');
      new UsersComponent(Scope, state);
    });
  }
</script>
